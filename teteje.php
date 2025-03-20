<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

include("php/dbconn.php"); // Új elérési út

if (!$kapcsolat) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_COOKIE['webshop_email'])) {
  $webshop_email = $_COOKIE['webshop_email'];
}
else {
  $webshop_email = "";
}

if (isset($_COOKIE['webshop_jelszo'])) {
  $webshop_jelszo = $_COOKIE['webshop_jelszo'];
}
else {
  $webshop_jelszo = "";
}

$belepve = 0;

if ($webshop_email != "" && $webshop_jelszo != "") {
  // Prepared Statement használata SQL Injection ellen
  $parancs = $kapcsolat->prepare("SELECT id, nev, role FROM ugyfel WHERE email=? AND jelszo=?");
  $parancs->bind_param("ss", $webshop_email, $webshop_jelszo);
  $parancs->execute();
  $eredmeny = $parancs->get_result();

  if ($eredmeny->num_rows > 0) {
    $sor = $eredmeny->fetch_assoc();
    $webshop_id = $sor["id"];
    $webshop_nev = $sor["nev"];
    $webshop_role = $sor["role"]; // Új változó a role tárolására
    $belepve = 1;
  }
}


// A $belepes változó kezelése
$belepes = 0; 
if(isset($_POST['mit']) && $_POST['mit'] == 'ellenoriz') {
  $belepes = 1;
}

?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/index.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="styles/teteje.css">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>

<body style="margin:0px;border:0px;" background="img/bg1.jpg">

  <div class="container">
  <div class="row" style="margin-bottom: -16px;">
      <div class="col-12 text-center" onClick="window.location='index.php'" style="cursor:pointer; background:url('img/banner2.png') center center no-repeat; background-repeat:no-repeat; border-top:0px #FFFFFF solid; height:250px;">
      </div>
    </div>


    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mt-3 rounded-top" style="
    margin-right: -12px;
    margin-left: -12px;
    border-bottom: 1px solid #dfdede;">
      <div class="container-fluid">
        <a class="navbar-brand gombok" href="index.php">Főoldal</a>
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
          <a class="nav-link active gombok" aria-current="page" href="info.php">ÁSZF</a>
        </li>
        <li class="nav-item">
          <a class="nav-link gombok" href="kosar.php">Kosár</a>
        </li>
<!-- Dropdown menü a felhasználói állapot szerint -->
<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle gombok" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" 
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
      <li><a class="dropdown-item" href="profil_modosit.php">Adatok módosítása</a></li>
      <li><a class="dropdown-item" href="korabbi_rendelesek.php">Korábbi rendeléseid</a></li>
      <?php 
      // Ha a felhasználó role értéke admin, akkor megjelenítjük az admin felület linket
      if (isset($webshop_role) && $webshop_role === "admin") { ?>
        <li><a class="dropdown-item" href="php/admin-index.php">Admin felület</a></li>
      <?php } ?>
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
      <div class="col-md-3" style="background-color: white;">
        <div class="bg-white p-3 mt-4 text-center border shadow-sm">
          <b>Kosár tartalma</b>
          <iframe id="kosar" name="kosar" src="kosar.php" class="w-100" height="100" scrolling="no" frameborder="0"></iframe>
        </div>
      </div>
      <div class="col-md-9" style="background-color: white;border-left: 0.8px solid #dfdede;">
