<?php
ob_start();

include("../dbconn.php");
include("../fuggvenyek.php");

header("Pragma: no-cache");
header("Cache-control: private, no-store, no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// Az ID beolvasása a GET paraméterből
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$torolhet = 1;

// Ellenőrizzük, hogy a termék raktáron van-e
$sql = "SELECT * FROM arucikk WHERE id=$id AND raktaron>0";
$eredmeny = mysqli_query($kapcsolat, $sql);
if (mysqli_num_rows($eredmeny) > 0) {
  $torolhet = 0;
}

// Ellenőrizzük, hogy a termék szerepel-e a kosárban
$sql = "SELECT * FROM kosar WHERE arucikk_id=$id";
$eredmeny = mysqli_query($kapcsolat, $sql);
if (mysqli_num_rows($eredmeny) > 0) {
  $torolhet = 0;
}

// Ellenőrizzük, hogy a terméket megtekintették-e
$sql = "SELECT * FROM megtekintve WHERE arucikk_id=$id";
$eredmeny = mysqli_query($kapcsolat, $sql);
if (mysqli_num_rows($eredmeny) > 0) {
  $torolhet = 0;
}

if ($id > 0) {
  ?>
  <html>
  <head>
    <meta name="cache-control" content="private, no-store, no-cache, must-revalidate">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Termék Törlés</title>
  </head>
  <body style="font-family:tahoma">
    <div class="container mt-4">
      <?php
      if ($torolhet == 1) {
        $sql = "DELETE FROM arucikk WHERE id=$id";
        mysqli_query($kapcsolat, $sql);
        ?>
        <div class="alert alert-success" role="alert">
          <h4 class="alert-heading">A termék törölve lett.</h4>
        </div>
        <?php
      } else {
        ?>
        <div class="alert alert-danger" role="alert">
          <h4 class="alert-heading">A termék nem törölhető!</h4>
          <p>A termék raktáron van, szerepel a kosárban, vagy megtekintették.</p>
          <hr>
          <a href="termek_modositas2.php?id=<?= $id ?>" class="btn btn-primary">A termék adatainak módosítása...</a>
        </div>
        <?php
      }
      ?>
    </div>
  </body>
  </html>
  <?php
} else {
  echo "<div class='alert alert-danger'><b>HIBA</b></div>";
}

mysqli_close($kapcsolat);
ob_end_flush();
?>
