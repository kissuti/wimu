<?php
ob_start();

session_start();

// Jogosultság ellenőrzése
if (!isset($_SESSION['webshop_role']) || $_SESSION['webshop_role'] !== 'admin') {
    header("Location: ../bejelentkezes.php");
    exit();
}

include("../../php/dbconn.php"); // DB kapcsolat helyes útvonallal
include("../../php/fuggvenyek.php");

header("Pragma: no-cache"); 
header("Cache-Control: private, no-store, no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// Változók inicializálása
$lista = isset($_GET['lista']) ? $_GET['lista'] : 'aktualis';
$mit = isset($_POST['mit']) ? $_POST['mit'] : '';
$rend_id = isset($_POST['rend_id']) ? intval($_POST['rend_id']) : 0;
$vilagos = "#E9D1D1"; // Háttérszín definiálása

// Lista szűrők kezelése
if ($mit == "" || $mit == "keres") {
    $cimke = "RENDELÉSEK";
    $szuro = "";

    // Keresés rendelés ID alapján
    if ($mit == "keres" && $rend_id > 0) {
        $szuro = "WHERE id = $rend_id";
    } else {
        switch ($lista) {
            case 'aktualis':
                $szuro = "WHERE torolve = 0 AND (kifizetve = '0000-00-00' OR teljesitve = '0000-00-00') ORDER BY id DESC";
                $cimke = "AKTUÁLIS RENDELÉSEK";
                break;
            case 'osszes':
                $szuro = "WHERE torolve = 0 ORDER BY id DESC";
                $cimke = "ÖSSZES RENDELÉS";
                break;
            case 'torolve':
                $szuro = "WHERE torolve = 1 ORDER BY id DESC";
                $cimke = "TÖRÖLT RENDELÉSEK";
                break;
            case 'kifizetve':
                $szuro = "WHERE torolve = 0 AND kifizetve > '0000-00-00' ORDER BY kifizetve DESC";
                $cimke = "KIFIZETETT RENDELÉSEK";
                break;
            case 'teljesiteni':
                $szuro = "WHERE torolve = 0 AND teljesitve = '0000-00-00' AND (((fizetesi_mod = 1 OR fizetesi_mod = 3) AND kifizetve > '0000-00-00') OR fizetesi_mod = 2) ORDER BY id DESC";
                $cimke = "TELJESÍTENDŐ RENDELÉSEK";
                break;
            case 'teljesitve':
                $szuro = "WHERE torolve = 0 AND teljesitve > '0000-00-00' ORDER BY teljesitve DESC";
                $cimke = "TELJESÍTETT RENDELÉSEK";
                break;
            default:
                $szuro = "WHERE torolve = 0 ORDER BY id DESC";
                $cimke = "ÖSSZES RENDELÉS";
        }
    }
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Rendelések kezelése</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-hover tbody tr:hover { background-color: #f8f9fa; }
        .szin-sor { background-color: <?= $vilagos ?>; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h1 class="mb-4"><?= $cimke ?></h1>
        <a href="../admin-index.php" class="btn btn-danger mb-3">Vissza a főoldalra</a>

        <!-- Keresés űrlap -->
        <form method="POST" class="mb-4 p-3 bg-white rounded shadow">
            <input type="hidden" name="mit" value="keres">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="number" name="rend_id" class="form-control border-5" placeholder="Rendelés ID">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Keresés</button>
                </div>
            </div>
        </form>

        <!-- Navigációs linkek -->
        <div class="mb-4">
            <div class="d-flex flex-wrap gap-2">
                <a href="?lista=aktualis" class="btn btn-outline-primary">Aktuális</a>
                <a href="?lista=osszes" class="btn btn-outline-primary">Összes</a>
                <a href="?lista=torolve" class="btn btn-outline-danger">Töröltek</a>
                <a href="?lista=kifizetve" class="btn btn-outline-success">Kifizetettek</a>
                <a href="?lista=teljesiteni" class="btn btn-outline-warning">Teljesítendők</a>
                <a href="?lista=teljesitve" class="btn btn-outline-info">Teljesítettek</a>
            </div>
        </div>

        <!-- Rendelések táblázata -->
        <div class="card shadow">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Megrendelő</th>
                            <th>Termékek</th>
                            <th>Összeg</th>
                            <th>Státusz</th>
                            <th>Műveletek</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM rendelesek $szuro";
                        $eredmeny = mysqli_query($kapcsolat, $sql);
                        
                        while ($sor = mysqli_fetch_assoc($eredmeny)) {
                            $statusz = "";
                            if ($sor['teljesitve'] != '0000-00-00') {
                                $statusz = "<span class='badge bg-success'>Teljesítve</span>";
                            } elseif ($sor['kifizetve'] != '0000-00-00') {
                                $statusz = "<span class='badge bg-warning'>Fizetve</span>";
                            } else {
                                $statusz = "<span class='badge bg-danger'>Függőben</span>";
                            }
                        ?>
                        <tr class="szin-sor">
                            <td>#<?= $sor['id'] ?></td>
                            <td><?= $sor['nev'] ?><br><small><?= $sor['email'] ?></small></td>
                            <td>
                                <?php
                                $termekek = "";
                                // MÓDOSÍTOTT: rendeles_tetelek táblából lekérdezés
                                $sql = "SELECT rt.db, a.nev 
                                        FROM rendeles_tetelek rt 
                                        JOIN arucikk a ON rt.arucikk_id = a.id 
                                        WHERE rt.rendeles_id = " . $sor['id'];
                                $eredmeny = mysqli_query($kapcsolat, $sql);
                                
                                if ($eredmeny && mysqli_num_rows($eredmeny) > 0) {
                                    while ($tetel = mysqli_fetch_assoc($eredmeny)) {
                                        $termekek .= $tetel['nev'] . " (" . $tetel['db'] . " db)<br>";
                                    }
                                } else {
                                    $termekek = "Nincs termék adat.";
                                }
                                echo $termekek;
                                ?>
                            </td>
                            <td><?= number_format($sor['fizetendo'], 0, ',', '.') ?> Ft</td>
                            <td><?= $statusz ?></td>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="mit" value="vegrehajt">
                                    <input type="hidden" name="id" value="<?= $sor['id'] ?>">
                                    
                                    <?php if ($sor['kifizetve'] == '0000-00-00') : ?>
                                        <button type="submit" name="kifizetes" value="1" class="btn btn-sm btn-success">Kifizetve</button>
                                    <?php endif; ?>
                                    
                                    <?php if ($sor['teljesitve'] == '0000-00-00') : ?>
                                        <button type="submit" name="teljesites" value="1" class="btn btn-sm btn-primary">Teljesítve</button>
                                    <?php endif; ?>
                                    
                                    <button type="submit" name="torles" value="1" class="btn btn-sm btn-danger">Törlés</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
} elseif ($mit == "vegrehajt") {
    // Műveletek végrehajtása
    if (isset($_POST['kifizetes'])) {
        $id = intval($_POST['id']);
        mysqli_query($kapcsolat, "UPDATE rendelesek SET kifizetve = NOW() WHERE id = $id");
    }
    
    if (isset($_POST['teljesites'])) {
        $id = intval($_POST['id']);
        mysqli_query($kapcsolat, "UPDATE rendelesek SET teljesitve = NOW() WHERE id = $id");
    }
    
    if (isset($_POST['torles'])) {
        $id = intval($_POST['id']);
        mysqli_query($kapcsolat, "UPDATE rendelesek SET torolve = 1 WHERE id = $id");
    }
    
    header("Location: rendeles_lista.php?lista=$lista"); // Visszairányítás
}

mysqli_close($kapcsolat);
ob_end_flush();
?>