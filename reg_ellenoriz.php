<?php
ob_start();

include("php/dbconn.php");

header("Pragma: no-cache"); 
Header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

if (isset($_POST['emailcim']) && isset($_POST['nev']) && isset($_POST['jelszo']) && isset($_POST['jelszo2'])) {
  $emailcim = $_POST['emailcim'];
  $nev = $_POST['nev'];
  $jelszo = $_POST['jelszo'];
  $jelszo2 = $_POST['jelszo2'];

  if ($jelszo !== $jelszo2) {
    $errorMessage = "A két jelszó nem egyezik.";
  } else {
    $parancs = "SELECT * from ugyfel WHERE email='$emailcim'";
    $eredmeny = mysqli_query($kapcsolat, $parancs);

    if (mysqli_num_rows($eredmeny) == 0) {
      $titkos = password_hash($jelszo, PASSWORD_DEFAULT);
      $idopont = date("Y-m-d H:i:s");

      $sql = "INSERT INTO ugyfel (email, nev, jelszo, reg_idopont) VALUES ('$emailcim', '$nev', '$titkos', '$idopont')";
      if (mysqli_query($kapcsolat, $sql)) {
        $successMessage = "A regisztráció sikeres volt. Most már bejelentkezhetsz.";
      } else {
        $errorMessage = "Hiba történt a regisztráció során. Próbáld újra.";
      }
    } else {
      $errorMessage = "A megadott e-mail címmel már regisztráltak.";
    }
  }
  ?>
  <html>
  <head>
    <title>Wimu Webshop</title>
    <meta name="cache-control" content="private, no-store, no-cache, must-revalidate" />
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <meta http-equiv="Content-Type" content="text/html" />
    <style>
      div{
        color: black;
      }
    </style>
  </head>

  <?php include("teteje.php"); ?>

  <div style="width:100%; text-align:right">
    <a href="index.php"><font color="<?= $hatter?>">visszatérés a webshop-hoz</font></a>
  </div>
  <font style="font-size:14pt;color:<?= $sotet?>"><b>Regisztráció</b></font>
  <br><br><br>

  <?php
  if (isset($successMessage)) {
    ?>
    <div class="alert alert-success" role="alert">
      <h4 class="alert-heading">Sikeres regisztráció!</h4>
      <p><?= $successMessage ?></p>
      <hr>
      <a href="belepes.php" class="btn btn-primary">Bejelentkezés</a>
    </div>
    <?php
  } elseif (isset($errorMessage)) {
    ?>
    <div class="alert alert-danger" role="alert">
      <h4 class="alert-heading">Hiba történt!</h4>
      <p><?= $errorMessage ?></p>
      <hr>
      <a href="reg.php" class="btn btn-primary">Vissza a regisztrációhoz</a>
    </div>
    <?php
  }
  ?>

  <?php include("alja.php"); ?>

  <?php
} else {
  header("Location: index.php");
}

mysqli_close($kapcsolat);
ob_end_flush();
?>
