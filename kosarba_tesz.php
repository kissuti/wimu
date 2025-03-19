<?php
ob_start();
session_start();

include("php/dbconn.php");

header("Pragma: no-cache"); 
Header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

$webshop_email = $_COOKIE['webshop_email'] ?? "";
$webshop_jelszo = $_COOKIE['webshop_jelszo'] ?? "";

$belepve = 0;
$most = date("Y-m-d H:i:s");

$arucikk_id = isset($_POST['arucikk_id']) ? intval($_POST['arucikk_id']) : 0;
$db = isset($_POST['db']) ? intval($_POST['db']) : 1;

if ($webshop_email != "" && $webshop_jelszo != "") {
  $parancs = "SELECT * from ugyfel WHERE email='$webshop_email' AND jelszo='$webshop_jelszo'";
  $eredmeny = mysqli_query($kapcsolat, $parancs);
  if ($eredmeny && mysqli_num_rows($eredmeny) > 0) {
    $sor = mysqli_fetch_array($eredmeny);
    $webshop_id = $sor["id"];
    $webshop_nev = $sor["nev"];
    $belepve = 1;
  }
}

if ($belepve == 0) {
  $parancs = "SELECT * FROM kosar WHERE session_id='" . session_id() . "' AND arucikk_id=$arucikk_id AND rendeles_id=0";
  $eredmeny = mysqli_query($kapcsolat, $parancs);
  if ($eredmeny && mysqli_num_rows($eredmeny) > 0) {
    $sor = mysqli_fetch_array($eredmeny);
    $kosar_id = $sor["id"];
    $sql = "UPDATE kosar SET db=db+$db WHERE id=$kosar_id";
    mysqli_query($kapcsolat, $sql);
  } else {
    $sql = "INSERT INTO kosar (arucikk_id, session_id, db, mikor) VALUES ($arucikk_id, '" . session_id() . "', $db, '$most')";
    mysqli_query($kapcsolat, $sql);
  }
} else {
  $parancs = "SELECT * FROM kosar WHERE ugyfel_id=$webshop_id AND arucikk_id=$arucikk_id AND rendeles_id=0";
  $eredmeny = mysqli_query($kapcsolat, $parancs);
  if ($eredmeny && mysqli_num_rows($eredmeny) > 0) {
    $sor = mysqli_fetch_array($eredmeny);
    $kosar_id = $sor["id"];
    $sql = "UPDATE kosar SET db=db+$db WHERE id=$kosar_id";
    mysqli_query($kapcsolat, $sql);
  } else {
    $sql = "INSERT INTO kosar (arucikk_id, ugyfel_id, session_id, db, mikor) VALUES ($arucikk_id, $webshop_id, '" . session_id() . "', $db, '$most')";
    mysqli_query($kapcsolat, $sql);
  }
}

mysqli_close($kapcsolat);
ob_end_flush();
?>
