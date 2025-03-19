<?php
ob_start();
session_start();

include("php/dbconn.php");
include("php/fuggvenyek.php");

header("Pragma: no-cache"); 
Header("Cache-control: private, no-store, no-cache, must-revalidate");  
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

// Kosár ürítése
if (isset($_GET['torolni'])) {
    $sql = ($belepve == 1)
        ? "DELETE FROM kosar WHERE ugyfel_id=$webshop_id AND rendeles_id=0"
        : "DELETE FROM kosar WHERE session_id='" . session_id() . "' AND rendeles_id=0";
    mysqli_query($kapcsolat, $sql);
}

?>

<html>
  <head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles/index.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
          rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
          crossorigin="anonymous">
  </head>
<body>
  
  <!-- Táblázat stílusok javítva -->
  <table align="center" cellspacing="0" cellpadding="5" 
         style="font-family:tahoma;font-size:8pt;
                color:<?= $sotet ?>;
                border-collapse: collapse;
                width: 100%;
                margin: 10px 0;">
    
    <?php
    $osszeg = 0;
    $sql = ($belepve == 0)
        ? "SELECT * FROM kosar WHERE session_id='" . session_id() . "' AND rendeles_id=0"
        : "SELECT * FROM kosar WHERE ugyfel_id=$webshop_id AND rendeles_id=0";

    $eredmeny = mysqli_query($kapcsolat, $sql);
    $sorok = mysqli_num_rows($eredmeny);

    if ($sorok > 0) {
        while ($sor = mysqli_fetch_array($eredmeny)) {
            $arucikk_id = $sor["arucikk_id"];
            $db = $sor["db"];
            
            $termek = mysqli_query($kapcsolat, "SELECT * FROM arucikk WHERE id=$arucikk_id");
            if (mysqli_num_rows($termek) > 0) {
                $egysor = mysqli_fetch_array($termek);
                $osszeg += $db * $egysor["ar_huf"];
                ?>
                <tr style="border-bottom: 1px solid <?= $sotet ?>;">
                  <td align="right" style="padding: 8px;"><?= $egysor["nev"] ?></td>
                  <td align="left" style="padding: 8px;">x <?= $db ?></td>
                  <td align="right" style="padding: 8px;"><?= szampontos($db * $egysor["ar_huf"]) ?> HUF</td>
                </tr>
                <?php
            }
        }
        ?>
        <tr>
          <td colspan="2" align="right" style="padding: 15px;"><b>Összesen:</b></td>
          <td align="right" style="padding: 15px;"><b><?= szampontos($osszeg) ?> HUF</b></td>
        </tr>
        <tr>
          <td colspan="3" align="center" style="padding: 20px;">
            <?php if ($belepve == 1): ?>
              <button onclick="top.location='1_kosar_tartalma.php'" 
                      style="background:<?= $sotet ?>;color:<?= $vilagos ?>;padding:8px 15px;border:none;cursor:pointer;">
                Vásárlás befejezése
              </button>
            <?php else: ?>
              <button onclick="alert('Előbb be kell jelentkezned!')" 
                      style="background:<?= $sotet ?>;color:<?= $vilagos ?>;padding:8px 15px;border:none;cursor:pointer;">
                Vásárlás befejezése
              </button>
            <?php endif; ?>
            <br><br>
            <button onclick="window.open('kosar.php?torolni=1','kosar')" 
                    style="background:#800000;color:<?= $vilagos ?>;padding:8px 15px;border:none;cursor:pointer;">
              Kosár ürítése
            </button>
          </td>
        </tr>
        <?php
    } else {
        ?>
        <tr>
          <td colspan="3" align="center">
            A kosár üres.
          </td>
        </tr>
        <?php
    }
    ?>
  </table>

  <!-- Iframe méret beállítás -->
  <script>
    const sorok = <?= $sorok ?>;
    const frameHeight = sorok > 0 ? 100 + (sorok * 30) : 100;
    parent.document.getElementById('kosar').style.height = `${frameHeight}px`;
  </script>

</body>
</html>

<?php
mysqli_close($kapcsolat);
ob_end_flush();
?>