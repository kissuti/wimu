<?php
ob_start();
session_start();

include("php/dbconn.php");

header("Pragma: no-cache"); 
header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// Ha a felhasználó be van jelentkezve, akkor a session változók alapján dolgozunk
if (isset($_SESSION['webshop_id'])) {
    $id = $_SESSION['webshop_id'];
    // Opcionálisan, ha a naplózáshoz szükséges, ha a bejelentkezéskor az e-mailt is elmentetted:
    $email = isset($_SESSION['webshop_email']) ? $_SESSION['webshop_email'] : "";

    // Frissítjük az adatbázisban a session_id-t és az érvényességi időt
    $sql = "UPDATE ugyfel SET session_id='', ervenyes='0000-00-00 00:00:00' WHERE id=$id";
    mysqli_query($kapcsolat, $sql);

    // Naplózás: a kijelentkezés idejének rögzítése
    if ($email != "") {
        $idopont = date("Y-m-d H:i:s");
        $sql = "SELECT * FROM naplo WHERE email='$email' AND sikeres=1 ORDER BY id DESC LIMIT 1";
        $rs = mysqli_query($kapcsolat, $sql);
        if (mysqli_num_rows($rs) > 0) {
            $egysor = mysqli_fetch_array($rs);
            $naplo_id = $egysor["id"];
            $sql = "UPDATE naplo SET kilepes='$idopont' WHERE id=$naplo_id";
            mysqli_query($kapcsolat, $sql);
        }
    }
}

// Töröljük a session változókat és a session-t
$_SESSION = array();
session_destroy();

// Ha korábban sütiket állítottunk be, töröljük azokat is
setcookie("webshop_email", "", time() - 3600);
setcookie("webshop_jelszo", "", time() - 3600);

header("Location: index.php");

mysqli_close($kapcsolat);
ob_end_flush();
?>
