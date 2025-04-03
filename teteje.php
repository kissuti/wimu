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

    $_SESSION['belepve'] = 1;
    $_SESSION['webshop_id'] = $webshop_id;
    $_SESSION['webshop_nev'] = $webshop_nev;
    $_SESSION['webshop_role'] = $webshop_role;
  }
}


// A $belepes változó kezelése
$belepes = 0; 
if(isset($_POST['mit']) && $_POST['mit'] == 'ellenoriz') {
  $belepes = 1;
}

?>
<!DOCTYPE html>
<html lang="hu">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="styles/teteje.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body style="margin:0px; border:0px;" background="img/bg1.jpg">
  <div class="container">
    <div class="row" style="margin-bottom: -16px;">
      <div class="col-12 text-center" onClick="window.location='index.php'" style="cursor:pointer; background:url('img/banner2.png') center center no-repeat; height:250px; margin-top: -24px;">
      </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mt-3 rounded-top" style="margin-right: -12px; margin-left: -12px; border-bottom: 1px solid #dfdede;">
      <div class="container-fluid">
        <a class="navbar-brand gombok" href="index.php">Főoldal</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link active gombok" aria-current="page" href="info.php">ÁSZF</a>
            </li>
            <li class="nav-item">
              <a class="nav-link gombok" href="kosar_megtekintes.php">Kosár</a>
            </li>
            <!-- Felhasználói menü -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle gombok" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?= $belepve ? htmlspecialchars($webshop_nev) : "Bejelentkezés" ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                <?php if($belepve): ?>
                  <li><a class="dropdown-item" href="profil_modosit.php"><i class="bi bi-person"></i> Profil</a></li>
                  <li><a href="korabbi_rendelesek.php" class="dropdown-item"><i class="bi bi-bag-check"></i> Korábbi rendelések</a></li>
                  <!-- Ellenőrizzük, hogy a szerepkör, kisbetűsítve, megegyezik-e az "admin" sztringgel -->
                  <?php if(isset($webshop_role) && strtolower(trim($webshop_role)) === 'admin'): ?>
                    <li><a class="dropdown-item text-danger" href="php/admin-index.php"><i class="bi bi-shield-lock"></i> Admin felület</a></li>
                  <?php endif; ?>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="kilepes.php"><i class="bi bi-box-arrow-right"></i> Kijelentkezés</a></li>
                <?php else: ?>
                  <li><a class="dropdown-item" href="belepes.php"><i class="bi bi-box-arrow-in-right"></i> Belépés</a></li>
                  <li><a class="dropdown-item" href="reg.php"><i class="bi bi-person-plus"></i> Regisztráció</a></li>
                <?php endif; ?>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <!-- Navbar vége -->



    <div class="row" style="height: 614px;">
      <div class="col-md-3" style="background-color: white;">
        <div class="bg-white p-0 text-center border">
          <iframe id="kosar" name="kosar" src="kosar.php" class="w-100" height="100" scrolling="no" frameborder="0"></iframe>
        </div>
      </div>
      <div class="col-md-9" style="background-color: white;border-left: 0.8px solid #dfdede;">
