<?php
ob_start();
session_start();

include("php/dbconn.php");
include("php/fuggvenyek.php");

header("Pragma: no-cache"); 
header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

$webshop_email = $_COOKIE['webshop_email'] ?? "";
$webshop_jelszo = $_COOKIE['webshop_jelszo'] ?? "";

$belepve = 0;
$most = date("Y-m-d H:i:s");

// Felhasználó azonosítás
if ($webshop_email != "" && $webshop_jelszo != "") {
    $parancs = $kapcsolat->prepare("SELECT id, nev, role FROM ugyfel WHERE email=? AND jelszo=?");
    $parancs->bind_param("ss", $webshop_email, $webshop_jelszo);
    $parancs->execute();
    $eredmeny = $parancs->get_result();

    if ($eredmeny->num_rows > 0) {
        $sor = $eredmeny->fetch_assoc();
        $webshop_id = $sor["id"];
        $webshop_nev = $sor["nev"];
        $belepve = 1;
    }
}

if ($belepve != 1) {
    header("Location: index.php");
    exit();
}

// FONTOS: Itt kell átvenni a fizetési módot
$fizet = intval($_POST['fizet'] ?? 0);
$osszeg = intval($_POST['osszeg'] ?? 0);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rendelés összegzése</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .summary-card { border: 2px solid #28a745; border-radius: 15px; }
        .total-row { background-color: #f8f9fa; font-weight: bold; }
    </style>
</head>
<body>
    <?php include("teteje.php"); ?>

    <div class="container mt-4">
        <div class="card summary-card shadow-lg">
            <div class="card-header bg-success text-white">
                <h3><i class="bi bi-cart-check"></i> Rendelés összegzése</h3>
            </div>
            
            <div class="card-body">
                <!-- Terméklista -->
                <div class="table-responsive">
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>Termék</th>
                                <th class="text-center">Darab</th>
                                <th class="text-end">Ár</th>
                                <th class="text-end">Összeg</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $osszeg = 0;
                            $sql = "SELECT k.*, a.nev, a.ar_huf 
                                    FROM kosar k
                                    JOIN arucikk a ON k.arucikk_id = a.id
                                    WHERE k.ugyfel_id = ? AND k.rendeles_id = 0";
                            $parancs = $kapcsolat->prepare($sql);
                            $parancs->bind_param("i", $webshop_id);
                            $parancs->execute();
                            $eredmeny = $parancs->get_result();

                            while ($sor = $eredmeny->fetch_assoc()):
                                $osszeg += $sor['db'] * $sor['ar_huf'];
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($sor['nev']) ?></td>
                                <td class="text-center"><?= $sor['db'] ?></td>
                                <td class="text-end"><?= szampontos($sor['ar_huf']) ?> HUF</td>
                                <td class="text-end"><?= szampontos($sor['db'] * $sor['ar_huf']) ?> HUF</td>
                            </tr>
                            <?php endwhile; ?>
                            
                            <tr class="total-row">
                                <td colspan="3" class="text-end">Összesen:</td>
                                <td class="text-end"><?= szampontos($osszeg) ?> HUF</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- FONTOS MÓDOSÍTÁS: Egyszerű form submit -->
                <form action="5_rendeles_elkuld.php" method="POST">
                    <input type="hidden" name="osszeg" value="<?= $osszeg ?>">
                    <input type="hidden" name="fizet" value="<?= $fizet ?>">
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle"></i> Rendelés véglegesítése
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include("alja.php"); ?>
</body>
</html>
<?php
mysqli_close($kapcsolat);
ob_end_flush();
?>