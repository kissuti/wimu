<?php
ob_start();
session_start();

include("php/dbconn.php");

header("Pragma: no-cache");
header("Cache-control: private, no-store, no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

$webshop_email = $_COOKIE['webshop_email'] ?? "";
$webshop_jelszo = $_COOKIE['webshop_jelszo'] ?? "";

$belepve = 0;
$most = date("Y-m-d H:i:s");

$arucikk_id = isset($_POST['arucikk_id']) ? intval($_POST['arucikk_id']) : 0;
$db = isset($_POST['db']) ? intval($_POST['db']) : 1;

// Lekérdezzük az aktuális készletet az adott termékhez
$sql = "SELECT raktaron FROM arucikk WHERE id = $arucikk_id";
$result = mysqli_query($kapcsolat, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    header('HTTP/1.1 400 Bad Request');
    echo "Hiba: A termék nem található!";
    exit();
}
$row = mysqli_fetch_assoc($result);
$current_stock = intval($row['raktaron']);

// Ha a kért mennyiség önmagában nagyobb, mint a készlet, akkor hiba
if ($db > $current_stock) {
    header('HTTP/1.1 400 Bad Request');
    echo "Hiba: A kért mennyiség meghaladja a raktáron lévő mennyiséget!";
    exit();
}

// Felhasználó ellenőrzése
$webshop_id = 0;
if ($webshop_email != "" && $webshop_jelszo != "") {
    $parancs = "SELECT * FROM ugyfel WHERE email='$webshop_email' AND jelszo='$webshop_jelszo'";
    $eredmeny = mysqli_query($kapcsolat, $parancs);
    if ($eredmeny && mysqli_num_rows($eredmeny) > 0) {
        $sor = mysqli_fetch_array($eredmeny);
        $webshop_id = $sor["id"];
        $belepve = 1;
    }
}

// Ellenőrizzük a kosárban lévő mennyiséget
$current_cart_quantity = 0;
if ($belepve == 1) {
    // Bejelentkezett felhasználó: keresés ugyfel_id alapján
    $parancs = "SELECT * FROM kosar WHERE ugyfel_id = $webshop_id AND arucikk_id = $arucikk_id AND rendeles_id = 0";
} else {
    // Nem bejelentkezett felhasználó: keresés session_id alapján
    $parancs = "SELECT * FROM kosar WHERE session_id = '" . session_id() . "' AND arucikk_id = $arucikk_id AND rendeles_id = 0";
}

$eredmeny = mysqli_query($kapcsolat, $parancs);
if ($eredmeny && mysqli_num_rows($eredmeny) > 0) {
    $sor = mysqli_fetch_array($eredmeny);
    $current_cart_quantity = intval($sor["db"]);
}

// Ellenőrizzük, hogy a kosárban lévő mennyiség plusz a most hozzáadandó ne lépje túl a készletet
if (($current_cart_quantity + $db) > $current_stock) {
    header('HTTP/1.1 400 Bad Request');
    echo "Hiba: A kosárban lévő mennyiség plusz a kért mennyiség meghaladja a raktáron lévő készletet!";
    exit();
}

// Ha minden ellenőrzés sikeres, akkor végrehajtjuk a kosárba helyezést
if ($belepve == 1) {
    // Bejelentkezett felhasználó: ugyfel_id használata
    if (mysqli_num_rows($eredmeny) > 0) {
        $kosar_id = $sor["id"];
        $sql = "UPDATE kosar SET db = db + $db WHERE id = $kosar_id";
    } else {
        $sql = "INSERT INTO kosar (arucikk_id, ugyfel_id, db, mikor) VALUES ($arucikk_id, $webshop_id, $db, '$most')";
    }
} else {
    // Nem bejelentkezett felhasználó: session_id használata
    if (mysqli_num_rows($eredmeny) > 0) {
        $kosar_id = $sor["id"];
        $sql = "UPDATE kosar SET db = db + $db WHERE id = $kosar_id";
    } else {
        $sql = "INSERT INTO kosar (arucikk_id, session_id, db, mikor) VALUES ($arucikk_id, '" . session_id() . "', $db, '$most')";
    }
}

if (!mysqli_query($kapcsolat, $sql)) {
    header('HTTP/1.1 500 Internal Server Error');
    die("Hiba a kosár frissítésekor: " . mysqli_error($kapcsolat));
  }
  
  // Frissítjük a termék készletét
  $sql = "UPDATE arucikk SET raktaron = raktaron - $db WHERE id = $arucikk_id";
  if (!mysqli_query($kapcsolat, $sql)) {
    header('HTTP/1.1 500 Internal Server Error');
    die("Hiba a készlet frissítésekor: " . mysqli_error($kapcsolat));
  }
  
  // Sikeres válasz küldése
  header('Content-Type: application/json');
  echo json_encode(array('success' => true));
  
  mysqli_close($kapcsolat);
ob_end_flush();
?>