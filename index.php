<?php
ob_start();

include("php/dbconn.php");
include("php/fuggvenyek.php");

header("Pragma: no-cache"); 
header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

$egyhete = date("Y-m-d", time()-86400*7);
$sql = "DELETE FROM kosar WHERE rendeles_id=0 AND mikor<'$egyhete'";
mysqli_query($kapcsolat, $sql);

$haromnapja = date("Y-m-d", time()-86400*3);
$sql = "DELETE FROM megtekintve WHERE mikor<'$haromnapja'";
mysqli_query($kapcsolat, $sql);

$jogosultsag = isset($_REQUEST['jogosultsag']) ? $_REQUEST['jogosultsag'] : 0;
$kat1 = isset($_REQUEST['kat1']) ? intval($_REQUEST['kat1']) : 0;
$kat2 = isset($_REQUEST['kat2']) ? intval($_REQUEST['kat2']) : 0;
$kat3 = isset($_REQUEST['kat3']) ? intval($_REQUEST['kat3']) : 0;
$szint = isset($_REQUEST['szint']) ? intval($_REQUEST['szint']) : 0;
$mitkeres = isset($_REQUEST['mitkeres']) ? $_REQUEST['mitkeres'] : "";
$leirasban = isset($_REQUEST['leirasban']) ? intval($_REQUEST['leirasban']) : 0;
$sorrend = isset($_REQUEST['sorrend']) ? $_REQUEST['sorrend'] : "";
$irany = isset($_REQUEST['irany']) ? $_REQUEST['irany'] : "";
$oldal = isset($_REQUEST['oldal']) ? intval($_REQUEST['oldal']) : 1;
$laponkent = isset($_REQUEST['laponkent']) ? intval($_REQUEST['laponkent']) : 6;

if ($szint == 1) {
  $kat2 = 0;
  $kat3 = 0;
}
if ($szint == 2) {
  $kat3 = 0;
}

$katszuro = "id>0";
if ($kat1 > 0) {
  $katszuro = "kat1=$kat1";
}
if ($kat2 > 0) {
  $katszuro = "kat1=$kat1 AND kat2=$kat2";
}
if ($kat3 > 0) {
  $katszuro = "kat1=$kat1 AND kat2=$kat2 AND kat3=$kat3";
}

$rendezes = "id";
if ($sorrend == "ar") {
  $rendezes = "ar_huf";
} elseif ($sorrend == "abc") {
  $rendezes = "nev";
} elseif ($sorrend == "nepszeruseg") {
  $rendezes = "id";
}

$mettol = ($oldal - 1) * $laponkent;

if ($sorrend == "nepszeruseg") {
  $laponkent = 999999;
  $sql = "SELECT arucikk_id, sum(db) as hanydarab FROM kosar WHERE rendeles_id>0 GROUP BY arucikk_id";
  $eredmeny = mysqli_query($kapcsolat, $sql);
  $osszes = 0;
  while ($sor = mysqli_fetch_array($eredmeny)) {
    $arucikk_id = $sor["arucikk_id"];
    $sql = "SELECT * FROM arucikk WHERE id=$arucikk_id AND $katszuro";
    $eredm = mysqli_query($kapcsolat, $sql);
    if (mysqli_num_rows($eredm) > 0) {
      $osszes += 1;
    }
  }
} else {
  if ($mitkeres == "") {
    $sql = "SELECT count(*) as osszes FROM arucikk WHERE $katszuro";
  } else {
    if ($leirasban == 1) {
      $sql = "SELECT count(*) as osszes FROM arucikk WHERE (nev LIKE '%$mitkeres%' OR nev2 LIKE '%$mitkeres%' OR rovidnev LIKE '%$mitkeres%' OR leiras LIKE '%$mitkeres%' OR hosszu_leiras LIKE '%$mitkeres%') AND $katszuro";
    } else {
      $sql = "SELECT count(*) as osszes FROM arucikk WHERE (nev LIKE '%$mitkeres%' OR nev2 LIKE '%$mitkeres%' OR rovidnev LIKE '%$mitkeres%') AND $katszuro";
    }
  }
  $eredmeny = mysqli_query($kapcsolat, $sql);
  $sor = mysqli_fetch_array($eredmeny);
  $osszes = $sor["osszes"];
  // A webshop szerepkörét a POST-ból kapott jogosultság alapján állítjuk be
  $webshop_role = ($jogosultsag == 1) ? "admin" : "user";
}

$oldalak = ceil($osszes / $laponkent);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Wimu Webshop</title>
  <meta name="cache-control" content="private, no-store, no-cache, must-revalidate" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles/index.css" />
  <link rel="stylesheet" href="styles/preloader.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
  <script>
    $(document).ready(function() {
  $("form.add-to-cart").off("submit").on("submit", function(event) {
    event.preventDefault();
    var form = $(this);
    var button = form.find('button');
    button.prop('disabled', true);
    
    console.log("Submit event triggered");
    
    $.ajax({
      type: form.attr('method'),
      url: form.attr('action'),
      data: form.serialize(),
      success: function(response) {
        alert("A termék sikeresen hozzáadva a kosárhoz!");
        $("#kosar").attr("src", "kosar.php?t=" + new Date().getTime());
        button.prop('disabled', false);
      },
      error: function(xhr) {
        var errorMsg = xhr.status + ': ' + xhr.statusText;
        if (xhr.responseText) {
          errorMsg += ' - ' + xhr.responseText;
        }
        alert("Hiba történt: " + errorMsg);
        button.prop('disabled', false);
      }
    });
  });
});

  </script>
  <script src="js/preloader.js"></script>
</head>
<body style="background-color: #F0EFE7;">
  <!-- Preloader -->
  <div id="preloader">
    <div class="spinner"></div>
  </div>

  <!-- Az oldal tartalmát egy konténerbe csomagoljuk -->
  <div id="main-content">
    <?php include("teteje.php"); ?>

    <div class="container mt-4">
      <!-- Webshop tartalma: kereső és kategória szűrők -->
      <form name="listazas" action="index.php" method="GET" class="bg-light p-3 rounded">
        <input type="hidden" name="szint" value="0">
        <input type="hidden" name="jogosultsag" value="<?= $jogosultsag ?>">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="kat1" class="form-label"><b>Kategóriák:</b></label>
            <select name="kat1" class="form-select" style="width: 100%;" onChange="document.listazas.szint.value=1; document.listazas.submit()">
              <option value="0" <?php if ($kat1==0) { print "selected"; } ?>>Összes termék</option>
              <?php
              $sql = "SELECT * FROM kategoriak WHERE szulo1 IS NULL ORDER BY id"; 
              $eredmeny = mysqli_query($kapcsolat, $sql);
              while ($sor = mysqli_fetch_array($eredmeny)) {
                $id = $sor["id"];
                $nev = $sor["nev"];
                ?>
                <option value="<?= $id ?>" <?php if ($kat1 == $id) { print "selected"; } ?>><?= $nev ?></option>
                <?php
              }
              ?>
            </select>
          </div>
          <div class="col-md-6 mb-3">
            <!-- Keresés eleje -->
            <label for="mitkeres" class="form-label"><b>Keresés:</b></label>
            <div class="keresesmezo-container">
              <input placeholder="Keresés..." type="text" name="mitkeres" class="keresomezo" value="<?= $mitkeres ?>">
            </div>
            <!-- Keresés vége -->
            <div class="form-check mt-2">
              <input type="checkbox" name="leirasban" value="1" class="form-check-input" <?php if ($leirasban==1) { print "checked"; } ?>>
              <label class="form-check-label">keresés a termékleírásban is</label>
            </div>
          </div>
        </div>

        <?php if ($kat1>0) { ?>
          <div class="mb-3">
            <label for="kat2" class="form-label"><b>Alkategóriák:</b></label>
            <select name="kat2" class="form-select" onChange="window.location.href='index.php?kat1=<?= $kat1 ?>&kat2=' + this.value + '&szint=2';">
              <option value="0" <?php if ($kat2==0) { echo "selected"; } ?>>Összes</option>
              <?php
              $sql = "SELECT * FROM kategoriak WHERE szulo1=$kat1 AND szulo2 IS NULL ORDER BY id";
              $eredmeny = mysqli_query($kapcsolat, $sql);
              while ($sor = mysqli_fetch_array($eredmeny)) {
                $id = $sor["id"];
                $nev = $sor["nev"];
                ?>
                <option value="<?= $id ?>" <?php if ($kat2==$id) { echo "selected"; } ?>><?= $nev ?></option>
                <?php
              }
              ?>
            </select>
          </div>
        <?php } ?>

        <?php if ($kat2>0) { ?>
          <div class="mb-3">
            <label for="kat3" class="form-label"><b>Alkategóriák:</b></label>
            <select name="kat3" class="form-select" onChange="window.location.href='index.php?kat1=<?= $kat1 ?>&kat2=<?= $kat2 ?>&kat3=' + this.value + '&szint=3';">
              <option value="0" <?php if ($kat3==0) { echo "selected"; } ?>>Összes</option>
              <?php
              $sql = "SELECT * FROM kategoriak WHERE szulo1=$kat1 AND szulo2=$kat2 ORDER BY id";
              $eredmeny = mysqli_query($kapcsolat, $sql);
              while ($sor = mysqli_fetch_array($eredmeny)) {
                $id = $sor["id"];
                $nev = $sor["nev"];
                ?>
                <option value="<?= $id ?>" <?php if ($kat3==$id) { echo "selected"; } ?>><?= $nev ?></option>
                <?php
              }
              ?>
            </select>
          </div>
        <?php } ?>
      </form>

      <!-- Termékek megjelenítése kártya formában -->
      <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php
          if ($mitkeres=="") {
            $sql = "SELECT * FROM arucikk WHERE $katszuro ORDER BY $rendezes $irany LIMIT $mettol, $laponkent";
          } else {
            if ($leirasban==1) {
              $sql = "SELECT * FROM arucikk WHERE (nev LIKE '%$mitkeres%' OR nev2 LIKE '%$mitkeres%' OR rovidnev LIKE '%$mitkeres%' OR leiras LIKE '%$mitkeres%' OR hosszu_leiras LIKE '%$mitkeres%') AND $katszuro ORDER BY $rendezes $irany LIMIT $mettol, $laponkent";
            } else {
              $sql = "SELECT * FROM arucikk WHERE (nev LIKE '%$mitkeres%' OR nev2 LIKE '%$mitkeres%' OR rovidnev LIKE '%$mitkeres%') AND $katszuro ORDER BY $rendezes $irany LIMIT $mettol, $laponkent";
            }
          }
          $eredmeny = mysqli_query($kapcsolat, $sql);
          while ($sor = mysqli_fetch_array($eredmeny)) {
            $id = $sor["id"];
            $nev = $sor["nev"];
            $nev2 = $sor["nev2"];
            $foto = $sor["foto"];
            $raktaron = $sor["raktaron"];
            $leiras = $sor["leiras"];
            $ar_huf = $sor["ar_huf"];
            $egyseg = $sor["egyseg"];
        ?>
          <div class="col">
        <div class="card h-100">
          <?php if ($webshop_role=="admin") { ?>
            <a href="php/admin/termek_modositas2.php?id=<?= $id ?>">
              <img src="img/<?= $foto ?>" class="card-img-top" alt="Termékkép">
            </a>
          <?php } else { ?>
            <a href="img/<?= $foto ?>" target="kepablak">
              <img src="img/<?= $foto ?>" class="card-img-top" alt="Nagy kép">
            </a>
          <?php } ?>
          
          <div class="card-body">
            <h5 class="card-title"><?= $nev ?></h5>
            <h6 class="card-subtitle mb-2 text-muted"><?= $nev2 ?></h6>
            <div class="mt-2 mb-2"><?= $leiras ?></div>
            
            <div class="mt-2">
            <?php if ($raktaron > 0) { ?>
              <form class="add-to-cart d-inline" action="kosarba_tesz.php" method="POST">
                <input type="hidden" name="arucikk_id" value="<?= $id ?>">
                <div class="input-group mb-3" style="max-width: 100px;">
                  <input type="number" name="db" class="form-control" value="1" min="1" max="<?= $raktaron ?>">
                </div>
                <div class="mt-2">
                <div class="h5 font-weight-bold"><?= szampontos($ar_huf) ?> HUF</div>
                  <button type="submit" class="w-100 kosarbtn p-2 mb-1">
                    <span>Kosárba <i class="bi bi-cart"></i></span>
                  </button>
                  <span class="badge bg-success mt-1 d-block">Raktáron: <?= $raktaron ?> <?= $egyseg ?></span>
                </div>
              </form>
            <?php } else { ?>
              <div class="alert alert-warning">Jelenleg nem elérhető</div>
            <?php } ?>
          </div>
            
            <a href="leiras.php?id=<?= $id ?>&oldal=<?= $oldal ?>&laponkent=<?= $laponkent ?>&kat1=<?= $kat1 ?>&kat2=<?= $kat2 ?>&kat3=<?= $kat3 ?>&jogosultsag=<?= $jogosultsag ?>&mitkeres=<?= $mitkeres ?>&irany=<?= $irany ?>" class="btn btn-link p-0">részletesebben...</a>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>
<!-- Lapozás (külön form) -->
<form action="index.php" method="POST">
  <input type="hidden" name="jogosultsag" value="<?= $jogosultsag ?>">
  <input type="hidden" name="kat1" value="<?= $kat1 ?>">
  <input type="hidden" name="kat2" value="<?= $kat2 ?>">
  <input type="hidden" name="kat3" value="<?= $kat3 ?>">
  <input type="hidden" name="mitkeres" value="<?= htmlspecialchars($mitkeres) ?>">
  <input type="hidden" name="leirasban" value="<?= $leirasban ?>">
  <input type="hidden" name="sorrend" value="<?= $sorrend ?>">
  <input type="hidden" name="irany" value="<?= $irany ?>">
  <input type="hidden" name="laponkent" value="<?= $laponkent ?>">
  
  <nav class="mt-4">
    <ul class="pagination justify-content-center">
      <?php for ($i = 1; $i <= $oldalak; $i++): ?>
        <li class="page-item <?= ($i == $oldal) ? 'active' : '' ?>">
          <button type="submit" name="oldal" value="<?= $i ?>" class="btn btn-outline-success"><?= $i ?></button>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</form> 

    <?php include("alja.php"); ?>
  </div>
    <!-- Bootstrap JS bundle (Popper included) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
          crossorigin="anonymous"></script>
</body>
</html>

<?php
mysqli_close($kapcsolat);
ob_end_flush();
?>
