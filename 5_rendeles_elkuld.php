<?php
ob_start();
session_start();

include("php/dbconn.php");
include("php/fuggvenyek.php");

header("Pragma: no-cache"); 
Header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

$webshop_email = $_COOKIE['webshop_email'] ?? "";
$webshop_jelszo = $_COOKIE['webshop_jelszo'] ?? "";

$belepve = 0;
$most = date("Y-m-d H:i:s");

if ($webshop_email != "" && $webshop_jelszo != "") {
  $parancs = "SELECT * FROM ugyfel WHERE email='$webshop_email' AND jelszo='$webshop_jelszo'";
  $eredmeny = mysqli_query($kapcsolat, $parancs);
  if (mysqli_num_rows($eredmeny) > 0) {
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

$osszeg = $_POST['osszeg'] ?? 0;
$fizet = $_POST['fizet'] ?? 0;

if ($osszeg == 0 || $belepve == 0) {
  header("Location: index.php");
  exit();
}

$idopont = date("Y-m-d H:i:s");
$kod = uniqid();

$sql = "INSERT INTO rendelesek (ugyfel_id, kod, idopont, fizetendo, fizetesi_mod, nev, email, telefon, kulfoldi, orszag, irszam, varos, utca, sz_nev, sz_irszam, sz_varos, sz_utca) VALUES ($webshop_id, '$kod', '$idopont', $osszeg, $fizet, '$webshop_nev', '$webshop_email', '$telefon', $kulfoldi, '$orszag', '$irszam', '$varos', '$utca', '$sz_nev', '$sz_irszam', '$sz_varos', '$sz_utca')";
mysqli_query($kapcsolat, $sql);

$sql = "SELECT id FROM rendelesek WHERE ugyfel_id=$webshop_id ORDER BY id DESC LIMIT 1";
$eredmeny = mysqli_query($kapcsolat, $sql);
$sor = mysqli_fetch_array($eredmeny);
$rendeles_id = $sor["id"];

$sql = "SELECT * FROM kosar WHERE ugyfel_id=$webshop_id AND rendeles_id=0";
$eredmeny = mysqli_query($kapcsolat, $sql);
while ($sor = mysqli_fetch_array($eredmeny)) {
  $arucikk_id = $sor["arucikk_id"];
  $db = $sor["db"];
  $sql = "UPDATE arucikk SET raktaron=raktaron-$db WHERE id=$arucikk_id";
  mysqli_query($kapcsolat, $sql);
}

$sql = "UPDATE kosar SET rendeles_id=$rendeles_id WHERE ugyfel_id=$webshop_id AND rendeles_id=0";
mysqli_query($kapcsolat, $sql);

$ujsor = "\r\n";
$uzenet = "Kedves $webshop_nev!" . $ujsor . $ujsor;
$uzenet .= "Köszönjük szépen a rendelést!" . $ujsor;
$uzenet .= "Az alábbi termékeket fogjuk elküldeni neked:" . $ujsor . $ujsor;
$uzenet .= $mitrendelt . $ujsor;
$uzenet .= "Fizetendő végösszeg: $osszeg HUF" . $ujsor . $ujsor;

if ($fizet == 1) {
  $uzenet .= "A fizetendő összeget az alábbi bankszámlaszámra kérjük átutalni 8 napon belül:" . $ujsor;
  $uzenet .= "Számlaszám (OTP): 11702525-45789632-00000000" . $ujsor;
  $uzenet .= "Számlatulajdonos: Gyakorló WEBshop Kft." . $ujsor;
  $uzenet .= "Közleménybe: rendelés $rendeles_id" . $ujsor . $ujsor;
  $uzenet .= "A megrendelt termékeket a sikeres fizetés után postázzuk." . $ujsor . $ujsor;
} else if ($fizet == 2) {
  $uzenet .= "A megrendelt termékeket azonnal postázzuk. A fizetendő végösszeget a postásnak kell kifizetni az áru átvételekor." . $ujsor . $ujsor;
} else if ($fizet == 3) {
  $uzenet .= "A megrendelt termékeket a sikeres fizetés után azonnal postázzuk." . $ujsor . $ujsor;
}

$uzenet .= "Ha bármilyen kérdésed van a rendeléssel kapcsolatban, írj e-mailt a webshop@oktato2.info címre!" . $ujsor . $ujsor . $ujsor;
$uzenet .= "Üdvözlettel:" . $ujsor . $ujsor;
$uzenet .= "Gyakorló WEBshop" . $ujsor;

// Remove email sending
// mail($webshop_email, "Rendelésed azonosítója: $rendeles_id", $uzenet, "From: Gyakorló WEBshop<webshop@oktato2.info>");

$uzenet = "megrendelő neve: $webshop_nev" . $ujsor;
$uzenet .= "e-mail címe: $webshop_email" . $ujsor;
$uzenet .= "telefonszáma: $telefon" . $ujsor;
$uzenet .= "postacíme: $irszam $varos, $utca" . $ujsor;
$uzenet .= "számlázási neve és címe: $sz_nev, $sz_irszam $sz_varos, $sz_utca" . $ujsor . $ujsor;
$uzenet .= "Megrendelt termékek:" . $ujsor . $ujsor;
$uzenet .= $mitrendelt . $ujsor;
$uzenet .= "Fizetendő végösszeg: $osszeg HUF" . $ujsor . $ujsor;

if ($fizet == 1) {
  $uzenet .= "Fizetési mód: banki átutalás" . $ujsor;
} else if ($fizet == 2) {
  $uzenet .= "Fizetési mód: postai utánvét" . $ujsor;
} else if ($fizet == 3) {
  $uzenet .= "Fizetési mód: bankkártyás fizetés" . $ujsor;
}

mail("webshop@oktato2.info", "Új rendelés: $rendeles_id ($osszeg HUF)", $uzenet, "From: $webshop_nev<$webshop_email>");

?>

<html>

<head>
  <title>Wimu Webshop</title>
  <meta name="cache-control" content="private, no-store, no-cache, must-revalidate" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2" />
</head>

<?php include("teteje_2.php"); ?>

<div class="container mt-4">
  <div class="d-flex justify-content-end mb-3">
    <a href="index.php" class="text-decoration-none text-dark">Visszatérés a webshop-hoz</a>
  </div>
  <h2 class="text-dark">Sikeres rendelés!</h2>
  <p>A rendelésed azonosítója: <b><?= $rendeles_id ?></b></p>
  <p><?= nl2br($uzenet) ?></p>
  <p>Ha bármilyen kérdésed van a rendeléssel kapcsolatban, írj e-mailt a <a href="mailto:webshop@oktato2.info">webshop@oktato2.info</a> címre!</p>
  <p><b>Köszönjük szépen a rendelést! :-)</b></p>
  <hr>
  <p class="text-danger"><b>FIGYELEM!!!</b> Ez a weblap kizárólag oktatási céllal készült, tehát nem valódi webshop! Az oldalon található termékek csak mintaként szerepelnek, és nem rendelhetők meg! A weblapon található adatok (cég neve, számlaszám) csak fiktív adatok! Bővebb információ: <a href="mailto:info@oktatovideok.hu">info@oktatovideok.hu</a></p>
</div>

<?php include("alja_2.php"); ?>

<?php
mysqli_close($kapcsolat);
ob_end_flush();
?>
