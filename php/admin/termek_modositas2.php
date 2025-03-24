<?php
ob_start();

include("../dbconn.php");
include("../fuggvenyek.php");

header("Pragma: no-cache");
header("Cache-control: private, no-store, no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

session_start();
if (!isset($_SESSION['webshop_role']) || $_SESSION['webshop_role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    exit('Hozzáférés megtagadva!');
}

// Alapértelmezett értékek beolvasása
$id    = isset($_REQUEST['id'])    ? intval($_REQUEST['id']) : 0;
$mit   = isset($_REQUEST['mit'])   ? $_REQUEST['mit'] : "urlap";
$kat1  = isset($_REQUEST['kat1'])  ? intval($_REQUEST['kat1']) : 0;
$kat2  = isset($_REQUEST['kat2'])  ? intval($_REQUEST['kat2']) : 0;
$kat3  = isset($_REQUEST['kat3'])  ? intval($_REQUEST['kat3']) : 0;
$szint = isset($_REQUEST['szint']) ? intval($_REQUEST['szint']) : 0;

// Kategóriák szintjeinek beállítása
if ($szint == 1) {
    $kat2 = 0;
    $kat3 = 0;
} elseif ($szint == 2) {
    $kat3 = 0;
}

// 1. LÉPÉS: Űrlap megjelenítése
if ($mit == "urlap") {
    if ($id > 0) {
        $sql = "SELECT * FROM arucikk WHERE id = ?";
        $stmt = $kapcsolat->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $eredmeny = $stmt->get_result();
        
        if ($sor = $eredmeny->fetch_array()) {
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
            $foto          = $sor["foto"];
        } else {
            die("<div class='alert alert-danger'><b>Nincs ilyen termék (ID: $id)</b></div>");
        }
    } else {
        die("<div class='alert alert-danger'><b>Érvénytelen termék ID!</b></div>");
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Termék Módosítása</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body style="font-family: tahoma;">
        <div class="container mt-4">
            <h1 class="mb-4 text-primary">Termék módosítása</h1>
            <a href="../admin-index.php" class="btn btn-primary btn-danger" style="margin-bottom: 10px;">Vissza az admin főoldalra</a>
            
            <?php if (!empty($foto)): ?>
            <div class="mb-3">
                <label class="form-label"><b>Jelenlegi kép:</b></label><br>
                <img src="../../img/<?= htmlspecialchars($foto) ?>" style="max-height: 150px;" class="img-thumbnail">
            </div>
            <?php endif; ?>
            
            <form action="termek_modositas2.php" method="POST" enctype="multipart/form-data" class="bg-light p-4 rounded">
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="hidden" name="mit" value="modositas">
                <input type="hidden" name="szint" value="0">
                
                <!-- Kategória választó -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label"><b>1. szint:</b></label>
                        <select name="kat1" class="form-select border-5" onchange="this.form.submit()">
                            <option value="0">Válassz fő kategóriát</option>
                            <?php
                            $sql = "SELECT * FROM kategoriak WHERE szulo1 = 0 ORDER BY nev";
                            $eredmeny = mysqli_query($kapcsolat, $sql);
                            while ($sor = mysqli_fetch_array($eredmeny)) {
                                echo '<option value="'.$sor['id'].'" '.($kat1==$sor['id']?'selected':'').'>'.$sor['nev'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <?php if ($kat1 > 0): ?>
                    <div class="col-md-4">
                        <label class="form-label"><b>2. szint:</b></label>
                        <select name="kat2" class="form-select border-5" onchange="this.form.submit()">
                            <option value="0">Válassz alkategóriát</option>
                            <?php
                            $sql = "SELECT * FROM kategoriak WHERE szulo1 = ? AND szulo2 = 0 ORDER BY nev";
                            $stmt = $kapcsolat->prepare($sql);
                            $stmt->bind_param("i", $kat1);
                            $stmt->execute();
                            $eredmeny = $stmt->get_result();
                            while ($sor = $eredmeny->fetch_array()) {
                                echo '<option value="'.$sor['id'].'" '.($kat2==$sor['id']?'selected':'').'>'.$sor['nev'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($kat2 > 0): ?>
                    <div class="col-md-4">
                        <label class="form-label"><b>3. szint:</b></label>
                        <select name="kat3" class="form-select border-5" onchange="this.form.submit()">
                            <option value="0">Válassz alkategóriát</option>
                            <?php
                            $sql = "SELECT * FROM kategoriak WHERE szulo1 = ? AND szulo2 = ? ORDER BY nev";
                            $stmt = $kapcsolat->prepare($sql);
                            $stmt->bind_param("ii", $kat1, $kat2);
                            $stmt->execute();
                            $eredmeny = $stmt->get_result();
                            while ($sor = $eredmeny->fetch_array()) {
                                echo '<option value="'.$sor['id'].'" '.($kat3==$sor['id']?'selected':'').'>'.$sor['nev'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Termék adatok -->
                <div class="mb-3">
                    <label class="form-label"><b>Terméknév:</b></label>
                    <input name="nev" class="form-control border-5" value="<?= htmlspecialchars($nev) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Kiegészítő név:</label>
                    <input name="nev2" class="form-control border-5" value="<?= htmlspecialchars($nev2) ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><b>Rövid név:</b></label>
                    <input name="rovidnev" class="form-control border-5" value="<?= htmlspecialchars($rovidnev) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><b>Új kép:</b></label>
                    <input type="file" name="imgfile" class="form-control border-5" accept="image/jpeg">
                    <small class="text-muted">Max. 500KB, csak JPG formátum</small>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><b>Ár (HUF):</b></label>
                        <input type="number" name="ar_huf" class="form-control border-5" value="<?= htmlspecialchars($ar_huf) ?>" min="0" step="1" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><b>Egység:</b></label>
                        <input name="egyseg" class="form-control border-5" value="<?= htmlspecialchars($egyseg) ?>" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><b>Raktáron:</b></label>
                        <input type="number" name="raktaron" class="form-control border-5" value="<?= htmlspecialchars($raktaron) ?>" min="0" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><b>Rövid leírás:</b></label>
                    <textarea name="leiras" class="form-control border-5" rows="3"><?= htmlspecialchars($leiras) ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><b>Részletes leírás:</b></label>
                    <textarea name="hosszu_leiras" class="form-control border-5" rows="6"><?= htmlspecialchars($hosszu_leiras) ?></textarea>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary me-md-2">Mentés</button>
                    <a href="termek_torles.php?id=<?= $id ?>" class="btn btn-danger" onclick="return confirm('Biztos törlöd?')">Törlés</a>
                </div>
            </form>
        </div>
    </body>
    </html>
    <?php
}
// 2. LÉPÉS: Adatok feldolgozása
elseif ($mit == "modositas") {
    // Adatok ellenőrzése és tisztítása
    $nev = mysqli_real_escape_string($kapcsolat, $_POST['nev'] ?? '');
    $nev2 = mysqli_real_escape_string($kapcsolat, $_POST['nev2'] ?? '');
    $rovidnev = mysqli_real_escape_string($kapcsolat, $_POST['rovidnev'] ?? '');
    $ar_huf = floatval($_POST['ar_huf'] ?? 0);
    $egyseg = mysqli_real_escape_string($kapcsolat, $_POST['egyseg'] ?? '');
    $leiras = mysqli_real_escape_string($kapcsolat, $_POST['leiras'] ?? '');
    $hosszu_leiras = mysqli_real_escape_string($kapcsolat, $_POST['hosszu_leiras'] ?? '');
    $raktaron = intval($_POST['raktaron'] ?? 0);
    $kat1 = intval($_POST['kat1'] ?? 0);
    $kat2 = intval($_POST['kat2'] ?? 0);
    $kat3 = intval($_POST['kat3'] ?? 0);
    
    // Képfeltöltés kezelése
    $kep_utvonal = null;
    $kep_hiba = '';
    
    if (isset($_FILES['imgfile']) && $_FILES['imgfile']['error'] == UPLOAD_ERR_OK) {
        $kep_info = getimagesize($_FILES['imgfile']['tmp_name']);
        
        if ($kep_info && ($kep_info['mime'] == 'image/jpeg' || $kep_info['mime'] == 'image/pjpeg')) {
            if ($_FILES['imgfile']['size'] <= 500000) { // 500KB
                $kiterjesztes = pathinfo($_FILES['imgfile']['name'], PATHINFO_EXTENSION);
                $uj_kep_nev = uniqid('img_').'.jpg';
                $cel_utvonal = '../../img/'.$uj_kep_nev;
                
                if (move_uploaded_file($_FILES['imgfile']['tmp_name'], $cel_utvonal)) {
                    $kep_utvonal = $uj_kep_nev;
                    
                    // Régi kép törlése
                    $sql = "SELECT foto FROM arucikk WHERE id = ?";
                    $stmt = $kapcsolat->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $eredmeny = $stmt->get_result();
                    if ($sor = $eredmeny->fetch_assoc()) {
                        $regi_kep = '../../img/'.$sor['foto'];
                        if (file_exists($regi_kep) && is_file($regi_kep)) {
                            unlink($regi_kep);
                        }
                    }
                } else {
                    $kep_hiba = "A kép mozgatása sikertelen!";
                }
            } else {
                $kep_hiba = "A kép túl nagy (max. 500KB)!";
            }
        } else {
            $kep_hiba = "Csak JPG formátumú képek engedélyezettek!";
        }
    } elseif ($_FILES['imgfile']['error'] != UPLOAD_ERR_NO_FILE) {
        $kep_hiba = "Hiba a kép feltöltésekor!";
    }
    
    // Adatbázis frissítése
    if (empty($kep_hiba)) {
        if ($kep_utvonal) {
            $sql = "UPDATE arucikk SET nev=?, nev2=?, rovidnev=?, foto=?, ar_huf=?, egyseg=?, leiras=?, hosszu_leiras=?, kat1=?, kat2=?, kat3=?, raktaron=? WHERE id=?";
            $stmt = $kapcsolat->prepare($sql);
            $stmt->bind_param("ssssdsssiiiii", $nev, $nev2, $rovidnev, $kep_utvonal, $ar_huf, $egyseg, $leiras, $hosszu_leiras, $kat1, $kat2, $kat3, $raktaron, $id);
        } else {
            $sql = "UPDATE arucikk SET nev=?, nev2=?, rovidnev=?, ar_huf=?, egyseg=?, leiras=?, hosszu_leiras=?, kat1=?, kat2=?, kat3=?, raktaron=? WHERE id=?";
            $stmt = $kapcsolat->prepare($sql);
            $stmt->bind_param("sssdsssiiiii", $nev, $nev2, $rovidnev, $ar_huf, $egyseg, $leiras, $hosszu_leiras, $kat1, $kat2, $kat3, $raktaron, $id);
        }
        
        if ($stmt->execute()) {
            $siker = true;
            $uzenet = "Sikeres módosítás!";
        } else {
            $siker = false;
            $uzenet = "Hiba az adatbázis frissítésekor: ".$stmt->error;
        }
    } else {
        $siker = false;
        $uzenet = $kep_hiba;
    }
    
    // Eredmény megjelenítése
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Módosítás eredménye</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-4">
            <div class="alert alert-<?= $siker ? 'success' : 'danger' ?>">
                <h4><?= $siker ? 'Siker!' : 'Hiba!' ?></h4>
                <p><?= $uzenet ?></p>
                <hr>
                <div class="d-flex justify-content-between">
                    <a href="termek_modositas2.php?id=<?= $id ?>" class="btn btn-primary">Vissza a szerkesztéshez</a>
                    <a href="termek_modositas.php" class="btn btn-secondary">Vissza a listához</a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
} else {
    die("<div class='alert alert-danger'>Érvénytelen művelet!</div>");
}

mysqli_close($kapcsolat);
ob_end_flush();
?>