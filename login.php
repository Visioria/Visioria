<?php
session_start();

// Usuário e senha corretos
$usuarioCorreto = "admin";
$senhaCorreta = "123456";

$erro = "";

if(isset($_POST['usuario']) && isset($_POST['senha'])){
    if($_POST['usuario'] === $usuarioCorreto && $_POST['senha'] === $senhaCorreta){
        $_SESSION['logado'] = true;
        header("Location: painel.php");
        exit();
    } else {
        $erro = "Usuário ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--OpenGraph Article-->
  <meta property="og:type" content="website">
  <meta property="og:title" content="Dashboard Visioria">
  <meta property="og:description" content="Acesso Restrito.">
  <meta property="og:url" content="https://visioria.pt/">
  <meta property="og:image" content="https://visioria.pt/assets/img/dashboard_og.png">
  <meta property="og:site_name" content="Visioria">
  <meta property="og:locale" content="pt_PT">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/Logo200x200.png" rel="apple-touch-icon">

  <title>Login - Visioria</title>
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
  <style>
    body {
      background: url('assets/img/hero-bg.png') no-repeat center center fixed;
      background-size: cover;
    }
    .login-container {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-card {
      background: rgba(255,255,255,0.95);
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      padding: 40px;
      max-width: 400px;
      width: 100%;
      text-align: center;
    }
    .login-card h2 {
      margin-bottom: 20px;
      font-weight: 600;
    }
    .form-control {
      border-radius: 12px;
    }
    .btn-login {
      background: #007bff;
      color: #fff;
      border-radius: 12px;
      transition: all 0.3s ease;
    }
    .btn-login:hover {
      background: #0056b3;
    }
    .error-message {
      color: red;
      margin-top: 15px;
    }
  </style>
</head>
<body>

<div class="login-container">
  <div class="login-card" data-aos="zoom-in">
    <img src="assets/img/logo.png" alt="Logo" style="max-width:120px; margin-bottom:15px;">
    <h2>Acesso Restrito</h2>
    <form method="post">
      <div class="mb-3">
        <input type="text" name="usuario" class="form-control" placeholder="Usuário" required>
      </div>
      <div class="mb-3">
        <input type="password" name="senha" class="form-control" placeholder="Senha" required>
      </div>
      <button type="submit" class="btn btn-login w-100">Entrar</button>
    </form>
    <?php if($erro): ?>
      <p class="error-message"><i class="bi bi-exclamation-triangle"></i> <?php echo $erro; ?></p>
    <?php endif; ?>
  </div>
</div>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script>
  AOS.init();
</script>
</body>
</html>
