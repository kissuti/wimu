<?php
ob_start();

include("php/dbconn.php");
include("php/fuggvenyek.php");

header("Pragma: no-cache"); 
Header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

if (!isset($mit)) {
  ?>

  <html>

  <head>
    <title>Wimu Webshop</title>
    <meta charset="UTF-8">
    <meta name="cache-control" content="private, no-store, no-cache, must-revalidate" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/preloader.css">
    <meta http-equiv="Content-Type" content="text/html"/>
  </head>

  <?php include("teteje_2.php"); ?>

  <div class="container mt-4">
    <div class="d-flex justify-content-end mb-3">
      <a href="index.php" class="text-decoration-none text-dark">Visszatérés a webshop-hoz</a>
    </div>
    <h2 class="text-dark">A kosár tartalma</h2>
    
    <form name="urlap" action="1_kosar_tartalma.php" method="POST">
      <input type="hidden" name="mit" value="modosit">
      <input type="hidden" name="osszeg" value="<?= $osszeg ?>">

      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="thead-dark">
            <tr>
              <th>A termék megnevezése</th>
              <th class="text-center">Készlet</th>
              <th class="text-end">Egységár</th>
              <th class="text-center">Darabszám</th>
              <th class="text-end">Összeg</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $osszeg = 0;
            if ($belepve == 0) {
              $sql = "SELECT * FROM kosar WHERE session_id='" . session_id() . "' AND rendeles_id=0";
            } else {
              $sql = "SELECT * FROM kosar WHERE ugyfel_id=$webshop_id AND rendeles_id=0";
            }

            $eredmeny = mysqli_query($kapcsolat, $sql);
            while ($sor = mysqli_fetch_array($eredmeny)) {
              $kosar_id = $sor["id"];
              $arucikk_id = $sor["arucikk_id"];
              $db = $sor["db"];
              
              $parancs = "SELECT * FROM arucikk WHERE id=$arucikk_id";
              $rs = mysqli_query($kapcsolat, $parancs);

              if (mysqli_num_rows($rs) > 0) {
                $egysor = mysqli_fetch_array($rs);
                $nev = $egysor["nev"];
                $nev2 = $egysor["nev2"];
                $ar_huf = $egysor["ar_huf"];
                $raktaron = $egysor["raktaron"];
                $egyseg = $egysor["egyseg"];
                $osszeg += $db * $ar_huf;
                ?>
                <tr>
                  <td><b><?= $nev ?></b> <?= $nev2 ?></td>
                  <td class="text-center">
                    <?php if ($raktaron > 0) { ?>
                      <?= $raktaron ?> <?= $egyseg ?>
                    <?php } else { ?>
                      <span class="text-danger"><b>Elfogyott!</b></span>
                    <?php } ?>
                  </td>
                  <td class="text-end"><?= szampontos($ar_huf) ?> HUF</td>
                  <td class="text-center">
                    <input name="<?= $kosar_id ?>" value="<?= $db ?>" class="form-control text-center" style="width: 60px;">
                  </td>
                  <td class="text-end"><?= szampontos($db * $ar_huf) ?> HUF</td>
                </tr>
                <?php
              }
            }
            ?>
            <tr>
              <td colspan="4" class="text-end"><b>Összesen:</b></td>
              <td class="text-end"><b><?= szampontos($osszeg) ?> HUF</b></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="d-flex justify-content-between mt-3">
        <button type="button" class="btn btn-dark" onclick="document.urlap.submit()">Kosár frissítése</button>
        <button type="button" class="btn btn-success" onclick="top.location='2_vasarloi_adatok.php'">A vásárlói adatok megadása >></button>
      </div>
    </form>
  </div>

  <?php include("alja_2.php"); ?>

  <?php
} elseif ($mit == "modosit") {
  foreach ($_POST as $nev => $ertek) {
    if ($nev != "mit") {
      if ($ertek == 0) {
        $sql = "DELETE FROM kosar WHERE id=$nev";
      } else {
        $sql = "UPDATE kosar SET db=$ertek WHERE id=$nev";
      }
      mysqli_query($kapcsolat, $sql);
    }
  }

  header('Location:1_kosar_tartalma.php');
}

mysqli_close($kapcsolat);
ob_end_flush();
?>