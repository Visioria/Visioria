<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Verifica login
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    header("Location: sign-in.php");
    exit();
}

// Paths
$jsonFile    = __DIR__ . "/produtos.json";
$UPLOADS_ROOT = __DIR__ . "/uploads";

// Garante pasta uploads
if (!is_dir($UPLOADS_ROOT)) {
    mkdir($UPLOADS_ROOT, 0755, true);
}

/**
 * Salva array em JSON formatado
 */
function salvarJSON($file, $data) {
    file_put_contents(
        $file,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    );
}

/**
 * Retorna o maior id de imagem dentro de um produto
 */
function findMaxImageIdInProduct($produto) {
    $max = 0;
    if (!isset($produto['imagens']) || !is_array($produto['imagens'])) return 0;
    foreach ($produto['imagens'] as $img) {
        if (isset($img['id']) && $img['id'] > $max) {
            $max = $img['id'];
        }
    }
    return $max;
}

/**
 * Processa uploads e retorna array de imagens
 * - $startId: define por qual id começa (1 para novo produto, ou último+1 para edição)
 */
function processarUploads($files, $startId = 1) {
    global $UPLOADS_ROOT;

    $imagens = [];
    $idCounter = $startId;

    if (!isset($files['name']) || !is_array($files['name'])) {
        return $imagens;
    }

    foreach ($files['name'] as $key => $name) {
        if (!isset($files['error'][$key]) || $files['error'][$key] !== UPLOAD_ERR_OK) continue;

        $tmp_name = $files['tmp_name'][$key];

        // 🔹 Remove acentos
        $nameSafe = iconv('UTF-8', 'ASCII//TRANSLIT', $name);

        // 🔹 Substitui espaços e símbolos
        $nameSafe = preg_replace('/[^A-Za-z0-9.\-_]/', '_', $nameSafe);

        // 🔹 Garante nome único
        $novo_nome = $nameSafe;
        $destino = $UPLOADS_ROOT . "/" . $novo_nome;
        $contador = 2;
        while (file_exists($destino)) {
            $path_parts = pathinfo($nameSafe);
            $novo_nome = $path_parts['filename'] . "_(" . $contador . ")." . $path_parts['extension'];
            $destino = $UPLOADS_ROOT . "/" . $novo_nome;
            $contador++;
        }

        if (move_uploaded_file($tmp_name, $destino)) {
            $relative = "uploads/" . $novo_nome;
            $imagens[] = [
                "id"  => $idCounter,
                "url" => $relative
            ];
            $idCounter++;
        }
    }

    return $imagens;
}

/**
 * Quando enviar formulário
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // carrega produtos existentes
    $produtos = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
    if (!is_array($produtos)) $produtos = [];

    // Detecta se é edição
    $isEdit = false;
    $productId = $_POST['productId'] ?? null;

    if ($productId) {
        // procura produto existente
        $foundIndex = null;
        foreach ($produtos as $i => $p) {
            if (isset($p['id']) && $p['id'] === $productId) {
                $foundIndex = $i;
                break;
            }
        }
        if ($foundIndex !== null) {
            $isEdit = true;
            $existingProduct = $produtos[$foundIndex];
            $startId = findMaxImageIdInProduct($existingProduct) + 1;
        } else {
            $productId = null;
            $isEdit = false;
            $startId = 1;
        }
    } else {
        $startId = 1;
    }

    // se for novo produto, gera ID
    if (!$productId) {
        $productId = uniqid("p_", true);
    }

    // processa uploads direto na pasta uploads/
    $imagensNovas = processarUploads($_FILES['imagem'], $startId);

    if ($isEdit) {
        // edição -> atualiza e anexa imagens
        $produtos[$foundIndex]['titulo'] = $_POST['titulo'] ?? $produtos[$foundIndex]['titulo'];
        $produtos[$foundIndex]['descricaoCurta'] = $_POST['descricaoCurta'] ?? $produtos[$foundIndex]['descricaoCurta'];
        $produtos[$foundIndex]['descricaoCompleta'] = $_POST['descricaoCompleta'] ?? $produtos[$foundIndex]['descricaoCompleta'];
        $produtos[$foundIndex]['categoria'] = $_POST['categoria'] ?? $produtos[$foundIndex]['categoria'];
        $produtos[$foundIndex]['cliente'] = $_POST['cliente'] ?? $produtos[$foundIndex]['cliente'];
        $produtos[$foundIndex]['data'] = $_POST['data'] ?? $produtos[$foundIndex]['data'];

        if (!isset($produtos[$foundIndex]['imagens']) || !is_array($produtos[$foundIndex]['imagens'])) {
            $produtos[$foundIndex]['imagens'] = [];
        }
        $produtos[$foundIndex]['imagens'] = array_merge($produtos[$foundIndex]['imagens'], $imagensNovas);
    } else {
        // novo produto
        $novo = [
            "id"                => $productId,
            "titulo"            => $_POST['titulo'] ?? '',
            "descricaoCurta"    => $_POST['descricaoCurta'] ?? '',
            "descricaoCompleta" => $_POST['descricaoCompleta'] ?? '',
            "categoria"         => $_POST['categoria'] ?? '',
            "cliente"           => $_POST['cliente'] ?? '',
            "data"              => $_POST['data'] ?? '',
            "imagens"           => $imagensNovas
        ];
        $produtos[] = $novo;
    }

    salvarJSON($jsonFile, $produtos);

    // 🚀 Integração com Facebook
require_once __DIR__ . "/facebook.php";  // onde estão $FB_PAGE_ID e $FB_ACCESS_TOKEN


// Último produto salvo (novo ou editado)
$produtoAtual = $isEdit ? $produtos[$foundIndex] : $novo;

// Extrair URLs absolutas das imagens
$imagensUrls = [];
foreach ($imagensNovas as $img) {
    $imagensUrls[] = absoluteUrl($img['url']); // $img['url'] já vem como "uploads/xxx.jpg"
}

// Só publica se tiver imagens novas
$fbMensagem = "";
$fbErro = "";

if (!empty($imagensUrls)) {
    $res = publicarNoFacebook(
        $produtoAtual['titulo'],
        $produtoAtual['descricaoCurta'],
        $imagensUrls
    );

    $json = json_decode($res, true);

    if (isset($json['error'])) {
        // Houve erro na publicação
        $fbErro = "❌ Erro ao compartilhar!" . htmlspecialchars($json['error']['message']);
    } else {
        // Sucesso
        $fbMensagem = "📩 Publicação realizada com sucesso!";
    }
}


// Monta mensagem final
$mensagemFinal = "<strong>Facebook Post:</strong>";

if ($fbMensagem) {
    $mensagemFinal .= "<br>" . $fbMensagem;
}

if ($fbErro) {
    $mensagemFinal .= "<br><span style='color:red; font-weight:bold;'>" . $fbErro . "</span>";
}



// Renderiza página de confirmação
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <title>Resultado do Upload</title>
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Sharp" />

</head>
<body class="bg-gray-100 d-flex align-items-center justify-content-center vh-100">

    <div class="card my-4">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
            <h6 class="text-white text-capitalize ps-3">✅ Operação concluída</h6>
            </div>
        </div>
        <div class="card-body px-4 pb-2">
            <div class="alert bg-gradient-info text-white">
            <?= $mensagemFinal ?>
            </div>

            <?php if (!empty($imagensNovas)): ?>
            <div class="mb-3">
                <h6 class="mb-2">Imagens enviadas:</h6>
                <div class="d-flex flex-wrap gap-2">
                <?php foreach ($imagensNovas as $img): ?>
                    <img src="<?= htmlspecialchars($img['url']) ?>" 
                        alt="Preview" 
                        style="max-width:120px; border-radius:12px; box-shadow:0 0 8px rgba(0,0,0,0.2);">
                <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="d-flex justify-content-center gap-3 mt-4">
                <a href="upload.php" class="btn bg-gradient-success d-flex align-items-center gap-2">
                    <i class="material-symbols-rounded">add_circle</i>
                    Continuar publicando
                </a>
                <a href="tables.php" class="btn bg-gradient-dark d-flex align-items-center gap-2">
                    <i class="material-symbols-rounded">table_view</i>
                    Ver todas as publicações
                </a>
            </div>

        </div>
    </div>


</body>
</html>
<?php
exit;

}
?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Visioria Contrução Civil - LSF</title>
  <meta name="description" content="Visioria Contrução Civil - LSF">
  <meta name="keywords" content="Visioria Contrução Civil - LSF">
  <meta name="Author" content="Visioria">
  <!--OpenGraph Article-->
  <meta property="og:type" content="website">
  <meta property="og:title" content="Dashboard Visioria">
  <meta property="og:description" content="Acesso Restrito.">
  <meta property="og:url" content="https://visioria.pt/">
  <meta property="og:image" content="https://visioria.pt/assets/img/dashboard_og.png">
  <meta property="og:site_name" content="Visioria">
  <meta property="og:locale" content="pt_PT">

  <!-- Favicons -->
  <link href="https://visioria.pt/dashboard/assets/img/favicon.png" rel="icon">
  <link href="https://visioria.pt/dashboard/assets/img/apple-touch-icon.png" rel="apple-touch-icon"> 
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
</head>

<body class="g-sidenav-show  bg-gray-100">
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand px-4 py-3 m-0" href=" https://demos.creative-tim.com/material-dashboard/pages/dashboard " target="_blank">
        <img src="../assets/img/logo.png" class="navbar-brand-img" width="75" height="25" alt="main_logo">
        <span class="ms-1 text-sm text-dark">Gerenciador</span>
      </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/dashboard.php">
            <i class="material-symbols-rounded opacity-5">dashboard</i>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/tables.php">
            <i class="material-symbols-rounded opacity-5">table_view</i>
            <span class="nav-link-text ms-1">Tables</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link active bg-gradient-dark text-white" href="../pages/upload.php">
            <i class="material-symbols-rounded opacity-5">upload</i>
            <span class="nav-link-text ms-1">Upload</span>
          </a>
        </li>
        <!--
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-5">Account pages</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/profile.html">
            <i class="material-symbols-rounded opacity-5">person</i>
            <span class="nav-link-text ms-1">Profile</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/sign-in.html">
            <i class="material-symbols-rounded opacity-5">login</i>
            <span class="nav-link-text ms-1">Sign In</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/sign-up.html">
            <i class="material-symbols-rounded opacity-5">assignment</i>
            <span class="nav-link-text ms-1">Sign Up</span>
          </a>
        </li>
        -->
      </ul>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0 ">
      <div class="mx-3">
        <a class="btn bg-gradient-dark w-100" href="sign-out.php" type="button">Sign out</a>
      </div>
    </div>
  </aside>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Tables</li>
          </ol>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
            
          </div>
          <ul class="navbar-nav d-flex align-items-center  justify-content-end">
            
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                </div>
              </a>
            </li>
            <li class="nav-item px-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0">
                <i class="material-symbols-rounded fixed-plugin-button-nav">settings</i>
              </a>
            </li>
            
            <li class="nav-item d-flex align-items-center">
              <a href="../pages/sign-in.html" class="nav-link text-body font-weight-bold px-0">
                <i class="material-symbols-rounded">account_circle</i>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
    
    <div class="container-fluid py-2">
      
      <!-- Adicionar Conteúdo -->
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Adicionar Conteúdo</h6>
              </div>
            </div>
            <div class="card-body px-4 pb-2">
              <form method="post" enctype="multipart/form-data" class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Título</label>
    <input type="text" name="titulo" class="form-control border px-2" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">Cliente</label>
    <input type="text" name="cliente" class="form-control border px-2" required>
  </div>

  <div class="col-12">
    <label class="form-label">Descrição Curta</label>
    <textarea name="descricaoCurta" rows="2" class="form-control border px-2" required></textarea>
  </div>

  <div class="col-12">
    <label class="form-label">Descrição Completa</label>
    <textarea name="descricaoCompleta" rows="4" class="form-control border px-2"></textarea>
  </div>

  <div class="col-md-6">
    <label class="form-label">Data</label>
    <input type="date" name="data" class="form-control border px-2" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">Categoria</label>
    <select name="categoria" class="form-control border px-2">
      <option value="filter-projects">Projetos</option>
      <option value="filter-construction">Construção</option>
      <option value="filter-remodeling">Remodelação</option>
    </select>
  </div>

  <div class="col-12">
    <label class="form-label">Imagens</label>
    <input type="file" name="imagem[]" multiple class="form-control border px-2">
  </div>

  <div class="col-12 mt-3">
    <button type="submit" class="btn btn-success">Salvar no JSON</button>
  </div>
</form>

            </div>
          </div>
        </div>
      </div>  
      
    

      <footer class="footer py-4  ">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted text-lg-start">
                ©Copyright 2018 <script>
                  document.write(new Date().getFullYear())
                </script>, <i class="fa fa-heart"></i> by
                <a href="https://www.visioria.pt" class="font-weight-bold" target="_blank">Visioria</a>
                | All Rights Reserved
              </div>
            </div>
            
          </div>
        </div>
      </footer>
    </div>
  </main>
  <div class="fixed-plugin">
    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
      <i class="material-symbols-rounded py-2">settings</i>
    </a>
    <div class="card shadow-lg">
      <div class="card-header pb-0 pt-3">
        <div class="float-start">
          <h5 class="mt-3 mb-0">Material UI Configurator</h5>
          <p>See our dashboard options.</p>
        </div>
        <div class="float-end mt-4">
          <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
            <i class="material-symbols-rounded">clear</i>
          </button>
        </div>
        <!-- End Toggle Button -->
      </div>
      <hr class="horizontal dark my-1">
      <div class="card-body pt-sm-3 pt-0">
        <!-- Sidebar Backgrounds -->
        <div>
          <h6 class="mb-0">Sidebar Colors</h6>
        </div>
        <a href="javascript:void(0)" class="switch-trigger background-color">
          <div class="badge-colors my-2 text-start">
            <span class="badge filter bg-gradient-primary" data-color="primary" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-dark active" data-color="dark" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
          </div>
        </a>
        <!-- Sidenav Type -->
        <div class="mt-3">
          <h6 class="mb-0">Sidenav Type</h6>
          <p class="text-sm">Choose between different sidenav types.</p>
        </div>
        <div class="d-flex">
          <button class="btn bg-gradient-dark px-3 mb-2" data-class="bg-gradient-dark" onclick="sidebarType(this)">Dark</button>
          <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent" onclick="sidebarType(this)">Transparent</button>
          <button class="btn bg-gradient-dark px-3 mb-2  active ms-2" data-class="bg-white" onclick="sidebarType(this)">White</button>
        </div>
        <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
        <!-- Navbar Fixed -->
        <div class="mt-3 d-flex">
          <h6 class="mb-0">Navbar Fixed</h6>
          <div class="form-check form-switch ps-0 ms-auto my-auto">
            <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
          </div>
        </div>
        <hr class="horizontal dark my-3">
        <div class="mt-2 d-flex">
          <h6 class="mb-0">Light / Dark</h6>
          <div class="form-check form-switch ps-0 ms-auto my-auto">
            <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version" onclick="darkMode(this)">
          </div>
        </div>
        <hr class="horizontal dark my-sm-4">
        <a class="btn bg-gradient-info w-100" href="https://www.creative-tim.com/product/material-dashboard-pro">Free Download</a>
        <a class="btn btn-outline-dark w-100" href="https://www.creative-tim.com/learning-lab/bootstrap/overview/material-dashboard">View documentation</a>
        <div class="w-100 text-center">
          <a class="github-button" href="https://github.com/creativetimofficial/material-dashboard" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star creativetimofficial/material-dashboard on GitHub">Star</a>
          <h6 class="mt-3">Thank you for sharing!</h6>
          <a href="https://twitter.com/intent/tweet?text=Check%20Material%20UI%20Dashboard%20made%20by%20%40CreativeTim%20%23webdesign%20%23dashboard%20%23bootstrap5&amp;url=https%3A%2F%2Fwww.creative-tim.com%2Fproduct%2Fsoft-ui-dashboard" class="btn btn-dark mb-0 me-2" target="_blank">
            <i class="fab fa-twitter me-1" aria-hidden="true"></i> Tweet
          </a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.creative-tim.com/product/material-dashboard" class="btn btn-dark mb-0 me-2" target="_blank">
            <i class="fab fa-facebook-square me-1" aria-hidden="true"></i> Share
          </a>
        </div>
      </div>
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>
    <!-- Ativar drag-and-drop só no editor -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
  new Sortable(document.getElementById('galeria'), {
    animation: 150,
    onEnd: function () {
      document.querySelectorAll('#galeria .image-item').forEach((el, i) => {
        el.querySelector('input[name="ordem[]"]').value = el.dataset.id;
      });
    }
  });
</script>
</body>

</html>