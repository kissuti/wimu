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

$jogosultsag = isset($_POST['jogosultsag']) ? $_POST['jogosultsag'] : 0;
$kat1 = isset($_POST['kat1']) ? intval($_POST['kat1']) : 0;
$kat2 = isset($_POST['kat2']) ? intval($_POST['kat2']) : 0;
$kat3 = isset($_POST['kat3']) ? intval($_POST['kat3']) : 0;
$szint = isset($_POST['szint']) ? intval($_POST['szint']) : 0;
$mitkeres = isset($_POST['mitkeres']) ? $_POST['mitkeres'] : "";
$leirasban = isset($_POST['leirasban']) ? intval($_POST['leirasban']) : 0;
$sorrend = isset($_POST['sorrend']) ? $_POST['sorrend'] : "";
$irany = isset($_POST['irany']) ? $_POST['irany'] : "";
$oldal = isset($_POST['oldal']) ? intval($_POST['oldal']) : 1;
$laponkent = isset($_POST['laponkent']) ? intval($_POST['laponkent']) : 5;

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

<html>
<head>
  <title>Wimu Webshop</title>
  <meta name="cache-control" content="private, no-store, no-cache, must-revalidate" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="styles/index.css" />
  <link rel="stylesheet" href="styles/preloader.css">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
  <script>
    // Az add-to-cart űrlapokhoz használt jQuery kód
    $(document).ready(function() {
      $("form.add-to-cart").submit(function(event) {
        event.preventDefault();
        var form = $(this);
        $.ajax({
          type: form.attr('method'),
          url: form.attr('action'),
          data: form.serialize(),
          success: function(response) {
            alert("A termék sikeresen hozzáadva a kosárhoz!");
            $("#kosar").attr("src", "kosar.php"); // Kosár iframe frissítése
          },
          error: function() {
            alert("Hiba történt a termék kosárba helyezése során.");
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
      <!-- Webshop tartalma -->
      <form name="listazas" action="index.php" method="POST" class="bg-light p-3 rounded">
        <input type="hidden" name="szint" value="0">
        <input type="hidden" name="jogosultsag" value="<?= $jogosultsag ?>">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="kat1" class="form-label"><b>Kategóriák:</b></label>
            <select name="kat1" class="form-select" style="width: 100%;" onChange="document.listazas.szint.value=1; document.listazas.submit()">
              <option value="0" <?php if ($kat1==0) { print "selected"; } ?>>Összes termék</option>
              <?php
              $sql = "SELECT * FROM kategoriak WHERE szulo1=0 ORDER BY id";
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
              <input type="text" name="text" class="keresesmezo" placeholder="Keresés..." value="<?= $mitkeres ?>">
              <span class="keresesicon"> 
                <svg width="19px" height="19px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path opacity="1" d="M14 5H20" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path opacity="1" d="M14 8H17" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M21 11.5C21 16.75 16.75 21 11.5 21C6.25 21 2 16.75 2 11.5C2 6.25 6.25 2 11.5 2" stroke="#000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"></path> <path opacity="1" d="M22 22L20 20" stroke="#000" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
              </span>
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
            <select name="kat2" class="form-select" onChange="document.listazas.szint.value=1; document.listazas.submit()">
              <option value="0" <?php if ($kat2==0) { print "selected"; } ?>>Összes</option>
              <?php
              $sql = "SELECT * FROM kategoriak WHERE szulo1=$kat1 AND szulo2=0 ORDER BY id";
              $eredmeny = mysqli_query($kapcsolat, $sql);
              while ($sor = mysqli_fetch_array($eredmeny)) {
                $id = $sor["id"];
                $nev = $sor["nev"];
                ?>
                <option value="<?= $id ?>" <?php if ($kat2==$id) { print "selected"; } ?>><?= $nev ?></option>
                <?php
              }
              ?>
            </select>
          </div>
        <?php } ?>

        <?php if ($kat2>0) { ?>
          <div class="mb-3">
            <label for="kat3" class="form-label"><b>Alkategóriák:</b></label>
            <select name="kat3" class="form-select" onChange="document.listazas.submit()">
              <option value="0" <?php if ($kat3==0) { print "selected"; } ?>>összes</option>
              <?php
              $sql = "SELECT * FROM kategoriak WHERE szulo1=$kat1 AND szulo2=$kat2 ORDER BY id";
              $eredmeny = mysqli_query($kapcsolat, $sql);
              while ($sor = mysqli_fetch_array($eredmeny)) {
                $id = $sor["id"];
                $nev = $sor["nev"];
                ?>
                <option value="<?= $id ?>" <?php if ($kat3==$id) { print "selected"; } ?>><?= $nev ?></option>
                <?php
              }
              ?>
            </select>
          </div>
        <?php } ?>
      </form>

      <table class="table table-bordered table-hover table-responsive rounded-0">
        <thead class="thead-light">
          <tr>
            <th scope="col">Kép</th>
            <th scope="col">Termék</th>
            <th scope="col">Ár</th>
          </tr>
        </thead>
        <tbody>
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
              <tr>
                <td class="text-center">
                  <?php if ($webshop_role=="admin") { ?>
                    <a href="php/admin/termek_modositas2.php?id=<?= $id ?>"><img src="img/<?= $foto ?>" class="img-fluid" alt="Kattints ide a termék módosításához!"></a>
                  <?php } else { ?>
                    <a href="img/<?= $foto ?>" target="kepablak"><img src="img/<?= $foto ?>" class="img-fluid" alt="Kattints ide a nagy kép megtekintéséhez!"></a>
                  <?php } ?>
                </td>
                <td>
                  <a href="leiras.php?id=<?= $id ?>&oldal=<?= $oldal ?>&laponkent=<?= $laponkent ?>&kat1=<?= $kat1 ?>&kat2=<?= $kat2 ?>&kat3=<?= $kat3 ?>&jogosultsag=<?= $jogosultsag ?>&mitkeres=<?= $mitkeres ?>&irany=<?= $irany ?>" class="text-dark font-weight-bold"><?= $nev ?></a><br>
                  <small class="text-muted"><?= $nev2 ?></small>
                  <div class="mt-2 mb-2"><?= $leiras ?></div>
                  <a href="leiras.php?id=<?= $id ?>&oldal=<?= $oldal ?>&laponkent=<?= $laponkent ?>&kat1=<?= $kat1 ?>&kat2=<?= $kat2 ?>&kat3=<?= $kat3 ?>&jogosultsag=<?= $jogosultsag ?>&mitkeres=<?= $mitkeres ?>&irany=<?= $irany ?>" class="btn btn-link p-0">részletesebb tájékoztató...</a>
                </td>
                <td class="text-center">
                  <form class="add-to-cart" action="kosarba_tesz.php" method="POST">
                    <input type="hidden" name="arucikk_id" value="<?= $id ?>">
                    <div class="h5 font-weight-bold"><?= szampontos($ar_huf) ?> HUF</div>
                    <input name="db" class="form-control text-center" style="width: 60px; display: inline-block;" value="1"> <?= $egyseg ?><br><br>
                    <button type="submit" class="kosarbtn"><span>Kosárba</span></button>
                    <br><br>
                    <?php if ($raktaron>0) { ?>
                      <span class="badge badge-success">Raktáron: <?= $raktaron ?> <?= $egyseg ?></span>
                    <?php } else { ?>
                      <span class="badge badge-danger">Elfogyott!</span>
                    <?php } ?>
                  </form>
                </td>
              </tr>
              <?php
            }
          ?>
        </tbody>
      </table>
    </div>

    <?php include("alja.php"); ?>
  </div>
</body>
</html>

<?php
mysqli_close($kapcsolat);
ob_end_flush();
?>
