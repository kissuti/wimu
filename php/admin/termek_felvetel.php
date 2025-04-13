<?php
ob_start();

include("../dbconn.php");
include("../fuggvenyek.php");

session_start();
if (!isset($_SESSION['webshop_role']) || $_SESSION['webshop_role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    exit('Hozzáférés megtagadva!');
}

// A beérkező adatok kiolvasása (POST és GET is lehetséges)
$kat1 = isset($_REQUEST['kat1']) ? $_REQUEST['kat1'] : 0;
$kat2 = isset($_REQUEST['kat2']) ? $_REQUEST['kat2'] : 0;
$kat3 = isset($_REQUEST['kat3']) ? $_REQUEST['kat3'] : 0;
$szint = isset($_REQUEST['szint']) ? $_REQUEST['szint'] : 0;
$mit   = isset($_REQUEST['mit'])   ? $_REQUEST['mit']   : "kategoria";

// Szintek korrekciója
if ($szint == 1) {
    $kat2 = 0;
    $kat3 = 0;
} elseif ($szint == 2) {
    $kat3 = 0;
}


// 1. LÉPÉS: Kategória kiválasztása
if ($mit == "kategoria") {
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Termék Felvétele</title>
    <meta name="cache-control" content="private, no-store, no-cache, must-revalidate">
  </head>
  <body style="font-family:tahoma">
    <div class="container mt-4">
      <h1 class="mb-4 text-primary">Új termék felvitele - a kategória kiválasztása</h1>
      <a href="../admin-index.php" class="btn btn-primary btn-danger">Vissza az admin főoldalra</a>
      <form name="urlap" action="termek_felvetel.php" method="POST" class="bg-light p-4 rounded">
        <input type="hidden" name="mit" value="kategoria">
        <input type="hidden" name="szint" value="0">
        
        <div class="mb-3">
          <label for="kat1" class="form-label"><b>1. szint:</b></label>
          <select name="kat1" class="form-select border-5" onChange="if(this.value != '0'){document.urlap.szint.value=1; document.urlap.submit();}">
            <option value="0" <?php if($kat1==0){ echo "selected"; } ?>>válaszd ki a fő kategóriát</option>
            <?php
            $kategoria = "";
            $sql = "SELECT * FROM kategoriak WHERE szulo1 IS NULL ORDER BY id";
            $eredmeny = mysqli_query($kapcsolat, $sql);
            while ($sor = mysqli_fetch_array($eredmeny)) {
                $id = $sor["id"];
                $nev = $sor["nev"];
                ?>
                <option value="<?= $id ?>" <?php if ($kat1 == $id) { echo "selected"; $kategoria = $nev; } ?>><?= $nev ?></option>
                <?php
            }
            ?>
          </select>
        </div>

        <?php
        // 2.szint (ha van alatta kategória)
        if ($kat1 > 0) {
            $sql = "SELECT * FROM kategoriak WHERE szulo1=? AND szulo2 IS NULL ORDER BY id";
            $stmt = $kapcsolat->prepare($sql);
            $stmt->bind_param("i", $kat1);
            $stmt->execute();
            $eredmeny = $stmt->get_result();
            if ($eredmeny->num_rows > 0) {
            ?>
              <div class="mb-3">
                <label for="kat2" class="form-label"><b>2. szint:</b></label>
                <select name="kat2" class="form-select border-5" onChange="if(this.value != '0'){document.urlap.szint.value=2; document.urlap.submit();}">
                  <option value="0" <?php if ($kat2==0){ echo "selected"; } ?>>válassz kategóriát</option>
                  <?php
                  while ($sor = $eredmeny->fetch_array()) {
                      $id = $sor["id"];
                      $nev = $sor["nev"];
                      ?>
                      <option value="<?= $id ?>" <?php if ($kat2==$id){ echo "selected"; $kategoria .= " - " . $nev; } ?>><?= $nev ?></option>
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
        // 3.szint (ha van alatta kategória)
        if ($kat2 > 0) {
            $sql = "SELECT * FROM kategoriak WHERE szulo1=? AND szulo2=? ORDER BY id";
            $stmt = $kapcsolat->prepare($sql);
            $stmt->bind_param("ii", $kat1, $kat2);
            $stmt->execute();
            $eredmeny = $stmt->get_result();
            if ($eredmeny->num_rows > 0) {
            ?>
              <div class="mb-3">
                <label for="kat3" class="form-label"><b>3. szint:</b></label>
                <select name="kat3" class="form-select border-5" onChange="document.urlap.submit();">
                  <option value="0" <?php if ($kat3==0){ echo "selected"; } ?>>válassz kategóriát</option>
                  <?php
                  while ($sor = $eredmeny->fetch_array()) {
                      $id = $sor["id"];
                      $nev = $sor["nev"];
                      ?>
                      <option value="<?= $id ?>" <?php if ($kat3==$id){ echo "selected"; $kategoria .= " - " . $nev; } ?>><?= $nev ?></option>
                      <?php
                  }
                  ?>
                </select>
              </div>
            <?php
            }
        }
        ?>

        <input type="hidden" name="kategoria" value="<?= $kategoria ?>">
        <button type="button" class="btn btn-primary" onClick="document.urlap.mit.value='adatok'; document.urlap.submit();">A termék adatai --></button>
      </form>
    </div>
  </body>
</html>
<?php
} 
// 2. LÉPÉS: A termék adatai
elseif ($mit == "adatok") {

    $nev         = isset($_REQUEST['nev']) ? $_REQUEST['nev'] : "";
    $nev2        = isset($_REQUEST['nev2']) ? $_REQUEST['nev2'] : "";
    $rovidnev    = isset($_REQUEST['rovidnev']) ? $_REQUEST['rovidnev'] : "";
    $ar_huf      = isset($_REQUEST['ar_huf']) ? $_REQUEST['ar_huf'] : "0";
    $raktaron    = isset($_REQUEST['raktaron']) ? $_REQUEST['raktaron'] : "0";
    $egyseg      = isset($_REQUEST['egyseg']) ? $_REQUEST['egyseg'] : "darab";
    $leiras      = isset($_REQUEST['leiras']) ? $_REQUEST['leiras'] : "";
    $hosszu_leiras = isset($_REQUEST['hosszu_leiras']) ? $_REQUEST['hosszu_leiras'] : "";
    $kategoria   = isset($_REQUEST['kategoria']) ? $_REQUEST['kategoria'] : "";
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Felvétel</title>
    <meta name="cache-control" content="private, no-store, no-cache, must-revalidate">
  </head>
  <body style="font-family:tahoma;font-size:10pt">
    <div class="container mt-4">
      <h1 class="mb-4 text-primary">Új termék felvitele - a termék adatai</h1>
      <form name="urlap" action="termek_felvetel.php" method="POST" enctype="multipart/form-data" class="bg-light p-4 rounded">
        <input type="hidden" name="MAX_FILE_SIZE" value="512000">
        <input type="hidden" name="mit" value="feltoltes">
        <input type="hidden" name="kat1" value="<?= $kat1 ?>">
        <input type="hidden" name="kat2" value="<?= $kat2 ?>">
        <input type="hidden" name="kat3" value="<?= $kat3 ?>">
        <input type="hidden" name="kategoria" value="<?= $kategoria ?>">
        <div class="mb-3">
          <label for="kategoria" class="form-label fs-4">Kategória:</label>
          <p class="form-control-plaintext fs-6"><?= $kategoria ?></p>
        </div>
        <div class="mb-3">
          <label for="nev" class="form-label"><b>Név:</b></label>
          <input name="nev" class="form-control border-5" value="<?= $nev ?>">
        </div>
        <div class="mb-3">
          <label for="nev2" class="form-label">(kiegészítő infó):</label>
          <input name="nev2" class="form-control border-5" value="<?= $nev2 ?>">
        </div>
        <div class="mb-3">
          <label for="rovidnev" class="form-label"><b>Rövid név:</b></label>
          <input name="rovidnev" class="form-control border-5" value="<?= $rovidnev ?>">
        </div>
        <div class="mb-3">
          <label for="imgfile" class="form-label"><b>Fotó:</b></label>
          <input type="file" name="imgfile" class="form-control border-5">
        </div>
        <div class="mb-3">
          <label for="ar_huf" class="form-label"><b>Ár (HUF):</b></label>
          <input name="ar_huf" class="form-control border-5" value="<?= $ar_huf ?>">
        </div>
        <div class="mb-3">
          <label for="egyseg" class="form-label"><b>Egység:</b></label>
          <input name="egyseg" class="form-control border-5" value="<?= $egyseg ?>">
        </div>
        <div class="mb-3">
          <label for="leiras" class="form-label"><b>Rövid leírás:</b></label>
          <textarea name="leiras" class="form-control border-5" rows="3"><?= $leiras ?></textarea>
        </div>
        <div class="mb-3">
          <label for="hosszu_leiras" class="form-label"><b>Hosszú leírás:</b></label>
          <textarea name="hosszu_leiras" class="form-control border-5" rows="6"><?= $hosszu_leiras ?></textarea>
        </div>
        <div class="mb-3">
          <label for="raktaron" class="form-label"><b>Raktáron:</b></label>
          <input name="raktaron" class="form-control border-5" value="<?= $raktaron ?>">
        </div>
        <button type="button" class="btn btn-primary" onClick="document.urlap.submit()">az új termék feltöltése --></button>
      </form>
    </div>
  </body>
</html>
<?php
}
// 3. LÉPÉS: A kép feltöltése és a termék felvitele
elseif ($mit == "feltoltes") {

    $hiba = 0;
    // A fájl feltöltése $_FILES használatával
    if (isset($_FILES['imgfile']) && is_uploaded_file($_FILES['imgfile']['tmp_name'])) {
        $imgfile_name = $_FILES['imgfile']['name'];
        $imgfile_tmp  = $_FILES['imgfile']['tmp_name'];
        $imgfile_type = $_FILES['imgfile']['type'];
        // Ellenőrizze, hogy a fájl jpg/jpeg típusú-e
        if (strpos($imgfile_type, "jpeg") !== false || strpos($imgfile_type, "jpg") !== false) {
            $newfile = "../../img/" . $imgfile_name;
            if (move_uploaded_file($imgfile_tmp, $newfile)) {
                $sql = "INSERT INTO arucikk (nev, nev2, rovidnev, foto, ar_huf, egyseg, leiras, hosszu_leiras, kat1, kat2, kat3, raktaron) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $kapcsolat->prepare($sql);
                // A termék adatai a feltöltés előtti űrlapból érkeznek
                $nev         = isset($_REQUEST['nev']) ? $_REQUEST['nev'] : "";
                $nev2        = isset($_REQUEST['nev2']) ? $_REQUEST['nev2'] : "";
                $rovidnev    = isset($_REQUEST['rovidnev']) ? $_REQUEST['rovidnev'] : "";
                $ar_huf      = isset($_REQUEST['ar_huf']) ? $_REQUEST['ar_huf'] : 0;
                $egyseg      = isset($_REQUEST['egyseg']) ? $_REQUEST['egyseg'] : "darab";
                $leiras      = isset($_REQUEST['leiras']) ? $_REQUEST['leiras'] : "";
                $hosszu_leiras = isset($_REQUEST['hosszu_leiras']) ? $_REQUEST['hosszu_leiras'] : "";
                $raktaron    = isset($_REQUEST['raktaron']) ? $_REQUEST['raktaron'] : 0;
                $stmt->bind_param("ssssdsssiiii", $nev, $nev2, $rovidnev, $imgfile_name, $ar_huf, $egyseg, $leiras, $hosszu_leiras, $kat1, $kat2, $kat3, $raktaron);
                $stmt->execute();
            } else {
                $hiba = 1;
            }
        } else {
            $hiba = 1;
        }
    } else {
        $hiba = 1;
    }
    ?>
    <html>
      <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <title>Felvétel</title>
        <meta name="cache-control" content="private, no-store, no-cache, must-revalidate">
      </head>
      <body style="font-family:tahoma;font-size:10pt">
        <div class="container mt-4">
          <?php
          if ($hiba == 0) {
          ?>
            <div class="alert alert-success" role="alert">
              <h4 class="alert-heading">Sikeres felvétel!</h4>
              <p>A termék sikeresen fel lett véve.</p>
              <hr>
              <a href="termek_felvetel.php" class="btn btn-primary">Újabb termék felvitele</a>
              <a href="../../index.php" class="btn btn-danger">Főoldal</a>
            </div>
          <?php
          } else {
          ?>
            <div class="alert alert-danger" role="alert">
              <h4 class="alert-heading">A kép feltöltése nem sikerült!</h4>
              <p>Próbáld újra.</p>
              <hr>
              <form name="urlap" action="termek_felvetel.php" method="POST">
                <input type="hidden" name="mit" value="adatok">
                <input type="hidden" name="kat1" value="<?= $kat1 ?>">
                <input type="hidden" name="kat2" value="<?= $kat2 ?>">
                <input type="hidden" name="kat3" value="<?= $kat3 ?>">
                <input type="hidden" name="kategoria" value="<?= isset($_REQUEST['kategoria']) ? $_REQUEST['kategoria'] : "" ?>">
                <input type="hidden" name="nev" value="<?= isset($_REQUEST['nev']) ? $_REQUEST['nev'] : "" ?>">
                <input type="hidden" name="nev2" value="<?= isset($_REQUEST['nev2']) ? $_REQUEST['nev2'] : "" ?>">
                <input type="hidden" name="rovidnev" value="<?= isset($_REQUEST['rovidnev']) ? $_REQUEST['rovidnev'] : "" ?>">
                <input type="hidden" name="ar_huf" value="<?= isset($_REQUEST['ar_huf']) ? $_REQUEST['ar_huf'] : 0 ?>">
                <input type="hidden" name="raktaron" value="<?= isset($_REQUEST['raktaron']) ? $_REQUEST['raktaron'] : 0 ?>">
                <input type="hidden" name="egyseg" value="<?= isset($_REQUEST['egyseg']) ? $_REQUEST['egyseg'] : "darab" ?>">
                <input type="hidden" name="leiras" value="<?= isset($_REQUEST['leiras']) ? $_REQUEST['leiras'] : "" ?>">
                <input type="hidden" name="hosszu_leiras" value="<?= isset($_REQUEST['hosszu_leiras']) ? $_REQUEST['hosszu_leiras'] : "" ?>">
                <button type="submit" class="btn btn-primary">Ide kattintva visszatérhetsz az előző oldalra.</button>
              </form>
            </div>
          <?php
          }
          ?>
        </div>
      </body>
    </html>
    <?php
}

mysqli_close($kapcsolat);
ob_end_flush();
?>
