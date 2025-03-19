<?php
ob_start();

include("../dbconn.php");
include("../fuggvenyek.php");

header("Pragma: no-cache");
header("Cache-control: private, no-store, no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// Változók beolvasása a $_REQUEST tömbből
$kat1 = isset($_REQUEST['kat1']) ? intval($_REQUEST['kat1']) : 0;
$kat2 = isset($_REQUEST['kat2']) ? intval($_REQUEST['kat2']) : 0;
$kat3 = isset($_REQUEST['kat3']) ? intval($_REQUEST['kat3']) : 0;
$szint = isset($_REQUEST['szint']) ? intval($_REQUEST['szint']) : 0;
$mit   = isset($_REQUEST['mit'])   ? $_REQUEST['mit'] : "lista";

// Szint beállítások
if ($szint == 1) {
  $kat2 = 0;
  $kat3 = 0;
}
if ($szint == 2) {
  $kat3 = 0;
}
?>
<html>
<head>
  <meta name="cache-control" content="private, no-store, no-cache, must-revalidate">
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <title>Kategóriák</title>
</head>
<body style="font-family:tahoma">
  <div class="container mt-4">
    <h1 class="mb-4">Kategóriák adminisztrációja</h1>
    <a href="../admin-index.php" class="btn btn-primary btn-danger">Vissza az admin főoldalra</a>

    <?php
    if ($mit=="lista") {
      // Alapértelmezett értékek
      $neve = "";
      $idja = 0;
      ?>
      <form name="urlap" id="urlap" action="kategoriak.php" method="POST" class="bg-light p-4 rounded">
        <input type="hidden" name="mit" value="lista">
        <input type="hidden" name="szint" value="0">

        <div class="mb-3">
          <label for="kat1" class="form-label"><b>1. szint:</b></label>
          <select name="kat1" class="form-select" onChange="if (this.value != '0') { document.getElementById('urlap').szint.value=1; document.getElementById('urlap').submit(); }">
            <option value="0" <?php if ($kat1==0) { echo "selected"; } ?>>Új 1. szintű kategória felvétele</option>
            <?php
            $sql = "SELECT * FROM kategoriak WHERE szulo1=0 ORDER BY id";
            $eredmeny = mysqli_query($kapcsolat, $sql);
            while ($sor = mysqli_fetch_array($eredmeny)) {
              $id = $sor["id"];
              $nev = $sor["nev"];
              ?>
              <option value="<?= $id ?>" <?php if ($kat1==$id) { echo "selected"; $neve=$nev; $idja=$id; } ?>><?= $nev ?></option>
              <?php
            }
            ?>
          </select>
        </div>

        <?php
        if ($kat1 > 0) {
          $sql = "SELECT * FROM kategoriak WHERE szulo1=$kat1 AND szulo2=0 ORDER BY id";
          $eredmeny = mysqli_query($kapcsolat, $sql);
          ?>
          <div class="mb-3">
            <label for="kat2" class="form-label"><b>2. szint:</b></label>
            <select name="kat2" class="form-select" onChange="document.getElementById('urlap').szint.value=2; document.getElementById('urlap').submit()">
              <option value="0" <?php if ($kat2==0) { echo "selected"; } ?>>Új 2. szintű kategória felvétele</option>
              <?php
              while ($sor = mysqli_fetch_array($eredmeny)) {
                $id = $sor["id"];
                $nev = $sor["nev"];
                ?>
                <option value="<?= $id ?>" <?php if ($kat2==$id) { echo "selected"; $neve=$nev; $idja=$id; } ?>><?= $nev ?></option>
                <?php
              }
              ?>
            </select>
          </div>
          <?php
        }
        ?>

        <?php
        if ($kat2 > 0) {
          $sql = "SELECT * FROM kategoriak WHERE szulo1=$kat1 AND szulo2=$kat2 ORDER BY id";
          $eredmeny = mysqli_query($kapcsolat, $sql);
          ?>
          <div class="mb-3">
            <label for="kat3" class="form-label"><b>3. szint:</b></label>
            <select name="kat3" class="form-select" onChange="document.getElementById('urlap').submit()">
              <option value="0" <?php if ($kat3==0) { echo "selected"; } ?>>Új 3. szintű kategória felvétele</option>
              <?php
              while ($sor = mysqli_fetch_array($eredmeny)) {
                $id = $sor["id"];
                $nev = $sor["nev"];
                ?>
                <option value="<?= $id ?>" <?php if ($kat3==$id) { echo "selected"; $neve=$nev; $idja=$id; } ?>><?= $nev ?></option>
                <?php
              }
              ?>
            </select>
          </div>
          <?php
        }
        ?>

        <input type="hidden" name="idja" value="<?= $idja ?>">

        <div class="mb-3">
          <label for="neve" class="form-label">Kategória neve:</label>
          <input name="neve" class="form-control" value="<?= $neve ?>">
        </div>

        <div class="mb-3">
          <button type="button" class="btn btn-primary" onClick="document.getElementById('urlap').mit.value='felvesz'; document.getElementById('urlap').submit();">Felvétel</button>
          <button type="button" class="btn btn-warning" onClick="document.getElementById('urlap').mit.value='modosit'; document.getElementById('urlap').submit();">Módosítás</button>
          <button type="button" class="btn btn-danger" onClick="document.getElementById('urlap').mit.value='torles'; document.getElementById('urlap').submit();">Törlés</button>
        </div>
      </form>
      <?php
    } // vége a "lista" ágának

    // FELVÉTEL
    elseif ($mit=="felvesz") {
      // A $_REQUEST-ből már elérhető a "neve" is
      $neve = isset($_REQUEST['neve']) ? $_REQUEST['neve'] : "";
      if (!empty($neve)) {
        $sql = "INSERT INTO kategoriak (nev, szulo1, szulo2) VALUES ('$neve', $kat1, $kat2)";
        mysqli_query($kapcsolat, $sql);
        $new_id = mysqli_insert_id($kapcsolat);

        if ($szint == 0) {
          $kat1 = $new_id;
          $szint = 1;
        } elseif ($szint == 1) {
          $kat2 = $new_id;
          $szint = 2;
        } elseif ($szint == 2) {
          $kat3 = $new_id;
        }
      }
      header("Location: kategoriak.php?kat1=$kat1&kat2=$kat2&kat3=$kat3&szint=$szint");
      exit();
    }
    // MÓDOSÍTÁS
    elseif ($mit=="modosit") {
      $neve = isset($_REQUEST['neve']) ? $_REQUEST['neve'] : "";
      $idja = isset($_REQUEST['idja']) ? intval($_REQUEST['idja']) : 0;
      if ($idja > 0 && !empty($neve)) {
        $sql = "UPDATE kategoriak SET nev='$neve' WHERE id=$idja";
        mysqli_query($kapcsolat, $sql);
      }
      $kat1 = isset($_POST['kat1']) ? intval($_POST['kat1']) : 0;
      $kat2 = isset($_POST['kat2']) ? intval($_POST['kat2']) : 0;
      $kat3 = isset($_POST['kat3']) ? intval($_POST['kat3']) : 0;
      $szint = isset($_POST['szint']) ? intval($_POST['szint']) : 0;
      header("Location: kategoriak.php?kat1=$kat1&kat2=$kat2&kat3=$kat3&szint=$szint");
      exit();
    }
    // TÖRLÉS
    elseif ($mit=="torles") {
      $idja = isset($_REQUEST['idja']) ? intval($_REQUEST['idja']) : 0;
      if ($idja > 0) {
        $sql = "SELECT * FROM arucikk WHERE (kat1=$idja OR kat2=$idja OR kat3=$idja)";
        $eredmeny = mysqli_query($kapcsolat, $sql);
        if (mysqli_num_rows($eredmeny)==0) {
          $sql = "SELECT * FROM kategoriak WHERE (szulo1=$idja OR szulo2=$idja)";
          $eredmeny = mysqli_query($kapcsolat, $sql);
          if (mysqli_num_rows($eredmeny)==0) {
            $sql = "DELETE FROM kategoriak WHERE id=$idja";
            mysqli_query($kapcsolat, $sql);
          }
        }
      }
      header("Location: kategoriak.php");
    }

    mysqli_close($kapcsolat);
    ob_end_flush();
    ?>
  </div>
</body>
</html>
