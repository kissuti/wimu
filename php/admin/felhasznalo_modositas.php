<?php
session_start();
if (!isset($_SESSION['webshop_role']) || $_SESSION['webshop_role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    exit('Hozzáférés megtagadva!');
}

include("../dbconn.php");
include("../fuggvenyek.php");

// Felhasználó ID lekérése a GET paraméterből
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Érvénytelen felhasználó ID!");
}

// Felhasználó adatainak lekérése
$sql = "SELECT * FROM ugyfel WHERE id = ?";
$stmt = $kapcsolat->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    die("Nem található ilyen felhasználó!");
}
$user = $result->fetch_assoc();

// Ha elküldték az űrlapot, akkor frissítjük az adatokat
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Adatok tisztítása
    $nev       = trim($_POST['nev']);
    $email     = trim($_POST['email']);
    $telefon   = trim($_POST['telefon']);
    $role      = trim($_POST['role']);
    $orszag    = trim($_POST['orszag']);
    $irszam    = trim($_POST['irszam']);
    $varos     = trim($_POST['varos']);
    $utca      = trim($_POST['utca']);
    $sz_nev    = trim($_POST['sz_nev']);
    $sz_irszam = trim($_POST['sz_irszam']);
    $sz_varos  = trim($_POST['sz_varos']);
    $sz_utca   = trim($_POST['sz_utca']);
    $kulfoldi  = isset($_POST['kulfoldi']) ? 1 : 0; // Checkbox kezelése

    // Opcionális jelszó módosítás
    $uj_jelszo  = $_POST['uj_jelszo'] ?? '';
    $uj_jelszo2 = $_POST['uj_jelszo2'] ?? '';

    // Alapvető validáció
    if (empty($nev) || empty($email)) {
        $error = "A név és az email megadása kötelező!";
    } elseif (!empty($uj_jelszo) && ($uj_jelszo !== $uj_jelszo2)) {
        $error = "A jelszavak nem egyeznek!";
    } else {
        if (!empty($uj_jelszo)) {
            // Jelszó hashelése (feltételezzük, hogy password_hash()-et használsz)
            $hash = password_hash($uj_jelszo, PASSWORD_DEFAULT);
            $sql_update = "UPDATE ugyfel 
                SET nev=?, email=?, telefon=?, role=?, orszag=?, irszam=?, varos=?, utca=?, sz_nev=?, sz_irszam=?, sz_varos=?, sz_utca=?, kulfoldi=?, jelszo=?
                WHERE id=?";
            $stmt_update = $kapcsolat->prepare($sql_update);
            $stmt_update->bind_param("sssssssssssisi", $nev, $email, $telefon, $role, $orszag, $irszam, $varos, $utca, $sz_nev, $sz_irszam, $sz_varos, $sz_utca, $kulfoldi, $hash, $id);
        } else {
            $sql_update = "UPDATE ugyfel 
                SET nev=?, email=?, telefon=?, role=?, orszag=?, irszam=?, varos=?, utca=?, sz_nev=?, sz_irszam=?, sz_varos=?, sz_utca=?, kulfoldi=?
                WHERE id=?";
            $stmt_update = $kapcsolat->prepare($sql_update);
            $stmt_update->bind_param("ssssssssssssi", $nev, $email, $telefon, $role, $orszag, $irszam, $varos, $utca, $sz_nev, $sz_irszam, $sz_varos, $sz_utca, $kulfoldi, $id);
        }
        
        if ($stmt_update->execute()) {
            $success = "Felhasználó adatai sikeresen frissítve!";
            // Frissítjük a $user tömböt a legújabb adatokkal
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
        } else {
            $error = "Hiba történt a frissítés során: " . $stmt_update->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Felhasználó módosítása</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="font-family: tahoma;">
<div class="container mt-4">
    <h1 class="mb-4 text-primary">Felhasználó módosítása</h1>
    <a href="felhasznalok.php" class="btn btn-primary btn-danger mb-3">Vissza a felhasználók listájához</a>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <form action="felhasznalo_modositas.php?id=<?= $id ?>" method="POST" class="bg-light p-4 rounded">
        <div class="mb-3">
            <label class="form-label"><b>Név:</b></label>
            <input type="text" name="nev" class="form-control" value="<?= htmlspecialchars($user['nev']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label"><b>Email:</b></label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label"><b>Telefon:</b></label>
            <input type="text" name="telefon" class="form-control" value="<?= htmlspecialchars($user['telefon']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><b>Szerepkör:</b></label>
            <select name="role" class="form-select">
                <option value="user" <?= ($user['role'] === 'user' ? 'selected' : '') ?>>User</option>
                <option value="admin" <?= ($user['role'] === 'admin' ? 'selected' : '') ?>>Admin</option>
            </select>
        </div>
        <!-- Új mezők -->
        <div class="mb-3">
            <label class="form-label"><b>Ország:</b></label>
            <input type="text" name="orszag" class="form-control" value="<?= htmlspecialchars($user['orszag'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><b>Irányítószám:</b></label>
            <input type="text" name="irszam" class="form-control" value="<?= htmlspecialchars($user['irszam'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><b>Város:</b></label>
            <input type="text" name="varos" class="form-control" value="<?= htmlspecialchars($user['varos'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><b>Utca:</b></label>
            <input type="text" name="utca" class="form-control" value="<?= htmlspecialchars($user['utca'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><b>Szállítási név:</b></label>
            <input type="text" name="sz_nev" class="form-control" value="<?= htmlspecialchars($user['sz_nev'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><b>Szállítási irányítószám:</b></label>
            <input type="text" name="sz_irszam" class="form-control" value="<?= htmlspecialchars($user['sz_irszam'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><b>Szállítási város:</b></label>
            <input type="text" name="sz_varos" class="form-control" value="<?= htmlspecialchars($user['sz_varos'] ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label"><b>Szállítási utca:</b></label>
            <input type="text" name="sz_utca" class="form-control" value="<?= htmlspecialchars($user['sz_utca'] ?? '') ?>">
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" name="kulfoldi" class="form-check-input" id="kulfoldi" <?= (isset($user['kulfoldi']) && $user['kulfoldi'] == 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="kulfoldi">Külföldi felhasználó</label>
        </div>
        <hr>
        <h5>Jelszó módosítása (opcionális)</h5>
        <div class="mb-3">
            <label class="form-label"><b>Új jelszó:</b></label>
            <input type="password" name="uj_jelszo" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label"><b>Jelszó megerősítése:</b></label>
            <input type="password" name="uj_jelszo2" class="form-control">
        </div>
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-primary">Mentés</button>
        </div>
    </form>
</div>
</body>
</html>
<?php
mysqli_close($kapcsolat);
ob_end_flush();
?>
