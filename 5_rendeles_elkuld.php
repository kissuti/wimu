<?php
ob_start();
session_start();

include("php/dbconn.php");
include("php/fuggvenyek.php");

header("Pragma: no-cache"); 
header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// Felhasználói adatok ellenőrzése
$webshop_email = $_COOKIE['webshop_email'] ?? "";
$webshop_jelszo = $_COOKIE['webshop_jelszo'] ?? "";
$belepve = 0;

if ($webshop_email != "" && $webshop_jelszo != "") {
    $parancs = "SELECT * FROM ugyfel WHERE email='".mysqli_real_escape_string($kapcsolat, $webshop_email)."' AND jelszo='".mysqli_real_escape_string($kapcsolat, $webshop_jelszo)."'";
    $eredmeny = mysqli_query($kapcsolat, $parancs);
    
    if ($eredmeny && mysqli_num_rows($eredmeny) > 0) {
        $sor = mysqli_fetch_array($eredmeny);
        $webshop_id = $sor["id"];
        $webshop_nev = $sor["nev"];
        $telefon = $sor["telefon"];
        $kulfoldi = $sor["kulfoldi"];
        $orszag = $sor["orszag"];
        $irszam = $sor["irszam"];
        $varos = $sor["varos"];
        $utca = $sor["utca"];
        $sz_nev = $sor["sz_nev"];
        $sz_irszam = $sor["sz_irszam"];
        $sz_varos = $sor["sz_varos"];
        $sz_utca = $sor["sz_utca"];
        $belepve = 1;
    }
}

// Adatok ellenőrzése
$osszeg = intval($_POST['osszeg'] ?? 0);
$fizet = intval($_POST['fizet'] ?? 0);

if ($osszeg <= 0 || $belepve != 1) {
    header("Location: index.php");
    exit();
}

// 1. Rendelés rögzítése
$kod = uniqid();
$idopont = date("Y-m-d H:i:s");

$sql = "INSERT INTO rendelesek (
    ugyfel_id, 
    kod, 
    idopont, 
    fizetendo, 
    fizetesi_mod, 
    nev, 
    email, 
    telefon, 
    kulfoldi, 
    orszag, 
    irszam, 
    varos, 
    utca, 
    sz_nev, 
    sz_irszam, 
    sz_varos, 
    sz_utca
) VALUES (
    ".intval($webshop_id).",
    '".mysqli_real_escape_string($kapcsolat, $kod)."',
    '".mysqli_real_escape_string($kapcsolat, $idopont)."',
    ".intval($osszeg).",
    ".intval($fizet).",
    '".mysqli_real_escape_string($kapcsolat, $webshop_nev)."',
    '".mysqli_real_escape_string($kapcsolat, $webshop_email)."',
    '".mysqli_real_escape_string($kapcsolat, $telefon)."',
    ".intval($kulfoldi).",
    '".mysqli_real_escape_string($kapcsolat, $orszag)."',
    '".mysqli_real_escape_string($kapcsolat, $irszam)."',
    '".mysqli_real_escape_string($kapcsolat, $varos)."',
    '".mysqli_real_escape_string($kapcsolat, $utca)."',
    '".mysqli_real_escape_string($kapcsolat, $sz_nev)."',
    '".mysqli_real_escape_string($kapcsolat, $sz_irszam)."',
    '".mysqli_real_escape_string($kapcsolat, $sz_varos)."',
    '".mysqli_real_escape_string($kapcsolat, $sz_utca)."'
)";

if (!mysqli_query($kapcsolat, $sql)) {
    die("Hiba a rendelés rögzítésénél: " . mysqli_error($kapcsolat));
}

// Rendelés ID lekérése
$rendeles_id = mysqli_insert_id($kapcsolat);

// 2. Termékek mentése a rendeles_tetelek táblába
$sql_kosar = "SELECT arucikk_id, db FROM kosar WHERE ugyfel_id = ? AND rendeles_id = 0";
$parancs = $kapcsolat->prepare($sql_kosar);
$parancs->bind_param("i", $webshop_id);
$parancs->execute();
$kosar_tetelek = $parancs->get_result();

while ($tetel = $kosar_tetelek->fetch_assoc()) {
    $arucikk_id = $tetel['arucikk_id'];
    $db = $tetel['db'];
    
    // Egységár lekérése
    $sql_ar = "SELECT ar_huf FROM arucikk WHERE id = ?";
    $parancs_ar = $kapcsolat->prepare($sql_ar);
    $parancs_ar->bind_param("i", $arucikk_id);
    $parancs_ar->execute();
    $ar = $parancs_ar->get_result()->fetch_assoc()['ar_huf'];
    
    // Beszúrás a rendeles_tetelek táblába
    $sql_insert = "INSERT INTO rendeles_tetelek (rendeles_id, arucikk_id, db, ar_huf) 
                   VALUES (?, ?, ?, ?)";
    $parancs_insert = $kapcsolat->prepare($sql_insert);
    $parancs_insert->bind_param("iiii", $rendeles_id, $arucikk_id, $db, $ar);
    $parancs_insert->execute();
}

// 3. Kosár tételek frissítése
$sql = "UPDATE kosar SET rendeles_id = ".intval($rendeles_id)." WHERE ugyfel_id = ".intval($webshop_id)." AND rendeles_id = 0";
if (!mysqli_query($kapcsolat, $sql)) {
    die("Hiba a kosár frissítésénél: " . mysqli_error($kapcsolat));
}

// 4. Termékek raktárkészletének csökkentése
$sql = "SELECT * FROM kosar WHERE ugyfel_id = ".intval($webshop_id)." AND rendeles_id = ".intval($rendeles_id);
$eredmeny = mysqli_query($kapcsolat, $sql);

while ($sor = mysqli_fetch_array($eredmeny)) {
    $arucikk_id = intval($sor['arucikk_id']);
    $db = intval($sor['db']);
    
    $update_sql = "UPDATE arucikk SET raktaron = raktaron - ".$db." WHERE id = ".$arucikk_id;
    if (!mysqli_query($kapcsolat, $update_sql)) {
        die("Hiba a raktárkészlet frissítésénél: " . mysqli_error($kapcsolat));
    }
}

// 5. Kosár tételek törlése
$sql = "DELETE FROM kosar WHERE ugyfel_id = ".intval($webshop_id)." AND rendeles_id = ".intval($rendeles_id);
if (!mysqli_query($kapcsolat, $sql)) {
    die("Hiba a kosár tételek törlésénél: " . mysqli_error($kapcsolat));
}

// Sikeres oldal megjelenítése
?>
<!DOCTYPE html>
<html>
<head>
    <title>Wimu Webshop - Rendelés sikeres</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include("teteje.php"); ?>

    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h3 class="mb-0">Rendelés sikeres!</h3>
            </div>
            <div class="card-body">
                <p class="lead">Köszönjük a rendelést, <strong><?= htmlspecialchars($webshop_nev) ?></strong>!</p>
                <div class="alert alert-info">
                    <h5>Rendelés azonosító: <span class="badge bg-primary"><?= $rendeles_id ?></span></h5>
                    <p>A rendelés részleteit elküldtük a <strong><?= htmlspecialchars($webshop_email) ?></strong> e-mail címre.</p>
                </div>
                <hr>
                <a href="index.php" class="btn btn-lg btn-success">Vissza a főoldalra</a>
            </div>
        </div>
        
        <div class="mt-4 alert alert-warning">
            <h5>Fontos tudnivalók:</h5>
            <ul>
                <li>A rendelésed állapotát a profilodban követheted</li>
                <li>Számlád elektronikus formában érkezik meg</li>
                <li>Kérdés esetén írj a info@wimu.com címre</li>
            </ul>
        </div>
    </div>

    <?php include("alja.php"); ?>
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