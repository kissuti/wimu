<?php
ob_start();
session_start();

include("php/dbconn.php");
include("php/fuggvenyek.php");

header("Pragma: no-cache"); 
header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// Cookie kezelés
$webshop_email = $_COOKIE['webshop_email'] ?? "";
$webshop_jelszo = $_COOKIE['webshop_jelszo'] ?? "";
$belepve = 0;

// Belépés ellenőrzése
if (!empty($webshop_email) && !empty($webshop_jelszo)) {
    $parancs = "SELECT * FROM ugyfel WHERE email='$webshop_email' AND jelszo='$webshop_jelszo'";
    $eredmeny = mysqli_query($kapcsolat, $parancs);
    
    if (mysqli_num_rows($eredmeny) > 0) {
        $sor = mysqli_fetch_array($eredmeny);
        $webshop_id = $sor["id"];
        $webshop_nev = $sor["nev"];
        $belepve = 1;
    }
}

// Kosár ürítése, ha a GET-ben torolni paraméter érkezik
if (isset($_GET['torolni'])) {
    $sql = ($belepve == 1)
        ? "DELETE FROM kosar WHERE ugyfel_id=$webshop_id AND rendeles_id=0"
        : "DELETE FROM kosar WHERE session_id='" . session_id() . "' AND rendeles_id=0";
    mysqli_query($kapcsolat, $sql);
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Kosár</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="styles/index.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="card-header fw-bold" style="background-color: #bbbbbb;">
        Kosár tartalma
      </div>
      <div class="card-body">
        <?php
        $osszeg = 0;
        $sql = ($belepve == 0)
            ? "SELECT * FROM kosar WHERE session_id='" . session_id() . "' AND rendeles_id=0"
            : "SELECT * FROM kosar WHERE ugyfel_id=$webshop_id AND rendeles_id=0";

        $eredmeny = mysqli_query($kapcsolat, $sql);
        $sorok = mysqli_num_rows($eredmeny);

        if ($sorok > 0) {
          echo '<ul class="list-group">';
          while ($sor = mysqli_fetch_array($eredmeny)) {
              $arucikk_id = $sor["arucikk_id"];
              $db = $sor["db"];
              
              $termek = mysqli_query($kapcsolat, "SELECT * FROM arucikk WHERE id=$arucikk_id");
              if (mysqli_num_rows($termek) > 0) {
                  $egysor = mysqli_fetch_array($termek);
                  $osszeg += $db * $egysor["ar_huf"];
                  ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                      <strong><?= $egysor["nev"] ?></strong> <br>
                      <small>Mennyiség: <?= $db ?></small>
                    </div>
                    <span><?= szampontos($db * $egysor["ar_huf"]) ?> HUF</span>
                  </li>
                  <?php
              }
          }
          echo '</ul>';
          ?>
          <div class="mt-3 text-end">
            <h5>Összesen: <strong><?= szampontos($osszeg) ?> HUF</strong></h5>
          </div>
          <?php
        } else {
          echo '<div class="alert alert-info text-center">A kosár üres.</div>';
        }
        ?>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS bundle (Popper included) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
          integrity="sha384-ENjdO4Dr2bkBIFxQpeoRZr8Ely2JztgS0rWErX5JqNQu/8kmwDKdIm2w2O8yK6gT" crossorigin="anonymous"></script>
  
  <!-- Iframe méret beállítása (ha szükséges) -->
  <script>
    const sorok = <?= $sorok ?>;
    const frameHeight = sorok > 0 ? 150 + (sorok * 50) : 150;
    if (parent && parent.document.getElementById('kosar')) {
      parent.document.getElementById('kosar').style.height = `${frameHeight}px`;
    }
  </script>
</body>
</html>
<?php
mysqli_close($kapcsolat);
ob_end_flush();
?>
