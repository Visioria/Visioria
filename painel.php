<?php
session_start();

// Se não estiver logado, redireciona para login
if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true){
    header("Location: login.php");
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
          @unlink($img);
        }
      }
      unset($produtos[$key]);
      break;
    }
  }
  $produtos = array_values($produtos);
  salvarJSON($jsonFile, $produtos);
  header("Location: painel.php");
  exit;
}

// Atualizar ------------------------
if($action === "update" && $_SERVER['REQUEST_METHOD']==="POST"){
  foreach($produtos as &$p){
    if($p['titulo'] === $_POST['titulo_original']){
      $p['titulo'] = $_POST['titulo'];
      $p['descricaoCurta'] = $_POST['descricaoCurta'];
      $p['descricaoCompleta'] = $_POST['descricaoCompleta'];
      $p['cliente'] = $_POST['cliente'];
      $p['data'] = $_POST['data'];
      $p['categoria'] = $_POST['categoria'];

      if(!empty($_FILES['imagem']['name'][0])){
        $imagens = [];
        foreach($_FILES['imagem']['name'] as $key => $name){
          $tmp_name = $_FILES['imagem']['tmp_name'][$key];
          $novo_nome = time().'_'.$name;
          $caminho = "uploads/".$novo_nome;
          move_uploaded_file($tmp_name, $caminho);
          $imagens[] = $caminho;
        }
        $p['imagens'] = $imagens;
      }
      break;
    }
  }
  salvarJSON($jsonFile, $produtos);
  header("Location: painel.php");
  exit;
}

// Criar ----------------------------
if($action === "create" && $_SERVER['REQUEST_METHOD']==="POST"){
  $imagens = [];
  
  foreach($_FILES['imagem']['name'] as $key => $name){
    $tmp_name = $_FILES['imagem']['tmp_name'][$key];
    $novo_nome = time().'_'.$name;
    $caminho = "uploads/".$novo_nome;
    move_uploaded_file($tmp_name, $caminho);
    $imagens[] = $caminho;
  }

  $novo = [
    "titulo" => $_POST['titulo'],
    "descricaoCurta" => $_POST['descricaoCurta'],
    "descricaoCompleta" => $_POST['descricaoCompleta'],
    "categoria" => $_POST['categoria'],
    "cliente" => $_POST['cliente'],
    "data" => $_POST['data'],
    "imagens" => $imagens
  ];

  $produtos[] = $novo;
  salvarJSON($jsonFile, $produtos);
  header("Location: painel.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Visioria Contruções - LFS</title>
  <meta name="description" content="Visioria Contrução Civil - LSF">
  <meta name="keywords" content="Visioria Contrução Civil - LSF">
  <meta name="Author" content="Visioria">
  <!--OpenGraph Article-->
  <meta property="og:type" content="website">
  <meta property="og:title" content="Visioria Construção Civil - LSF">
  <meta property="og:description" content="Acesso Restrito.">
  <meta property="og:url" content="https://visioria.pt/">
  <meta property="og:image" content="https://visioria.pt/assets/img/Logo200x200.png">
  <meta property="og:site_name" content="Visioria">
  <meta property="og:locale" content="pt_PT">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  </head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="index.html" class="logo d-flex align-items-center">
        <!-- Uncomment the line below if you also wish to use an title logo -->
        <!-- <h1 class="sitename">Visioria</h1> -->

        <img src="assets/img/logo.png" alt="">
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="#edit">Editor</a></li>
          <li><a href="#upload">Dashboard</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <main class="main">

    <!-- Upload Section -->
    <section id="upload" class="contact section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span class="description-title">Dashboard</span>
        <h2>Dashboard</h2>
        <p>Gestor de Conteúdo</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">
          <h1>Adicionar Conteúdo</h1>
          <br>
          <!-- Form Adicionar -->
          
            <form action="painel.php?action=create" method="post" enctype="multipart/form-data" class="mb-4">
              <input class="form-control mb-2" name="titulo" placeholder="Título" required>
              <textarea class="form-control mb-2" name="descricaoCurta" placeholder="Descrição Curta" required></textarea>
              <textarea class="form-control mb-2" name="descricaoCompleta" placeholder="Descrição Completa" required></textarea>
              <input class="form-control mb-2" name="cliente" placeholder="Cliente" required>
              <input class="form-control mb-2" type="date" name="data" required>
              <select class="form-control mb-2" name="categoria">
              <option value="filter-projects">Projetos</option>
              <option value="filter-construction">Construção</option>
              <option value="filter-remodeling">Remodelação</option>
              </select>
              <input class="form-control mb-2" type="file" name="imagem[]" multiple required>
              <button class="btn btn-success">Salvar</button>
            </form>
        </div>

    </section><!-- /Contact Section -->

    <!-- Delet and Edit Section -->
    <section id="edit" class="contact section">
      <div class="container" data-aos="fade-up" data-aos-delay="100">   
        <?php if($action === "edit" && $tituloBusca): 
          $produto = array_filter($produtos, fn($p)=>$p['titulo']===$tituloBusca);
          $produto = reset($produto);
          ?>
          <!-- Form Editar -->
          <h2>Editar Produto</h2>
            <form action="painel.php?action=update" method="post" enctype="multipart/form-data" class="mb-4">
              <input type="hidden" name="titulo_original" value="<?= $produto['titulo'] ?>">
              <input class="form-control mb-2" name="titulo" value="<?= $produto['titulo'] ?>" required>
              <textarea class="form-control mb-2" name="descricaoCurta" required><?= $produto['descricaoCurta'] ?></textarea>
              <textarea class="form-control mb-2" name="descricaoCompleta" required><?= $produto['descricaoCompleta'] ?></textarea>
              <input class="form-control mb-2" name="cliente" value="<?= $produto['cliente'] ?>" required>
              <input class="form-control mb-2" type="date" name="data" value="<?= $produto['data'] ?>" required>
              <select class="form-control mb-2" name="categoria">
              <option value="filter-projects" <?= $produto['categoria']=="filter-projects"?"selected":"" ?>>Projetos</option>
              <option value="filter-construction" <?= $produto['categoria']=="filter-construction"?"selected":"" ?>>Construção</option>
              <option value="filter-remodeling" <?= $produto['categoria']=="filter-remodeling"?"selected":"" ?>>Remodelação</option>
              </select>
              <p>Imagens atuais:</p>
              <?php if(!empty($produto['imagens'])): ?>
              <?php foreach($produto['imagens'] as $img): ?>
              <img src="<?= $img ?>" width="100" class="me-2 mb-2">
              <?php endforeach; ?>
              <?php endif; ?>
              <input class="form-control mb-2" type="file" name="imagem[]" multiple>
              <button class="btn btn-warning">Atualizar</button>
              </form>
            <a href="painel.php" class="btn btn-secondary">Voltar</a>
          <?php else: ?>         
        <h2>Editar Públicados</h2>
        <br>
          <table class="table table-bordered">
            <tr>
            <th>Imagem</th>
            <th>Título</th>
            <th>Cliente</th>
            <th>Data</th>
            <th>Ações</th>
            </tr>
            <?php foreach($produtos as $p): ?>
            <tr>
            <td>
            <?php if(!empty($p['imagens'])): ?>
            <img src="<?= $p['imagens'][0] ?>" width="100" class="img-thumbnail">
            <?php else: ?>
            <span>Sem imagem</span>
            <?php endif; ?>
            </td>
            <td><?= $p['titulo'] ?></td>
            <td><?= $p['cliente'] ?></td>
            <td><?= $p['data'] ?></td>
            <td>
            <a class="btn btn-warning btn-sm" href="painel.php?action=edit&titulo=<?= urlencode($p['titulo']) ?>">Editar</a>
            <a class="btn btn-danger btn-sm" href="painel.php?action=delete&titulo=<?= urlencode($p['titulo']) ?>" 
            onclick="return confirm('Deseja excluir este produto?')">Excluir</a>
            </td>
            </tr>
            <?php endforeach; ?>
          </table>
        <?php endif; ?>
        <br>
        <a class="btn btn-success" href="logout.php">Sair</a>
      </div>
    </section><!-- /Delet and Edit Section  -->

  </main>

  <footer id="footer" class="footer dark-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.html" class="logo d-flex align-items-center">
            <span class="sitename">Plato</span>
          </a>
          <div class="footer-contact pt-3">
            <p>A108 Adam Street</p>
            <p>New York, NY 535022</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+1 5589 55488 55</span></p>
            <p><strong>Email:</strong> <span>info@example.com</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">About us</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Terms of service</a></li>
            <li><a href="#">Privacy policy</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><a href="#">Web Design</a></li>
            <li><a href="#">Web Development</a></li>
            <li><a href="#">Product Management</a></li>
            <li><a href="#">Marketing</a></li>
            <li><a href="#">Graphic Design</a></li>
          </ul>
        </div>

        <div class="col-lg-4 col-md-12 footer-newsletter">
          <h4>Our Newsletter</h4>
          <p>Subscribe to our newsletter and receive the latest news about our products and services!</p>
          <form action="forms/newsletter.php" method="post" class="php-email-form">
            <div class="newsletter-form"><input type="email" name="email"><input type="submit" value="Subscribe"></div>
            <div class="loading">Loading</div>
            <div class="error-message"></div>
            <div class="sent-message">Your subscription request has been sent. Thank you!</div>
          </form>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Plato</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>