<?php
session_start();

// Usuário e senha corretos (apenas exemplo)
$usuarioCorreto = "admin";
$senhaCorreta = "123456";

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if ($usuario === $usuarioCorreto && $senha === $senhaCorreta) {
        $_SESSION['logado'] = true;
        // Recomendo redirect para uma página .php (para poder verificar a sessão)
        header("Location: dashboard.php");
        exit();
    } else {
        $erro = "Usuário ou senha incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
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
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
</head>
<body class="bg-gray-200">
  <main class="main-content mt-0">
    <div class="page-header align-items-start min-vh-100" style="background-image: url('../assets/img/hero-bg.png');">
      <span class="mask bg-gradient-dark opacity-6"></span>
      <div class="container my-auto">
        <div class="row">
          <div class="col-lg-4 col-md-8 col-12 mx-auto">
            <div class="card z-index-0 fadeIn3 fadeInBottom">
              <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1 text-center">
                  <img src="../assets/img/logo.png" alt="Logo Visioria" class="d-block mx-auto mb-2" style="max-width:120px; margin-bottom:15px;">
                  <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Sign in</h4>
                  <div class="row mt-3 justify-content-center">
                    <div class="col-2 text-center">
                      <a class="btn btn-link px-3" href="javascript:;">
                        <i class="fa fa-facebook text-white text-lg"></i>
                      </a>
                    </div>
                    <div class="col-2 text-center">
                      <a class="btn btn-link px-3" href="javascript:;">
                        <i class="fa fa-github text-white text-lg"></i>
                      </a>
                    </div>
                    <div class="col-2 text-center">
                      <a class="btn btn-link px-3" href="javascript:;">
                        <i class="fa fa-google text-white text-lg"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <!-- Mostra erro se existir -->
                <?php if (!empty($erro)): ?>
                  <div class="alert alert-danger text-center" role="alert">
                    <?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
                  </div>
                <?php endif; ?>

                <!-- form submetido por POST -->
                <form role="form" class="text-start" method="post" action="">
                  <div class="input-group input-group-outline my-3">
                    <input type="text" name="usuario" class="form-control" 
                          placeholder="Email"
                          value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>">
                  </div>

                  <div class="input-group input-group-outline mb-3">
                    <input type="password" name="senha" class="form-control" placeholder="Password">
                  </div>

                  <div class="form-check form-switch d-flex align-items-center mb-3">
                    <input class="form-check-input" type="checkbox" id="rememberMe" checked>
                    <label class="form-check-label mb-0 ms-3" for="rememberMe">Remember me</label>
                  </div>
                  <div class="text-center">
                    <!-- botão submit -->
                    <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Sign in</button>
                  </div>
                  
                </form>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <script src="../assets/js/core/bootstrap.min.js"></script>
</body>
</html>
