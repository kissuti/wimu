<?php
ob_start();

include("../dbconn.php");
include("../fuggvenyek.php");

header("Pragma: no-cache");
header("Cache-control: private, no-store, no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// Alapértelmezett értékek beolvasása a $_REQUEST tömbből
$id    = isset($_REQUEST['id'])    ? intval($_REQUEST['id']) : 0;
$mit   = isset($_REQUEST['mit'])   ? $_REQUEST['mit'] : "urlap";
$kat1  = isset($_REQUEST['kat1'])  ? intval($_REQUEST['kat1']) : 0;
$kat2  = isset($_REQUEST['kat2'])  ? intval($_REQUEST['kat2']) : 0;
$kat3  = isset($_REQUEST['kat3'])  ? intval($_REQUEST['kat3']) : 0;
$szint = isset($_REQUEST['szint']) ? intval($_REQUEST['szint']) : 0;

// A kategóriák szintjeinek beállítása
if ($szint == 1) {
    $kat2 = 0;
    $kat3 = 0;
} elseif ($szint == 2) {
    $kat3 = 0;
}

if ($mit == "urlap") {
    // Csak akkor próbáljuk lekérni az adatokat, ha az ID érvényes
    if ($id > 0) {
        $sql = "SELECT * FROM arucikk WHERE id = ?";
        $stmt = $kapcsolat->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $eredmeny = $stmt->get_result();
        if ($sor = $eredmeny->fetch_array()) {
            // A termék adatai az adatbázisból
            $nev           = $sor["nev"];
            $nev2          = $sor["nev2"];
            $rovidnev      = $sor["rovidnev"];
            $ar_huf        = $sor["ar_huf"];
            $raktaron      = $sor["raktaron"];
            $egyseg        = $sor["egyseg"];
            $leiras        = $sor["leiras"];
            $hosszu_leiras = $sor["hosszu_leiras"];
            $kat1          = $sor["kat1"];
            $kat2          = $sor["kat2"];
            $kat3          = $sor["kat3"];
        } else {
            echo "<div class='alert alert-danger'><b>Nincs olyan termék, amelynek az ID-je: $id</b></div>";
            exit;
        }
    } else {
        echo "<div class='alert alert-danger'><b>Érvénytelen termék ID!</b></div>";
        exit;
    }
}

if ($mit == "urlap" && $id > 0) {
    ?>
    <html>
    <head>
      <meta name="cache-control" content="private, no-store, no-cache, must-revalidate">
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
      <title>Termék Módosítás</title>
    </head>
    <body style="font-family: tahoma;">
      <div class="container mt-4">
        <h1 class="mb-4 text-primary">Termék adatainak módosítása</h1>
        <form name="urlap" action="termek_modositas.php" method="POST" enctype="multipart/form-data" class="bg-light p-4 rounded">
          <input type="hidden" name="id" value="<?= $id ?>">
          <input type="hidden" name="mit" value="urlap">
          <input type="hidden" name="szint" value="0">
          
          <div class="mb-3">
            <label for="kat1" class="form-label"><b>1. szint:</b></label>
            <select name="kat1" class="form-select" onChange="document.urlap.szint.value=1; document.urlap.submit()">
              <option value="0" <?php if ($kat1 == 0) { echo "selected"; } ?>>válaszd ki a fő kategóriát</option>
              <?php
              $sql = "SELECT * FROM kategoriak WHERE szulo1 = 0 ORDER BY id";
              $eredmeny = mysqli_query($kapcsolat, $sql);
              while ($sor = mysqli_fetch_array($eredmeny)) {
                  $k_id = $sor["id"];
                  $k_nev = $sor["nev"];
                  ?>
                  <option value="<?= $k_id ?>" <?php if ($kat1 == $k_id) { echo "selected"; } ?>><?= $k_nev ?></option>
                  <?php
              }
              ?>
            </select>
          </div>

          <?php
          if ($kat1 > 0) {
              $sql = "SELECT * FROM kategoriak WHERE szulo1 = ? AND szulo2 = 0 ORDER BY id";
              $stmt = $kapcsolat->prepare($sql);
              $stmt->bind_param("i", $kat1);
              $stmt->execute();
              $eredmeny = $stmt->get_result();
              if ($eredmeny->num_rows > 0) {
                  ?>
                  <div class="mb-3">
                    <label for="kat2" class="form-label"><b>2. szint:</b></label>
                    <select name="kat2" class="form-select" onChange="document.urlap.szint.value=2; document.urlap.submit()">
                      <option value="0" <?php if ($kat2 == 0) { echo "selected"; } ?>>válassz kategóriát</option>
                      <?php
                      while ($sor = $eredmeny->fetch_array()) {
                          $k_id = $sor["id"];
                          $k_nev = $sor["nev"];
                          ?>
                          <option value="<?= $k_id ?>" <?php if ($kat2 == $k_id) { echo "selected"; } ?>><?= $k_nev ?></option>
                          <?php
                      }
                      ?>
                    </select>
                  </div>
                  <?php
              }
          }
          ?>

          <?php
          if ($kat2 > 0) {
              $sql = "SELECT * FROM kategoriak WHERE szulo1 = ? AND szulo2 = ? ORDER BY id";
              $stmt = $kapcsolat->prepare($sql);
              $stmt->bind_param("ii", $kat1, $kat2);
              $stmt->execute();
              $eredmeny = $stmt->get_result();
              if ($eredmeny->num_rows > 0) {
                  ?>
                  <div class="mb-3">
                    <label for="kat3" class="form-label"><b>3. szint:</b></label>
                    <select name="kat3" class="form-select" onChange="document.urlap.submit()">
                      <option value="0" <?php if ($kat3 == 0) { echo "selected"; } ?>>válassz kategóriát</option>
                      <?php
                      while ($sor = $eredmeny->fetch_array()) {
                          $k_id = $sor["id"];
                          $k_nev = $sor["nev"];
                          ?>
                          <option value="<?= $k_id ?>" <?php if ($kat3 == $k_id) { echo "selected"; } ?>><?= $k_nev ?></option>
                          <?php
                      }
                      ?>
                    </select>
                  </div>
                  <?php
              }
          }
          ?>

          <input type="hidden" name="MAX_FILE_SIZE" value="512000">
          <div class="mb-3">
            <label for="nev" class="form-label"><b>Név:</b></label>
            <input name="nev" class="form-control" value="<?= htmlspecialchars($nev) ?>">
          </div>
          <div class="mb-3">
            <label for="nev2" class="form-label">(kiegészítő infó):</label>
            <input name="nev2" class="form-control" value="<?= htmlspecialchars($nev2) ?>">
          </div>
          <div class="mb-3">
            <label for="rovidnev" class="form-label"><b>Rövid név:</b></label>
            <input name="rovidnev" class="form-control" value="<?= htmlspecialchars($rovidnev) ?>">
          </div>
          <div class="mb-3">
            <label for="imgfile" class="form-label"><b>Új fotó:</b></label>
            <input type="file" name="imgfile" class="form-control">
          </div>
          <div class="mb-3">
            <label for="ar_huf" class="form-label"><b>Ár (HUF):</b></label>
            <input name="ar_huf" class="form-control" value="<?= htmlspecialchars($ar_huf) ?>">
          </div>
          <div class="mb-3">
            <label for="egyseg" class="form-label"><b>Egység:</b></label>
            <input name="egyseg" class="form-control" value="<?= htmlspecialchars($egyseg) ?>">
          </div>
          <div class="mb-3">
            <label for="leiras" class="form-label"><b>Rövid leírás:</b></label>
            <textarea name="leiras" class="form-control" rows="3"><?= htmlspecialchars($leiras) ?></textarea>
          </div>
          <div class="mb-3">
            <label for="hosszu_leiras" class="form-label"><b>Hosszú leírás:</b></label>
            <textarea name="hosszu_leiras" class="form-control" rows="6"><?= htmlspecialchars($hosszu_leiras) ?></textarea>
          </div>
          <div class="mb-3">
            <label for="raktaron" class="form-label"><b>Raktáron:</b></label>
            <input name="raktaron" class="form-control" value="<?= htmlspecialchars($raktaron) ?>">
          </div>
          <button type="button" class="btn btn-primary" onClick="document.urlap.mit.value='modositas';document.urlap.submit()">A módosítások végrehajtása</button>
          <br><br>
          <a href="termek_torles.php?id=<?= $id ?>" class="btn btn-danger">A termék törléséhez kattints ide!</a>
        </form>
      </div>
    </body>
    </html>
    <?php
}
// A termék módosítása, beleértve a kép feltöltését is
elseif ($mit == "modositas") {
    // POST adatainak beolvasása
    $nev           = isset($_POST['nev']) ? $_POST['nev'] : "";
    $nev2          = isset($_POST['nev2']) ? $_POST['nev2'] : "";
    $rovidnev      = isset($_POST['rovidnev']) ? $_POST['rovidnev'] : "";
    $ar_huf        = isset($_POST['ar_huf']) ? floatval($_POST['ar_huf']) : 0;
    $egyseg        = isset($_POST['egyseg']) ? $_POST['egyseg'] : "";
    $leiras        = isset($_POST['leiras']) ? $_POST['leiras'] : "";
    $hosszu_leiras = isset($_POST['hosszu_leiras']) ? $_POST['hosszu_leiras'] : "";
    $raktaron     = isset($_POST['raktaron']) ? intval($_POST['raktaron']) : 0;
    $kat1         = isset($_POST['kat1']) ? intval($_POST['kat1']) : 0;
    $kat2         = isset($_POST['kat2']) ? intval($_POST['kat2']) : 0;
    $kat3         = isset($_POST['kat3']) ? intval($_POST['kat3']) : 0;

    $ujkep = 0;
    
    // Kép feltöltésének ellenőrzése
    if (isset($_FILES['imgfile']) && is_uploaded_file($_FILES['imgfile']['tmp_name'])) {
        // Csak JPEG típusú képek elfogadása
        if ($_FILES['imgfile']['type'] == "image/pjpeg" || $_FILES['imgfile']['type'] == "image/jpeg") {
            $imgfile_name = basename($_FILES['imgfile']['name']);
            $newfile = "../img/" . $imgfile_name;
            if (move_uploaded_file($_FILES['imgfile']['tmp_name'], $newfile)) {
                $ujkep = 1;
            }
        }
    }

    if ($ujkep == 1) {
        $sql = "UPDATE arucikk SET nev = ?, nev2 = ?, rovidnev = ?, foto = ?, ar_huf = ?, egyseg = ?, leiras = ?, hosszu_leiras = ?, kat1 = ?, kat2 = ?, kat3 = ?, raktaron = ? WHERE id = ?";
        // Paraméterek: 3 string, 1 string (foto), 1 double, 1 string, 2 string, 4 integer (kat1,kat2,kat3,raktaron) és az id integer
        $stmt = $kapcsolat->prepare($sql);
        $stmt->bind_param("ssssdsssiiiii", $nev, $nev2, $rovidnev, $imgfile_name, $ar_huf, $egyseg, $leiras, $hosszu_leiras, $kat1, $kat2, $kat3, $raktaron, $id);
    } else {
        $sql = "UPDATE arucikk SET nev = ?, nev2 = ?, rovidnev = ?, ar_huf = ?, egyseg = ?, leiras = ?, hosszu_leiras = ?, kat1 = ?, kat2 = ?, kat3 = ?, raktaron = ? WHERE id = ?";
        // Paraméterek: 3 string, 1 double, 1 string, 2 string, 4 integer, majd id integer
        $stmt = $kapcsolat->prepare($sql);
        $stmt->bind_param("sssdsssiiiii", $nev, $nev2, $rovidnev, $ar_huf, $egyseg, $leiras, $hosszu_leiras, $kat1, $kat2, $kat3, $raktaron, $id);
    }
    $stmt->execute();
    ?>
    <html>
    <head>
      <meta name="cache-control" content="private, no-store, no-cache, must-revalidate">
      <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
      <title>Módosítás</title>
    </head>
    <body style="font-family: tahoma; font-size:10pt;">
      <div class="container mt-4">
        <?php if ($ujkep == 1) { ?>
            <div class="alert alert-success" role="alert">
              <h4 class="alert-heading">Sikeres módosítás és képfeltöltés!</h4>
              <p>A termék adatai és a kép sikeresen módosítva.</p>
            </div>
        <?php } else { ?>
            <div class="alert alert-success" role="alert">
              <h4 class="alert-heading">Sikeres módosítás!</h4>
              <p>A termék adatai sikeresen módosítva.</p>
            </div>
        <?php } ?>
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
