<?php
ob_start();

include("php/dbconn.php");

header("Pragma: no-cache"); 
Header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

if (isset($_POST['emailcim']) && isset($_POST['nev']) && isset($_POST['jelszo']) && isset($_POST['jelszo2'])) {
  $emailcim = mysqli_real_escape_string($kapcsolat, $_POST['emailcim']);
  $nev = mysqli_real_escape_string($kapcsolat, $_POST['nev']);
  $jelszo = $_POST['jelszo'];
  $jelszo2 = $_POST['jelszo2'];

  // Jelszókomplexitás ellenőrzése
  if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $jelszo)) {
    $errorMessage = "A jelszónak minimum 8 karakter, 1 nagybetű és 1 szám kell tartalmaznia!";
  }
  elseif ($jelszo !== $jelszo2) {
    $errorMessage = "A két jelszó nem egyezik.";
  } else {
    // SQL injection védelem prepared statementtal
    $stmt = $kapcsolat->prepare("SELECT * FROM ugyfel WHERE email = ?");
    $stmt->bind_param("s", $emailcim);
    $stmt->execute();
    $eredmeny = $stmt->get_result();

    if (mysqli_num_rows($eredmeny) == 0) {
      $titkos = password_hash($jelszo, PASSWORD_DEFAULT);
      $idopont = date("Y-m-d H:i:s");

      $insert = $kapcsolat->prepare("INSERT INTO ugyfel (email, nev, jelszo, reg_idopont) VALUES (?, ?, ?, ?)");
      $insert->bind_param("ssss", $emailcim, $nev, $titkos, $idopont);
      
      if ($insert->execute()) {
        $successMessage = "A regisztráció sikeres volt. Most már bejelentkezhetsz.";
      } else {
        $errorMessage = "Hiba történt a regisztráció során. Próbáld újra.";
      }
      $insert->close();
    } else {
      $errorMessage = "A megadott e-mail címmel már regisztráltak.";
    }
    $stmt->close();
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
