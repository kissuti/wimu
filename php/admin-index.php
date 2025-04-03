<?php
session_start();
if (!isset($_SESSION['webshop_role']) || $_SESSION['webshop_role'] !== 'admin') {
    header('HTTP/1.0 403 Forbidden');
    exit('Hozzáférés megtagadva!');
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Felület</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles/admin.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Admin Felület</h1>
        <div class="list-group mt-4">
            <a href="admin/kategoriak.php" class="list-group-item list-group-item-action border-5">Kategóriák</a>
            <a href="admin/rendeles_lista.php" class="list-group-item list-group-item-action border-5">Rendelési lista</a>
            <a href="admin/termek_felvetel.php" class="list-group-item list-group-item-action border-5">Termék felvétel</a>
            <a href="admin/termek_modositas.php" class="list-group-item list-group-item-action border-5">Termék módosítás</a>
            <a href="admin/felhasznalok.php" class="list-group-item list-group-item-action border-5">Regisztrált fiókok</a>
        </div>
        <div class="list-group mt-4">
        <a href="../index.php" class="btn btn-primary btn-danger">Vissza a főoldalra</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>
</html>
