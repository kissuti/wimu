<?php
ob_start();

include("php/dbconn.php");

header("Pragma: no-cache"); 
Header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

if (isset($_COOKIE['webshop_email'])) {
  $webshop_email = $_COOKIE['webshop_email'];
}
else {
  $webshop_email = "";
}

if (isset($_COOKIE['webshop_jelszo'])) {
  $webshop_jelszo = $_COOKIE['webshop_jelszo'];
}
else {
  $webshop_jelszo = "";
}

if ($webshop_email!="" AND $webshop_jelszo!="") {

  $parancs = "SELECT * from ugyfel WHERE email='$webshop_email' AND jelszo='$webshop_jelszo'";
  $eredmeny = mysqli_query($kapcsolat, $parancs);
 
  if (mysqli_num_rows($eredmeny)>0) {

    $sor = mysqli_fetch_array($eredmeny);
    $id = $sor["id"];

    $sql = "UPDATE ugyfel SET session_id='', ervenyes='0000-00-00 00:00:00' WHERE id=$id";
    mysqli_query($kapcsolat, $sql);

    // NAPLOZAS:
    //---------------------------------------------------------------------------------------------------
    $idopont = date("Y-m-d H:i:s");

    $sql = "SELECT * FROM naplo WHERE email='$webshop_email' AND sikeres=1 ORDER BY id DESC";
    $rs = mysqli_query($kapcsolat, $sql);
    $egysor = mysqli_fetch_array($rs);
    $naplo_id = $egysor["id"];

    $sql = "UPDATE naplo SET kilepes='$idopont' WHERE id=$naplo_id";
    mysqli_query($kapcsolat, $sql);
    //------------------------------------------------------------------------------------------------


  }

}

setcookie("webshop_email","",1);
setcookie("webshop_jelszo","",1);

header("Location: index.php");

mysqli_close($kapcsolat);
ob_end_flush();
?>
