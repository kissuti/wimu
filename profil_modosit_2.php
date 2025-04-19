<?php
session_start();
ob_start();

include("php/dbconn.php");

header("Pragma: no-cache"); 
header("Cache-Control: private, no-store, no-cache, must-revalidate");

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: profil_modosit.php");
    exit();
}

if (!isset($_SESSION['belepve']) || $_SESSION['belepve'] != 1) {
    $_SESSION['hiba'] = "Ehhez be kell jelentkeznie!";
    header("Location: belepes.php");
    exit();
}

$id = $_POST['id'];
$nev = trim($_POST['nev']);
$email = trim($_POST['email']);
$uj_jelszo = trim($_POST['uj_jelszo'] ?? '');
$uj_jelszo_2 = trim($_POST['uj_jelszo_2'] ?? '');

// Validáció
if (empty($nev)) {
    $_SESSION['hiba'] = "A név megadása kötelező!";
    header("Location: profil_modosit.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['hiba'] = "Érvénytelen e-mail cím formátum!";
    header("Location: profil_modosit.php");
    exit();
}

// Jelszó komplexitás ellenőrzése, ha meg van adva
if (!empty($uj_jelszo)) {
    if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $uj_jelszo)) {
        $_SESSION['hiba'] = "A jelszónak minimum 8 karakter, 1 nagybetű és 1 szám kell tartalmaznia!";
        header("Location: profil_modosit.php");
        exit();
    }

    if ($uj_jelszo !== $uj_jelszo_2) {
        $_SESSION['hiba'] = "A jelszavak nem egyeznek!";
        header("Location: profil_modosit.php");
        exit();
    }
}

// Ellenőrizzük, hogy az email már foglalt-e másik felhasználónál
$stmt = $kapcsolat->prepare("SELECT id FROM ugyfel WHERE email = ? AND id != ?");
$stmt->bind_param("si", $email, $id);
$stmt->execute();
$eredmeny = $stmt->get_result();

if ($eredmeny->num_rows > 0) {
    $_SESSION['hiba'] = "Ez az e-mail cím már foglalt!";
    header("Location: profil_modosit.php");
    exit();
}

// Jelszó frissítése, ha meg van adva
if (!empty($uj_jelszo)) {
    $jelszo_hash = password_hash($uj_jelszo, PASSWORD_DEFAULT);
    $stmt = $kapcsolat->prepare("UPDATE ugyfel SET nev = ?, email = ?, jelszo = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nev, $email, $jelszo_hash, $id);
} else {
    $stmt = $kapcsolat->prepare("UPDATE ugyfel SET nev = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nev, $email, $id);
}

if ($stmt->execute()) {
    $_SESSION['siker'] = "A profil adatai sikeresen frissítve!";
    $_SESSION['webshop_nev'] = $nev; // Frissítjük a session-ben tárolt nevet
} else {
    $_SESSION['hiba'] = "Hiba történt a frissítés során: " . $stmt->error;
}

header("Location: profil_modosit.php");
exit();
?>