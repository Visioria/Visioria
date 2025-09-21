<?php
session_start();

// Se não estiver logado, redireciona para login
if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true){
    header("Location: sign-in.php");
    exit();
  }

  $jsonFile = "produtos.json";
  $produtos = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

  $action = $_GET['action'] ?? null;
  $tituloBusca = $_GET['titulo'] ?? null;

// Função salvar JSON
  function salvarJSON($file, $data){
  file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Excluir --------------------------
if($action === "delete" && $tituloBusca){
  foreach($produtos as $key => $p){
    if($p['titulo'] === $tituloBusca){
    
    if(isset($p['imagens'])){
    foreach($p['imagens'] as $img){
      if (is_array($img) && isset($img['url'])) {
        @unlink($img['url']); // formato novo
      } elseif (is_string($img)) {
        @unlink($img); // formato antigo
      }
    }
    }

      unset($produtos[$key]);
      break;
    }
  }
  $produtos = array_values($produtos);
  salvarJSON($jsonFile, $produtos);
  header("Location: tables.php");
  exit;
}

// Atualizar ------------------------
if($action === "update" && $_SERVER['REQUEST_METHOD']==="POST"){
    foreach($produtos as &$p){
        if($p['titulo'] === $_POST['titulo_original']){
            // Atualiza campos principais
            $p['titulo'] = $_POST['titulo'];
            $p['descricaoCurta'] = $_POST['descricaoCurta'];
            $p['descricaoCompleta'] = $_POST['descricaoCompleta'];
            $p['cliente'] = $_POST['cliente'];
            $p['data'] = $_POST['data'];
            $p['categoria'] = $_POST['categoria'];

            // Pega imagens atuais
            $imagens = $p['imagens'] ?? [];

            // ---- Excluir imagens marcadas ----
            if(!empty($_POST['delete'])){
                $idsDelete = $_POST['delete'];
                $imagens = array_filter($imagens, function($img) use ($idsDelete){
                    if(is_array($img) && in_array($img['id'], $idsDelete)){
                        @unlink($img['url']);
                        return false;
                    }
                    return true;
                });
            }

            
            // ---- Substituir imagens ----
            if (!empty($_FILES['replace']['name'])) {
                foreach ($_FILES['replace']['name'] as $id => $name) {
                    if ($name && $_FILES['replace']['error'][$id] === 0) {
                        $tmp_name = $_FILES['replace']['tmp_name'][$id];

                        // 🔹 Remove acentos
                        $name = iconv('UTF-8', 'ASCII//TRANSLIT', $name);

                        // 🔹 Substitui espaços, parênteses e símbolos por "_"
                        $name = preg_replace('/[^A-Za-z0-9.\-_]/', '_', $name);

                        // 🔹 Se já existir um arquivo com o mesmo nome, cria versão (2), (3)...
                        $novo_nome = $name;
                        $caminho = "uploads/" . $novo_nome;
                        $contador = 2;
                        while (file_exists($caminho)) {
                            $path_parts = pathinfo($name);
                            $novo_nome = $path_parts['filename'] . "_(" . $contador . ")." . $path_parts['extension'];
                            $caminho = "uploads/" . $novo_nome;
                            $contador++;
                        }

                        // 🔹 Move o arquivo
                        if (move_uploaded_file($tmp_name, $caminho)) {
                            foreach ($imagens as &$img) {
                                if (is_array($img) && $img['id'] == $id) {
                                    @unlink($img['url']); // remove a antiga
                                    $img['url'] = $caminho; // salva a nova
                                    break;
                                }
                            }
                        }
                    }
                }
            }


            // ---- Reordenação ----
            if(!empty($_POST['ordem'])){
                $novaOrdem = $_POST['ordem'];
                $ordenadas = [];
                foreach($novaOrdem as $id){
                    foreach($imagens as $img){
                        if(is_array($img) && $img['id'] == $id){
                            $ordenadas[] = $img;
                            break;
                        }
                    }
                }
                $imagens = $ordenadas;
            }

            // ---- Adicionar novas imagens ----
            if(!empty($_FILES['nova_imagem']['name'][0])){
                // pegar o maior ID atual
                $maxId = 0;
                foreach($imagens as $img){
                    if(is_array($img) && $img['id'] > $maxId) $maxId = $img['id'];
                }

                foreach($_FILES['nova_imagem']['name'] as $key => $name){
                    if($name && $_FILES['nova_imagem']['error'][$key] === 0){
                        $tmp_name = $_FILES['nova_imagem']['tmp_name'][$key];
                        $novo_nome = $name;
                        $caminho = "uploads/".$novo_nome;
                        move_uploaded_file($tmp_name, $caminho);
                        $imagens[] = [
                            "id" => ++$maxId,
                            "url" => $caminho
                        ];
                    }
                }
            }

            // Atualiza no produto
            $p['imagens'] = array_values($imagens);
        }
    }
    salvarJSON($jsonFile, $produtos);
    header("Location: tables.php");
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
      <a class="navbar-brand px-4 py-3 m-0" href="https://visioria.pt/" target="_blank">
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
          <a class="nav-link active bg-gradient-dark text-white" href="../pages/tables.php">
            <i class="material-symbols-rounded opacity-5">table_view</i>
            <span class="nav-link-text ms-1">Tables</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-dark" href="../pages/upload.php">
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
         
      <!-- Listagem de Produtos -->
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">
                  <?= $action === "edit" ? "Editar Conteúdo" : "Conteúdos Publicados" ?>
                </h6>
              </div>
            </div>
            <div class="card-body px-4 pb-2">
              <?php if($action === "edit" && $tituloBusca): 
                $produto = array_filter($produtos, fn($p)=>$p['titulo']===$tituloBusca);
                $produto = reset($produto);
              ?>
                <form action="tables.php?action=update" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="titulo_original" value="<?= htmlspecialchars($produto['titulo']) ?>">
                  <input type="text" class="form-control border px-2 mb-3" name="titulo" 
                        value="<?= htmlspecialchars($produto['titulo']) ?>" required>
                  <textarea class="form-control border px-2 mb-3" rows="2" 
                            name="descricaoCurta" required><?= htmlspecialchars($produto['descricaoCurta']) ?></textarea>
                  <textarea class="form-control border px-2 mb-3" rows="4" 
                            name="descricaoCompleta" required><?= htmlspecialchars($produto['descricaoCompleta']) ?></textarea>
                  <input type="text" class="form-control border px-2 mb-3" 
                        name="cliente" value="<?= htmlspecialchars($produto['cliente']) ?>" required>
                  <input type="date" class="form-control border px-2 mb-3" 
                        name="data" value="<?= htmlspecialchars($produto['data']) ?>" required>
                  <select class="form-control border px-2 mb-3" name="categoria">
                    <option value="filter-projects" <?= $produto['categoria']=="filter-projects"?"selected":"" ?>>Projetos</option>
                    <option value="filter-construction" <?= $produto['categoria']=="filter-construction"?"selected":"" ?>>Construção</option>
                    <option value="filter-remodeling" <?= $produto['categoria']=="filter-remodeling"?"selected":"" ?>>Remodelação</option>
                  </select>

                  <p>Gerenciar imagens:</p>
                  <div id="galeria" class="d-flex flex-wrap gap-2">
                    <?php foreach($produto['imagens'] as $img): ?>
                      <?php if(is_array($img)): ?>
                        <!-- Formato novo -->
                        <div class="image-item border p-2 rounded" data-id="<?= $img['id'] ?>">
                          <img src="<?= htmlspecialchars($img['url']) ?>" width="120" class="mb-2 d-block">

                          <!-- Substituir imagem -->
                          <input type="file" name="replace[<?= $img['id'] ?>]" class="form-control border px-2">

                          <!-- Excluir imagem -->
                          <div class="form-check mb-2">
                            <input type="checkbox"  name="delete[]" value="<?= $img['id'] ?>" id="del<?= $img['id'] ?>">
                            <label for="del<?= $img['id'] ?>" >Excluir</label>
                          </div>

                          <!-- Ordem -->
                          <input type="hidden" name="ordem[]" value="<?= $img['id'] ?>">
                        </div>
                      <?php else: ?>
                        <!-- Compatibilidade com formato antigo -->
                        <div class="image-item border p-2 rounded">
                          <img src="<?= htmlspecialchars($img) ?>" width="120" class="mb-2 d-block">
                        </div>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </div>

                  <!-- Adicionar novas -->
                  <p class="mt-3">Adicionar novas imagens:</p>
                  <input class="form-control border px-2" type="file" name="nova_imagem[]" multiple>
                  <br>
                  <button class="btn btn-warning">Atualizar</button>
                  <a href="tables.php" class="btn btn-secondary">Voltar</a>
                </form>

              <?php else: ?>
                <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
                <script>
                  new Sortable(document.getElementById('galeria'), {
                    animation: 150,
                    onEnd: function () {
                      // atualiza campos hidden "ordem[]" conforme a posição
                      document.querySelectorAll('#galeria .image-item').forEach((el, i) => {
                        el.querySelector('input[name="ordem[]"]').value = i;
                      });
                    }
                  });
                </script>

                <!-- Tabela de listagem -->
                <div class="table-responsive p-0">
                  <table class="table align-items-center mb-0">
                    <thead>
                      <tr>
                        <th>Imagem</th>
                        <th>Título</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Ações</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach($produtos as $p): ?>
                      <tr>
                        <td>
                          <?php if(!empty($p['imagens'])): ?>
                            <?php if(is_array($p['imagens'][0])): ?>
                              <img src="<?= htmlspecialchars($p['imagens'][0]['url']) ?>" width="100" class="img-thumbnail">
                            <?php else: ?>
                              <img src="<?= htmlspecialchars($p['imagens'][0]) ?>" width="100" class="img-thumbnail">
                            <?php endif; ?>
                          <?php else: ?>
                            <span>Sem imagem</span>
                          <?php endif; ?>

                        </td>
                        <td><?= $p['titulo'] ?></td>
                        <td><?= $p['cliente'] ?></td>
                        <td><?= $p['data'] ?></td>
                        <td>
                          <a class="btn btn-warning btn-sm" href="tables.php?action=edit&titulo=<?= urlencode($p['titulo']) ?>">Editar</a>
                          <a class="btn btn-danger btn-sm" href="tables.php?action=delete&titulo=<?= urlencode($p['titulo']) ?>" onclick="return confirm('Deseja excluir este produto?')">Excluir</a>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
                
              <?php endif; ?>
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