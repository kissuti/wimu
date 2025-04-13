<?php
include("../dbconn.php");
include("../fuggvenyek.php");

session_start();
if (!isset($_SESSION['webshop_role']) || $_SESSION['webshop_role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    exit('Hozzáférés megtagadva!');
}

// Összes árucikk lekérdezése
$sql = "SELECT * FROM arucikk ORDER BY id ASC";
$eredmeny = mysqli_query($kapcsolat, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Termékek listája</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body style="font-family: tahoma;">
<div class="container mt-4">
    <h1 class="mb-4 text-primary">Összes árucikk listája</h1>
    <a href="../admin-index.php" class="btn btn-primary btn-danger" style="margin-bottom: 30px">Vissza az admin főoldalra</a>
    <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Név</th>
                <th>Rövid név</th>
                <th>Ár (HUF)</th>
                <th>Raktáron</th>
                <th>Kép</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            <?php while($sor = mysqli_fetch_assoc($eredmeny)): ?>
            <tr>
                <td><?= $sor['id'] ?></td>
                <td><?= htmlspecialchars($sor['nev']) ?></td>
                <td><?= htmlspecialchars($sor['rovidnev']) ?></td>
                <td><?= $sor['ar_huf'] ?></td>
                <td><?= $sor['raktaron'] ?></td>
                <td>
                    <?php if (!empty($sor['foto'])): ?>
                        <img src="../../img/<?= htmlspecialchars($sor['foto']) ?>" alt="Termékkép" style="height: 80px;">
                    <?php else: ?>
                        <span class="text-muted">Nincs kép</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="termek_modositas2.php?id=<?= $sor['id'] ?>" class="btn btn-primary btn-sm">Szerkesztés</a>
                    <a href="termek_torles.php?id=<?= $sor['id'] ?>" class="btn btn-danger btn-sm" 
                    onclick="return confirm('Biztosan törlöd ezt a terméket?');">
                    Törlés
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>

    </table>
</div>
<?php
mysqli_close($kapcsolat);
?>
</body>
</html>
