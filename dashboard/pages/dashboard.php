<?php
session_start();

// Se não estiver logado, redireciona para login
if(!isset($_SESSION['logado']) || $_SESSION['logado'] !== true){
    header("Location: sign-in.php");
    exit();
}
?>
<!--
* Projeto: 
   Site institucional moderno.
   Painel administrativo funcional.
   Gestão de produtos/portfólio.
* URL: https://visioria.pt/
* Implementação: Desenvolvido e personalizado por Maxwell Clemente
* Atualizado: Ago 2025
* Empresa: Visioria - Construção Civil e LSF
* Nota: Este código foi implementado exclusivamente para a Visioria.
-->
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
  <!-- Menu Lateral -->  
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
          <a class="nav-link active bg-gradient-dark text-white" href="../pages/dashboard.php">
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
    <!--Menu Superior-->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
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
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Menu Superior -->

    <!-- cards sem dados -->
    <div class="container-fluid py-2"> 
              <!-- Mês/Ano -->
   
      <div class="row">
        <div class="ms-3">
        <img src="../assets/img/logo.png" class="navbar-brand-img" width="110" height="35" alt="main_logo">
        
        <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
          <p class="mb-4">
            Principais métricas de desempenho do site.
          </p>
        </div>                
        <p>Última atualização: <span id="lastUpdate">--</span></p>
        <!--Selecionar Periodo-->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-2 ps-3">
              <div class="d-flex justify-content-between">
                <div>
                  <p class="text-sm mb-0 text-capitalize">Selecionar Período</p>
                </div>
                 <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                  <i class="material-symbols-rounded opacity-10">leaderboard</i>
                </div>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-body p-3">
              <form class="row g-3">
                <div class="col-md-6">
                  <label for="selectYear" class="form-label text-sm">Ano</label>
                  <select id="selectYear" class="form-select">
                    <option value="2025" selected>2025</option>
                    <option value="2024">2024</option>
                    <option value="2023">2023</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="selectMonth" class="form-label text-sm">Mês</label>
                  <select id="selectMonth" class="form-select">
                    <option value="1">Janeiro</option>
                    <option value="2">Fevereiro</option>
                    <option value="3">Março</option>
                    <option value="4">Abril</option>
                    <option value="5">Maio</option>
                    <option value="6">Junho</option>
                    <option value="7">Julho</option>
                    <option value="8">Agosto</option>
                    <option value="9">Setembro</option>
                    <option value="10">Outubro</option>
                    <option value="11">Novembro</option>
                    <option value="12">Dezembro</option>
                  </select>
                </div>
                <div class="col-12">
                  <button id="btnLoadData" type="button" class="btn bg-gradient-dark w-100">
                    Confirmar
                  </button>
                </div>
              </form>
            </div>
            
          </div>
        </div>  
        <!--Visitantes Unicos-->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-2 ps-3">
              <div class="d-flex justify-content-between">
                <div>
                  <p class="text-sm mb-0 text-capitalize">Visitantes Unicos</p>
                  <h4 class="mb-0" id="uniqueVisitors">0</h4>
                </div>
                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                  <i class="material-symbols-rounded opacity-10">person</i>
                </div>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-2 ps-3">
              <p class="mb-0 text-sm"><span class="text-success font-weight-bolder">+55% </span>than last week</p>
            </div>
          </div>
        </div>
        <!--Numero de Visitas-->
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-2 ps-3">
              <div class="d-flex justify-content-between">
                <div>
                  <p class="text-sm mb-0 text-capitalize">Numero de Visitas</p>
                  <h4 class="mb-0" id="totalVisits">0</h4>
                </div>
                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                  <i class="material-symbols-rounded opacity-10">weekend</i>
                </div>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-2 ps-3">
              <p class="mb-0 text-sm"><span class="text-success font-weight-bolder">+3% </span>than last month</p>
            </div>
          </div>
        </div>        
      </div>
      
      <!--Grafics-->
      <div class="row">
        <!--Dias da semana-->
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-0 ">Dias da semana</h6>
              <p class="text-sm ">Last Campaign Performance</p>
              <div class="pe-2">
                <div class="chart">
                  <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-symbols-rounded text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm"> campaign sent 2 days ago </p>
              </div>
            </div>
          </div>
        </div>
        <!--Histórico mensal-->
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
          <div class="card ">
            <div class="card-body">
              <h6 class="mb-0 ">Histórico mensal</h6>
              <p class="text-sm "> (<span class="font-weight-bolder">+15%</span>) increase in today sales. </p>
              <div class="pe-2">
                <div class="chart">
                  <canvas id="chart-line" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-symbols-rounded text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm"> updated 4 min ago </p>
              </div>
            </div>
          </div>
        </div>
        <!--Dias do mês-->        
        <div class="col-lg-4 mt-4 mb-3">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-0 ">Dias do mês</h6>
              <p class="text-sm ">Last Campaign Performance</p>
              <div class="pe-2">
                <div class="chart">
                  <canvas id="chart-line-tasks" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-symbols-rounded text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm">just updated</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      
      <div class="row mb-4">
      <!--Projects-->
        <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
          <div class="card">
            <div class="card-header pb-0">
              <div class="row">
                <div class="col-lg-6 col-7">
                  <h6>Acessos por Região</h6>
                  <p class="text-sm mb-0">
                    <i class="fa fa-check text-info" aria-hidden="true"></i>
                    <span class="font-weight-bold ms-1">Atualizado</span> via AWStats
                  </p>
                </div>
                <div class="col-lg-6 col-5 my-auto text-end">
                  <!-- seletor de período -->
                  <input type="month" id="periodSelector" class="form-control form-control-sm d-inline-block w-auto">
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Região</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acessos</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Percentual</th>
                    </tr>
                  </thead>
                  <tbody id="region-access-table">
                    <!-- JS irá preencher -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <!-- Devices Card -->
<div class="col-lg-4 col-md-6">
  <div class="card h-100">
    <div class="card-header pb-0">
      <h6>Dispositivos</h6>
      <p class="text-sm">
        <i class="fa fa-arrow-up text-success" aria-hidden="true"></i>
        <span id="devicesGrowth" class="font-weight-bold">--</span> este mês
      </p>
    </div>
    <div class="card-body p-3">
      <div class="timeline timeline-one-side">

        <div class="timeline-block mb-3">
          <span class="timeline-step">
            <i class="material-symbols-rounded text-primary text-gradient">computer</i>
          </span>
          <div class="timeline-content">
            <h6 class="text-dark text-sm font-weight-bold mb-0">Desktop</h6>
            <p id="desktopValue" class="text-secondary font-weight-bold text-xs mt-1 mb-0">--</p>
          </div>
        </div>

        <div class="timeline-block mb-3">
          <span class="timeline-step">
            <i class="material-symbols-rounded text-success text-gradient">smartphone</i>
          </span>
          <div class="timeline-content">
            <h6 class="text-dark text-sm font-weight-bold mb-0">Mobile</h6>
            <p id="mobileValue" class="text-secondary font-weight-bold text-xs mt-1 mb-0">--</p>
          </div>
        </div>

        <div class="timeline-block mb-3">
          <span class="timeline-step">
            <i class="material-symbols-rounded text-warning text-gradient">tablet_mac</i>
          </span>
          <div class="timeline-content">
            <h6 class="text-dark text-sm font-weight-bold mb-0">Tablet</h6>
            <p id="tabletValue" class="text-secondary font-weight-bold text-xs mt-1 mb-0">--</p>
          </div>
        </div>

        <div class="timeline-block">
          <span class="timeline-step">
            <i class="material-symbols-rounded text-dark text-gradient">devices_other</i>
          </span>
          <div class="timeline-content">
            <h6 class="text-dark text-sm font-weight-bold mb-0">Outros</h6>
            <p id="otherValue" class="text-secondary font-weight-bold text-xs mt-1 mb-0">--</p>
          </div>
        </div>

      </div>
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
          <h5 class="mt-3 mb-0">Color Configurator</h5>
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
        
      </div>
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/chartjs.min.js"></script>
  
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
  <script>
    var ctx = document.getElementById("chart-bars").getContext("2d");

    new Chart(ctx, {
      type: "bar",
      data: {
        labels: ["M", "T", "W", "T", "F", "S", "S"],
        datasets: [{
          label: "Views",
          tension: 0.4,
          borderWidth: 0,
          borderRadius: 4,
          borderSkipped: false,
          backgroundColor: "#43A047",
          data: [50, 45, 22, 28, 50, 60, 76],
          barThickness: 'flex'
        }, ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: '#e5e5e5'
            },
            ticks: {
              suggestedMin: 0,
              suggestedMax: 500,
              beginAtZero: true,
              padding: 10,
              font: {
                size: 14,
                lineHeight: 2
              },
              color: "#737373"
            },
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              color: '#737373',
              padding: 10,
              font: {
                size: 14,
                lineHeight: 2
              },
            }
          },
        },
      },
    });


    var ctx2 = document.getElementById("chart-line").getContext("2d");

    new Chart(ctx2, {
      type: "line",
      data: {
        labels: ["J", "F", "M", "A", "M", "J", "J", "A", "S", "O", "N", "D"],
        datasets: [{
          label: "Sales",
          tension: 0,
          borderWidth: 2,
          pointRadius: 3,
          pointBackgroundColor: "#43A047",
          pointBorderColor: "transparent",
          borderColor: "#43A047",
          backgroundColor: "transparent",
          fill: true,
          data: [120, 230, 130, 440, 250, 360, 270, 180, 90, 300, 310, 220],
          maxBarThickness: 6

        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          },
          tooltip: {
            callbacks: {
              title: function(context) {
                const fullMonths = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                return fullMonths[context[0].dataIndex];
              }
            }
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [4, 4],
              color: '#e5e5e5'
            },
            ticks: {
              display: true,
              color: '#737373',
              padding: 10,
              font: {
                size: 12,
                lineHeight: 2
              },
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              color: '#737373',
              padding: 10,
              font: {
                size: 12,
                lineHeight: 2
              },
            }
          },
        },
      },
    });

    var ctx3 = document.getElementById("chart-line-tasks").getContext("2d");

    new Chart(ctx3, {
      type: "line",
      data: {
        labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
          label: "Tasks",
          tension: 0,
          borderWidth: 2,
          pointRadius: 3,
          pointBackgroundColor: "#43A047",
          pointBorderColor: "transparent",
          borderColor: "#43A047",
          backgroundColor: "transparent",
          fill: true,
          data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
          maxBarThickness: 6

        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [4, 4],
              color: '#e5e5e5'
            },
            ticks: {
              display: true,
              padding: 10,
              color: '#737373',
              font: {
                size: 14,
                lineHeight: 2
              },
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [4, 4]
            },
            ticks: {
              display: true,
              color: '#737373',
              padding: 10,
              font: {
                size: 14,
                lineHeight: 2
              },
            }
          },
        },
      },
    });
  </script>
  <!--Awstats Gráficos-->
  <script>
    (function(){
      const CHART_IDS = ['chart-bars','chart-line','chart-line-tasks'];

      function waitForCharts(ids, timeout = 2000, interval = 50) {
        return new Promise(resolve => {
          const start = Date.now();
          const timer = setInterval(() => {
            const found = {};
            let all = true;
            ids.forEach(id => {
              try {
                const c = Chart.getChart(id);
                if (c) found[id] = c;
                else all = false;
              } catch(e) {
                all = false;
              }
            });
            if (all || (Date.now() - start) > timeout) {
              clearInterval(timer);
              resolve(found);
            }
          }, interval);
        });
      }

      function daysToArrays(daysObj) {
        const keys = Object.keys(daysObj).map(k => String(k));
        keys.sort((a,b)=> parseInt(a,10) - parseInt(b,10));
        return {
          labels: keys.map(k => String(parseInt(k,10))),
          data: keys.map(k => Number(daysObj[k] || 0))
        };
      }

      function aggregateWeekdaysFromDays(daysObj, year, month) {
        const labels = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        const counts = [0,0,0,0,0,0,0];
        Object.entries(daysObj).forEach(([k,v]) => {
          const dayNum = parseInt(k,10);
          if (!isFinite(dayNum)) return;
          const d = new Date(Number(year), Number(month) - 1, dayNum);
          const w = d.getDay();
          counts[w] += Number(v || 0);
        });
        return { labels, counts };
      }

      function monthsToArray(monthsObj, year, month, daysObj) {
        const monthLabels = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        const arr = new Array(12).fill(0);
        const keys = Object.keys(monthsObj || {});
        if (keys.length > 0) {
          Object.entries(monthsObj).forEach(([k,v]) => {
            const m = parseInt(String(k).slice(-2),10);
            if (m >= 1 && m <= 12) arr[m-1] = Number(v || 0);
          });
        } else if (daysObj && Object.keys(daysObj).length > 0 && month) {
          const total = Object.values(daysObj).reduce((s,x) => s + Number(x||0), 0);
          const sel = Number(month) - 1;
          if (sel >= 0 && sel < 12) arr[sel] = total;
        }
        return { labels: monthLabels, data: arr };
      }

      function updateCharts(instances, data, year, month) {
        // tenta obter ano/mês do próprio JSON caso não venha via parâmetro
        const usedYear = year ?? data.year ?? new Date().getFullYear();
        const usedMonth = month ?? data.month ?? (new Date().getMonth()+1);

        // 1) chart-bars
        const chartBars = instances['chart-bars'];
        if (chartBars) {
          let labels = [];
          let values = [];
          if (data.weekdays && Object.keys(data.weekdays).length) {
            labels = Object.keys(data.weekdays);
            values = Object.values(data.weekdays).map(Number);
          } else if (data.days && Object.keys(data.days).length) {
            const agg = aggregateWeekdaysFromDays(data.days, usedYear, usedMonth);
            labels = agg.labels;
            values = agg.counts;
          }
          chartBars.data.labels = labels;
          chartBars.data.datasets[0].data = values;
          chartBars.update();
        }

        // 2) chart-line
        const chartLine = instances['chart-line'];
        if (chartLine) {
          const monthsResult = monthsToArray(data.months || {}, usedYear, usedMonth, data.days || {});
          chartLine.data.labels = monthsResult.labels;
          chartLine.data.datasets[0].data = monthsResult.data;
          chartLine.update();
        }

        // 3) chart-line-tasks
        const chartTasks = instances['chart-line-tasks'];
        if (chartTasks) {
          if (data.days && Object.keys(data.days).length) {
            const da = daysToArrays(data.days);
            chartTasks.data.labels = da.labels;
            chartTasks.data.datasets[0].data = da.data;
          }
          chartTasks.update();
        }
      }

      async function fetchAndUpdateCharts(instances, year = null, month = null) {
        try {
          let url = 'awstats_reader.php';
          if (year !== null && month !== null) {
            url += `?year=${encodeURIComponent(year)}&month=${encodeURIComponent(month)}`;
          }
          const resp = await fetch(url, { cache: 'no-store' });
          if (!resp.ok) throw new Error('HTTP ' + resp.status);
          const data = await resp.json();
          updateCharts(instances, data, year, month);
        } catch (err) {
          console.error('Erro ao buscar/atualizar gráficos AWStats:', err);
        }
      }

      document.addEventListener('DOMContentLoaded', async () => {
        const instances = await waitForCharts(CHART_IDS, 2000);

        function reload(useLatest = true) {
          if (useLatest) {
            fetchAndUpdateCharts(instances); // último ficheiro
          } else {
            const yearEl = document.getElementById('selectYear');
            const monthEl = document.getElementById('selectMonth');
            const year = yearEl ? yearEl.value : new Date().getFullYear();
            const month = monthEl ? monthEl.value : (new Date().getMonth() + 1);
            fetchAndUpdateCharts(instances, year, month);
          }
        }

        const btn = document.getElementById('btnLoadData');
        if (btn) btn.addEventListener('click', () => reload(false));

        // inicial → último ficheiro modificado
        reload(true);
      });
    })();
  </script>
  <!--Awstats cards-->
  <script>
    (() => {
      // IDs que vamos atualizar (mantemos compatibilidade com variantes)
      const ID_ALIASES = {
        uniqueVisitors: ['uniqueVisitors', 'card-unique', 'totalUnique'],
        totalVisits:    ['totalVisits', 'card-visits', 'totalVisits'], 
        pagesPerVisit:  ['pagesPerVisit', 'card-pages', 'pagesPerVisit'],
        avgDuration:    ['avgDuration', 'card-duration', 'avgDuration'],
        lastUpdate:     ['lastUpdate'],
        month:          ['month', 'card-month'],
        year:           ['year', 'card-year']
      };

      // utilitários
      const el = id => document.getElementById(id) || null;
      const setText = (id, text) => { const e = el(id); if (e) e.innerText = text; };
      const setTextIfExists = (ids, text) => ids.forEach(i => setText(i, text));

      const nfInt = v => {
        if (v === null || v === undefined || v === '') return v;
        const n = Number(v);
        return Number.isFinite(n) ? new Intl.NumberFormat('pt-PT').format(Math.round(n)) : v;
      };
      const nfFloat = v => {
        if (v === null || v === undefined || v === '') return v;
        const n = Number(v);
        return Number.isFinite(n) ? new Intl.NumberFormat('pt-PT', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n) : v;
      };
      const formatDuration = s => {
        // se for string não-numérica devolve tal qual; se for número (segundos) -> hh:mm:ss
        if (s === null || s === undefined || s === '') return s;
        const n = Number(s);
        if (!Number.isFinite(n)) return s;
        const sec = Math.max(0, Math.floor(n));
        const h = Math.floor(sec / 3600);
        const m = Math.floor((sec % 3600) / 60);
        const se = sec % 60;
        if (h > 0) return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(se).padStart(2,'0')}`;
        return `${String(m).padStart(2,'0')}:${String(se).padStart(2,'0')}`;
      };

      // --- helpers para formatar lastUpdate ---
function parseDate(val) {
  if (!val) return null;
  const s = String(val).trim();

  // epoch em segundos ou milissegundos
  if (/^\d+$/.test(s)) {
    const n = Number(s);
    return s.length <= 10 ? new Date(n * 1000) : new Date(n);
  }

  // yyyy-mm-dd hh:mm:ss
  let m = s.match(/^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})(?::(\d{2}))?$/);
  if (m) return new Date(+m[1], +m[2] - 1, +m[3], +m[4], +m[5], +(m[6] || 0));

  // dd/mm/yyyy hh:mm:ss
  m = s.match(/^(\d{2})\/(\d{2})\/(\d{4})(?:[ T](\d{2}):(\d{2})(?::(\d{2}))?)?$/);
  if (m) return new Date(+m[3], +m[2] - 1, +m[1], +(m[4] || 0), +(m[5] || 0), +(m[6] || 0));

  const d = new Date(s);
  return isNaN(d) ? null : d;
}

const capitalize = str => str.charAt(0).toUpperCase() + str.slice(1);

function formatLastUpdate(val) {
  const d = parseDate(val);
  if (!d) return val || '--';
  const formatted = d.toLocaleString('pt-PT', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
  return capitalize(formatted);
}

      // mostra erro visível (cria elemento se não existir)
      function showError(msg) {
        let holder = document.getElementById('awstats-error');
        if (!holder) {
          const container = document.createElement('div');
          container.id = 'awstats-error';
          container.style.color = 'crimson';
          container.style.marginTop = '8px';
          // tentar anexar perto do lastUpdate, senão ao body
          const ref = el('lastUpdate');
          (ref && ref.parentElement ? ref.parentElement : document.body).appendChild(container);
          holder = container;
        }
        holder.innerText = msg || '';
      }

      // função principal para buscar dados e atualizar os cards
      async function fetchAndUpdate(year, month) {
        // botão de carregamento (se existir)
        const btn = el('btnLoadData');
        const originalBtnText = btn ? btn.innerText : null;
        if (btn) { btn.disabled = true; btn.innerText = 'Carregando...'; }

        // limpar erro anterior
        showError('');

        try {
          const query = `awstats_reader.php?year=${encodeURIComponent(year)}&month=${encodeURIComponent(month)}`;
          const res = await fetch(query, { cache: 'no-store' });
          if (!res.ok) throw new Error(`Resposta HTTP ${res.status}`);
          const data = await res.json();

          // se PHP devolveu erro no JSON
          if (data && data.error) throw new Error(data.error);

          // Debug (opcional): mostrar object no console para inspeção
          console.debug('AWStats data:', data);

          // mapeamentos e formatações
          const uv = data.uniqueVisitors ?? data.unique_visitors ?? 0;
          const tv = data.totalVisits ?? data.total_visits ?? 0;
          const ppv = data.pagesPerVisit ?? data.pages_per_visit ?? data.pages_per_visit ?? 0;
          const dur = data.avgDuration ?? data.VisitDuration ?? data.Visit_Duration ?? 0;
          const last = data.lastUpdate ?? data.LastUpdate ?? '--';

          setTextIfExists(ID_ALIASES.uniqueVisitors, nfInt(uv));
          setTextIfExists(ID_ALIASES.totalVisits, nfInt(tv));
          // páginas/visita costuma ser float
          setTextIfExists(ID_ALIASES.pagesPerVisit, nfFloat(ppv));
          // duração formatada (hh:mm:ss) se for um número de segundos
          setTextIfExists(ID_ALIASES.avgDuration, formatDuration(dur) ?? dur);
          setTextIfExists(ID_ALIASES.lastUpdate, formatLastUpdate(last));


        } catch (err) {
          console.error('Erro ao carregar AWStats:', err);
          showError('Erro ao carregar dados AWStats: ' + (err.message || err));
        } finally {
          if (btn) { btn.disabled = false; if (originalBtnText) btn.innerText = originalBtnText; }
        }
      }

      // inicialização: ligar eventos e carregar dados iniciais
      document.addEventListener('DOMContentLoaded', () => {
        const yearSel = el('selectYear');
        const monthSel = el('selectMonth');
        const btn = el('btnLoadData');

        const getYear = () => (yearSel ? yearSel.value : (new Date()).getFullYear());
        const getMonth = () => (monthSel ? monthSel.value : (new Date()).getMonth() + 1);

        if (btn) {
          btn.addEventListener('click', () => {
            fetchAndUpdate(getYear(), getMonth());
          });
        }

        // carrega inicialmente
        fetchAndUpdate(null, null);
      });
    })();
  </script>
  <!--Awstats Listas-->
  <script>
    function loadRegions(year = null, month = null) {
      let url = "awstats_reader.php";
      if (year && month) {
        url += `?year=${year}&month=${month}`;
      }

      fetch(url, { cache: "no-store" }) // evita cache do browser
        .then(res => res.json())
        .then(data => {
          const tbody = document.getElementById("region-access-table");
          tbody.innerHTML = "";

          // conversor de sigla -> nome do país em português
          const regionNames = new Intl.DisplayNames(['pt'], { type: 'region' });

          if (!data.locales) return;

          // filtra apenas países com acessos > 0
          const validLocales = Object.entries(data.locales).filter(([_, val]) => val > 0);

          const total = validLocales.reduce((a, [_, b]) => a + b, 0);

          validLocales.forEach(([region, value]) => {
            const percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
            // converte sigla para nome, ex: BR -> Brasil
            const regionName = regionNames.of(region.toUpperCase()) || region;

            const tr = document.createElement("tr");
            tr.innerHTML = `
              <td>
                <div class="d-flex px-2 py-1">
                  <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm">${regionName}</h6>
                  </div>
                </div>
              </td>
              <td class="align-middle text-center text-sm">
                <span class="text-xs font-weight-bold">${value}</span>
              </td>
              <td class="align-middle">
                <div class="progress-wrapper w-75 mx-auto">
                  <div class="progress-info">
                    <div class="progress-percentage">
                      <span class="text-xs font-weight-bold">${percent}%</span>
                    </div>
                  </div>
                  <div class="progress">
                    <div class="progress-bar bg-gradient-info" role="progressbar"
                        style="width: ${percent}%"
                        aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
              </td>
            `;
            tbody.appendChild(tr);
          });

          // define o valor do seletor conforme resposta
          if (data.year && data.month) {
            const mp = String(data.month).padStart(2, "0");
            document.getElementById("periodSelector").value = `${data.year}-${mp}`;
          }
        })
        .catch(err => console.error("Erro ao carregar dados:", err));
    }

    document.addEventListener("DOMContentLoaded", () => {
      // inicializa com último ficheiro (padrão do PHP)
      loadRegions();

      // quando muda o seletor do card
      document.getElementById("periodSelector").addEventListener("change", (e) => {
        const [year, month] = e.target.value.split("-");
        loadRegions(year, month);
      });

      // quando confirma pelo painel global
      const btn = document.getElementById("btnLoadData");
      if (btn) {
        btn.addEventListener("click", () => {
          const year = document.getElementById("selectYear").value;
          const month = document.getElementById("selectMonth").value;
          loadRegions(year, month);
        });
      }
    });
  </script>
  <!--Awstats Dispositivos-->
  <script>
    function loadDevices(year = null, month = null) {
      let url = "awstats_reader.php";
      if (year && month) {
        url += `?year=${encodeURIComponent(year)}&month=${encodeURIComponent(month)}`;
      }

      fetch(url, { cache: "no-store" })
        .then(res => {
          if (!res.ok) throw new Error("Erro HTTP " + res.status);
          return res.json();
        })
        .then(data => {
          const devices = data.devices || {};
          const total = Object.values(devices).reduce((s, v) => s + Number(v || 0), 0);

          const getPercent = (val) => total > 0 ? ((val / total) * 100).toFixed(1) + "%" : "0%";

          document.getElementById("desktopValue").innerText = `${getPercent(devices.Desktop || 0)} dos acessos`;
          document.getElementById("mobileValue").innerText = `${getPercent(devices.Mobile || 0)} dos acessos`;
          document.getElementById("tabletValue").innerText = `${getPercent(devices.Tablet || 0)} dos acessos`;
          document.getElementById("otherValue").innerText = `${getPercent(devices.Other || 0)} dos acessos`;

          // exemplo simples de crescimento: mobile vs total
          let growth = total > 0 ? ((devices.Mobile || 0) / total * 100).toFixed(1) : 0;
          document.getElementById("devicesGrowth").innerText = `+${growth}% mobile`;

          // Atualiza seletor global para refletir os dados carregados
          if (data.year && data.month) {
            const mp = String(data.month).padStart(2, "0");
            const sel = document.getElementById("periodSelector");
            if (sel) sel.value = `${data.year}-${mp}`;
          }
        })
        .catch(err => {
          console.error("Erro ao carregar dispositivos:", err);
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
      // inicializa com último ficheiro (padrão do PHP)
      loadDevices();

      // quando muda o seletor do card
      const periodSelector = document.getElementById("periodSelector");
      if (periodSelector) {
        periodSelector.addEventListener("change", (e) => {
          const [year, month] = e.target.value.split("-");
          loadDevices(year, month);
        });
      }

      // quando confirma pelo painel global
      const btn = document.getElementById("btnLoadData");
      if (btn) {
        btn.addEventListener("click", () => {
          const year = document.getElementById("selectYear").value;
          const month = document.getElementById("selectMonth").value;
          loadDevices(year, month);
        });
      }
    });
  </script>
</body>

</html>