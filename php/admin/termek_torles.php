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

// Az ID beolvasása a GET paraméterből
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$confirm = isset($_GET['confirm']) ? intval($_GET['confirm']) : 0;

// Ellenőrizzük a termék állapotát
$problémák = [];

// 1. Raktárkészlet ellenőrzése
$sql = "SELECT nev, raktaron FROM arucikk WHERE id=$id";
$eredmeny = mysqli_query($kapcsolat, $sql);
if ($sor = mysqli_fetch_array($eredmeny)) {
    $termek_nev = $sor['nev'];
    if ($sor['raktaron'] > 0) {
        $problémák[] = "A termék még raktáron van (" . $sor['raktaron'] . " db)";
    }
} else {
    die("<div class='alert alert-danger'>Nem létező termék!</div>");
}

// 2. Kosárban szerepel-e
$sql = "SELECT COUNT(*) AS db FROM kosar WHERE arucikk_id=$id";
$eredmeny = mysqli_query($kapcsolat, $sql);
$sor = mysqli_fetch_array($eredmeny);
if ($sor['db'] > 0) {
    $problémák[] = "A termék szerepel " . $sor['db'] . " kosárban";
}

// 3. Megtekintések ellenőrzése
$sql = "SELECT COUNT(*) AS db FROM megtekintve WHERE arucikk_id=$id";
$eredmeny = mysqli_query($kapcsolat, $sql);
$sor = mysqli_fetch_array($eredmeny);
if ($sor['db'] > 0) {
    $problémák[] = "A termék " . $sor['db'] . " alkalommal lett megtekintve";
}

if ($id > 0) {
    if ($confirm == 1) {
        // TÖRLÉS MEGERŐSÍTÉS UTÁN
        // 1. Kép törlése (ha van)
        $sql = "SELECT foto FROM arucikk WHERE id=$id";
        $eredmeny = mysqli_query($kapcsolat, $sql);
        if ($sor = mysqli_fetch_array($eredmeny) && !empty($sor['foto'])) {
            $kep_utvonal = "../../img/" . $sor['foto'];
            if (file_exists($kep_utvonal)) {
                unlink($kep_utvonal);
            }
        }
        
        // 2. Termék törlése
        $sql = "DELETE FROM arucikk WHERE id=$id";
        mysqli_query($kapcsolat, $sql);
        
        // 3. Kapcsolódó rekordok törlése
        $sql = "DELETE FROM kosar WHERE arucikk_id=$id";
        mysqli_query($kapcsolat, $sql);
        
        $sql = "DELETE FROM megtekintve WHERE arucikk_id=$id";
        mysqli_query($kapcsolat, $sql);
        ?>
        <html>
        <head>
            <meta name="cache-control" content="private, no-store, no-cache, must-revalidate">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <title>Termék Törölve</title>
        </head>
        <body style="font-family:tahoma">
            <div class="container mt-4">
                <div class="alert alert-success">
                    <h4>A termék sikeresen törölve!</h4>
                    <p><b><?= htmlspecialchars($termek_nev) ?></b> (ID: <?= $id ?>)</p>
                    <a href="termek_modositas.php" class="btn btn-primary">Vissza a terméklistához</a>
                </div>
            </div>
        </body>
        </html>
        <?php
    } else {
        // TÖRLÉS MEGERŐSÍTÉSE
        ?>
        <html>
        <head>
            <meta name="cache-control" content="private, no-store, no-cache, must-revalidate">
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <title>Termék Törlés - Megerősítés</title>
        </head>
        <body style="font-family:tahoma">
            <div class="container mt-4">
                <div class="alert alert-<?= empty($problémák) ? 'info' : 'warning' ?>">
                    <h4>Termék törlés - megerősítés</h4>
                    <p>Biztosan törölni szeretnéd ezt a terméket?</p>
                    <p><b><?= htmlspecialchars($termek_nev) ?></b> (ID: <?= $id ?>)</p>
                    
                    <?php if (!empty($problémák)): ?>
                    <div class="alert alert-danger mt-3">
                        <h5>Figyelmeztetés!</h5>
                        <ul>
                            <?php foreach ($problémák as $probléma): ?>
                                <li><?= $probléma ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <p>Ennek ellenére is törölheted a terméket.</p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex gap-2 mt-3">
                        <a href="termek_torles.php?id=<?= $id ?>&confirm=1" class="btn btn-danger">Igen, törlöm</a>
                        <a href="termek_modositas2.php?id=<?= $id ?>" class="btn btn-secondary">Mégse</a>
                        <a href="termek_modositas.php" class="btn btn-primary ms-auto">Vissza a listához</a>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
} else {
    echo "<div class='alert alert-danger'>Érvénytelen termék ID!</div>";
}

mysqli_close($kapcsolat);
ob_end_flush();
?>