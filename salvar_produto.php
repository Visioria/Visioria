<?php
$jsonFile = "produtos.json";

// Receber dados
$titulo = $_POST['titulo'] ?? '';
$descricaoCurta = $_POST['descricaoCurta'] ?? '';
$descricaoCompleta = $_POST['descricaoCompleta'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$cliente = $_POST['cliente'] ?? '';
$data = $_POST['data'] ?? '';

// Pasta de uploads
$targetDir = "uploads/";
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$imagensArray = [];

if(isset($_FILES['imagem'])){
    $totalFiles = count($_FILES['imagem']['name']);
    for($i = 0; $i < $totalFiles; $i++){
        $fileName = time() . "_" . basename($_FILES['imagem']['name'][$i]);
        $targetFile = $targetDir . $fileName;

        if(move_uploaded_file($_FILES['imagem']['tmp_name'][$i], $targetFile)){
            $imagensArray[] = $targetFile;
        } else {
            echo "Erro ao enviar a imagem $fileName<br>";
        }
    }
}

// Carregar produtos existentes
$produtos = [];
if (file_exists($jsonFile)) {
    $produtos = json_decode(file_get_contents($jsonFile), true);
}

// Adicionar novo produto
$produtos[] = [
    "titulo" => $titulo,
    "descricaoCurta" => $descricaoCurta,
    "descricaoCompleta" => $descricaoCompleta,
    "categoria" => $categoria,
    "cliente" => $cliente,
    "data" => $data,
    "imagens" => $imagensArray
];

// Guardar no JSON
file_put_contents($jsonFile, json_encode($produtos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Redirecionar
header("Location: painel.html?sucesso=1");
exit;
?>
