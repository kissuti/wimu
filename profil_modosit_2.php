<?php
session_start();
ob_start();
include("php/dbconn.php");

// Ellenőrizzük a bejelentkezést
if (!isset($_SESSION['belepve']) || $_SESSION['belepve'] !== 1) {
  header("Location: index.php");
  exit();
}

// Adatok fogadása és szűrése
$id = (int)$_POST['id'];
$kod = mysqli_real_escape_string($kapcsolat, $_POST['kod']);
$emailcim = filter_var($_POST['emailcim'], FILTER_SANITIZE_EMAIL);
$nev = mysqli_real_escape_string($kapcsolat, $_POST['nev']);
$uj_jelszo = $_POST['uj_jelszo'] ?? '';

// Ellenőrzés
if (empty($id) || empty($kod) || !filter_var($emailcim, FILTER_VALIDATE_EMAIL)) {
  $_SESSION['hiba'] = "Érvénytelen adatok!";
  header("Location: profil_modosit.php");
  exit();
}

// Adatbázis frissítés
try {
  // Ellenőrizzük, hogy a felhasználó létezik
  $stmt = $kapcsolat->prepare("SELECT * FROM ugyfel WHERE id = ? AND kod = ?");
  $stmt->bind_param("is", $id, $kod);
  $stmt->execute();
  $eredmeny = $stmt->get_result();

  if ($eredmeny->num_rows === 0) {
    throw new Exception("Érvénytelen azonosító vagy kód!");
  }

  $sor = $eredmeny->fetch_assoc();

  // Frissítjük az emailt és nevet
  $updateSql = "UPDATE ugyfel SET email = ?, nev = ? WHERE id = ?";
  $stmt = $kapcsolat->prepare($updateSql);
  $stmt->bind_param("ssi", $emailcim, $nev, $id);
  $stmt->execute();

  // Jelszó frissítés (ha meg van adva)
  if (!empty($uj_jelszo)) {
    $uj_jelszo_hash = md5($uj_jelszo);
    $updateJelszo = "UPDATE ugyfel SET jelszo = ? WHERE id = ?";
    $stmt = $kapcsolat->prepare($updateJelszo);
    $stmt->bind_param("si", $uj_jelszo_hash, $id);
    $stmt->execute();
  }

  $_SESSION['siker'] = "Sikeres módosítás!";
} catch (Exception $e) {
  $_SESSION['hiba'] = $e->getMessage();
}

header("Location: profil_modosit.php");
exit();
?>