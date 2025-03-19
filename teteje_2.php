<?php

if (isset($_COOKIE['webshop_email'])) {
  $webshop_email = $_COOKIE['webshop_email'];
} else {
  $webshop_email = "";
}

if (isset($_COOKIE['webshop_jelszo'])) {
  $webshop_jelszo = $_COOKIE['webshop_jelszo'];
} else {
  $webshop_jelszo = "";
}

$belepve = 0;

if ($webshop_email != "" && $webshop_jelszo != "") {
  $most = date("Y-m-d H:i:s");

  $parancs = "SELECT * from ugyfel WHERE email='$webshop_email' AND jelszo='$webshop_jelszo'";
  $eredmeny = mysqli_query($kapcsolat, $parancs);

  if (mysqli_num_rows($eredmeny) > 0) {
    $sor = mysqli_fetch_array($eredmeny);
    $webshop_id = $sor["id"];
    $webshop_nev = $sor["nev"];

    $belepve = 1;
  }
}

?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/index.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</head>

<body style="margin:0px;border:0px;">

  <div class="container mt-4">

    <div class="row">
      <div class="col-12 text-center" onClick="window.location='index.php'" style="cursor:pointer; background:url('img/banner2.png') center center no-repeat; background-repeat:no-repeat; border-top:0px #FFFFFF solid; border-bottom:10px solid white; height:250px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
      </div>
    </div>

        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light mt-3">
      <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Főoldal</a>
          <button 
          class="navbar-toggler" 
          type="button" 
          data-bs-toggle="collapse" 
          data-bs-target="#navbarSupportedContent" 
          aria-controls="navbarSupportedContent" 
          aria-expanded="false" 
          aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="info.php">ÁSZF</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="kosar.php">Kosár</a>
        </li>
        <!-- Dropdown menü a felhasználói állapot szerint -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" 
             aria-expanded="false">
            <?php 
            if ($belepve == 1) {
              echo ($webshop_nev != "") ? $webshop_nev : $webshop_email;
            } else {
              echo "Bejelentkezés / Regisztráció";
            }
            ?>
          </a>
          <ul class="dropdown-menu" aria-labelledby="userDropdown">
            <?php if ($belepve == 1) { ?>
              <li><a class="dropdown-item" href="profil_modosit.php">Beállításaid módosítása</a></li>
              <li><a class="dropdown-item" href="korabbi_rendelesek.php">Korábbi rendeléseid</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="kilepes.php">Kijelentkezés</a></li>
            <?php } else { ?>
              <li><a class="dropdown-item" href="belepes.php">Belépés</a></li>
              <li><a class="dropdown-item" href="reg.php">Regisztráció</a></li>
            <?php } ?>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
<!-- Navbar vége -->

    <div class="row">
      <div class="col-md-3 kulonshadow" style="background: rgb(255 255 255);padding: 0px; height: 364px;">
        <div class="bg-white p-3 mt-3 text-center border shadow-sm">
          <b>Kosár tartalma</b>
          <iframe id="kosar" name="kosar" src="kosar.php" class="w-100" height="100" scrolling="no" frameborder="0"></iframe>
        </div>
      </div>
      <div class="col-md-9" style="color:<?= $szoveg?>; padding:20px; border-left:10px <?= $hatter?> solid; border-bottom:2px <?= $hatter?> solid;">

