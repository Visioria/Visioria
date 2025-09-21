<?php
// Credenciais do Facebook (Meta for Developers)
$FB_PAGE_ID = "388550107676755"; // ID numérico da pagina "Bom Gajo"
$FB_ACCESS_TOKEN = "EAAPg7wB4eXYBPYusJw4p9aqZCvhpsdhVKnvoa4M7W6pOgyfxWVZBRCjqWqjRQlZCaXZAFtzaJRzW4Ku54RFZCWHjQE60mQhsDOoaQRD9fn177pvyNU7qlONorG8jMlREXnfYyWHfBTjttQy0CONCs2yixSiz6BKEJuGokOZA0azYBOdBcJRSuSkw48VdN7kdldWvgwHTEaRYim1Xvag5f8"; 

/**
 * Gera URL absoluta a partir do caminho salvo no JSON
 */
function absoluteUrl($relativePath) {
    $BASE_URL = "https://visioria.pt/dashboard/pages";
    $relativePath = ltrim($relativePath, "/\\");
    return rtrim($BASE_URL, "/") . "/" . $relativePath;
}

function publicarNoFacebook($titulo, $descricao, $imagensUrls) {
    global $FB_PAGE_ID, $FB_ACCESS_TOKEN;

    $media_ids = [];

    // 1️⃣ Upload das imagens em modo rascunho
    foreach ($imagensUrls as $url) {
        $photoData = [
            "url"          => $url,
            "published"    => "false",
            "access_token" => $FB_ACCESS_TOKEN
        ];

        $ch = curl_init("https://graph.facebook.com/v21.0/$FB_PAGE_ID/photos");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $photoData);
        $res = curl_exec($ch);

        if ($res === false) {
            $err = curl_error($ch);
            curl_close($ch);
            return json_encode([
                "error" => ["message" => "Falha cURL (upload imagem): " . $err]
            ]);
        }

        curl_close($ch);
        $json = json_decode($res, true);

        if (isset($json['error'])) {
            return $res; // já é JSON com erro
        }

        if (isset($json['id'])) {
            $media_ids[] = $json['id'];
        }
    }

    // 2️⃣ Cria a publicação única com todas as imagens
    if (!empty($media_ids)) {
        $attached_media = [];
        foreach ($media_ids as $id) {
            $attached_media[] = ['media_fbid' => $id];
        }

        $postData = [
            "message"        => $titulo . "\n\n" . $descricao,
            "attached_media" => json_encode($attached_media),
            "access_token"   => $FB_ACCESS_TOKEN
        ];

        $ch = curl_init("https://graph.facebook.com/v21.0/$FB_PAGE_ID/feed");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $res = curl_exec($ch);

        if ($res === false) {
            $err = curl_error($ch);
            curl_close($ch);
            return json_encode([
                "error" => ["message" => "Falha cURL (criação post): " . $err]
            ]);
        }

        curl_close($ch);
        return $res;
    }

    // 3️⃣ Nenhuma imagem enviada
    return json_encode([
        "error" => ["message" => "Nenhuma imagem enviada ao Facebook."]
    ]);
}


