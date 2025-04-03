<?php
include("../dbconn.php");
include("../fuggvenyek.php");

session_start();
if (!isset($_SESSION['webshop_role']) || $_SESSION['webshop_role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    exit('Hozzáférés megtagadva!');
}

// Összes felhasználó lekérdezése
$sql = "SELECT * FROM ugyfel ORDER BY id ASC";
$eredmeny = mysqli_query($kapcsolat, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Felhasználók listája</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body style="font-family: tahoma;">
<div class="container mt-4">
    <h1 class="mb-4 text-primary">Összes felhasználó listája</h1>
    <a href="../admin-index.php" class="btn btn-primary btn-danger" style="margin-bottom: 30px">Vissza az admin főoldalra</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Név</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            <?php while($sor = mysqli_fetch_assoc($eredmeny)): ?>
            <tr>
                <td><?= $sor['id'] ?></td>
                <td><?= htmlspecialchars($sor['email']) ?></td>
                <td><?= htmlspecialchars($sor['nev']) ?></td>
                <td>
                    <!-- Itt adhatsz hozzá linkeket a felhasználó törléséhez vagy módosításához -->
                    <a href="felhasznalo_modositas.php?id=<?= $sor['id'] ?>" class="btn btn-primary btn-sm">Szerkesztés</a>
                    <a href="felhasznalo_torles.php?id=<?= $sor['id'] ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('Biztosan törlöd ezt a felhasználót?');">Törlés</a>
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
