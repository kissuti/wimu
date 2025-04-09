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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="styles/btn_gombok.css">
  <link rel="stylesheet" href="styles/teteje.css">
</head>
<body>
  <?php include("teteje.php"); ?>

  <div class="container mt-4">
    <div class="card shadow">
      <div class="card-header" style="background-color: #bbbbbb;">
        <h3 class="mb-0"><i class="bi bi-cart"></i> Kosár tartalma</h3>
      </div>
      
      <div class="card-body">
        <form name="urlap" action="1_kosar_tartalma.php" method="POST">
          <input type="hidden" name="mit" value="modosit">
          <input type="hidden" name="osszeg" value="<?= $osszeg ?>">

          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th class="text-start">Termék</th>
                  <th class="text-end">Egységár</th>
                  <th class="text-center">Mennyiség</th>
                  <th class="text-end">Összesen</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $osszeg = 0;
                $sql = ($belepve == 0) 
                  ? "SELECT * FROM kosar WHERE session_id='" . session_id() . "' AND rendeles_id=0"
                  : "SELECT * FROM kosar WHERE ugyfel_id=$webshop_id AND rendeles_id=0";

                $eredmeny = mysqli_query($kapcsolat, $sql);
                while ($sor = mysqli_fetch_array($eredmeny)) {
                  $kosar_id = $sor["id"];
                  $arucikk_id = $sor["arucikk_id"];
                  $db = $sor["db"];
                  
                  $termek = mysqli_fetch_array(mysqli_query(
                    $kapcsolat, 
                    "SELECT * FROM arucikk WHERE id=".intval($arucikk_id)
                  ));
                  
                  $osszeg += $db * $termek['ar_huf'];
                ?>
                <tr>
                  <td><b><?= $termek['nev'] ?></b> <?= $termek['nev2'] ?></td>
                  <td class="text-end"><?= szampontos($termek['ar_huf']) ?> HUF</td>
                  <td class="text-center">
                    <input name="<?= $kosar_id ?>" 
                           value="<?= $db ?>" 
                           class="form-control text-center" 
                           style="width: 80px; margin-left: 135px">
                  </td>
                  <td class="text-end"><?= szampontos($db * $termek['ar_huf']) ?> HUF</td>
                </tr>
                <?php } ?>
                
                <tr>
                  <td colspan="3" class="text-end"><b>Összesen:</b></td>
                  <td class="text-end"><b><?= szampontos($osszeg) ?> HUF</b></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="row">
            <div class="col-md-12 text-end">
              <?php if($belepve): ?>
                <a href="2_vasarloi_adatok.php" 
                   class="btngombok link-offset-2 link-underline link-underline-opacity-0">
                  <i class="bi bi-credit-card"></i> Tovább a fizetéshez >>
                </a>
              <?php else: ?>
                <div class="alert alert-warning mt-3">
                  A vásárlás befejezéséhez <a href="belepes.php" class="alert-link">jelentkezz be</a>!
                </div>
              <?php endif; ?>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include("alja.php"); ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
} elseif ($mit == "modosit") {
  // [A meglévő módosító logika maradjon változatlan]
}

mysqli_close($kapcsolat);
ob_end_flush();
?>