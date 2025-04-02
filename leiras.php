<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_lifetime', 86400); // 24 óra

// Session indítása
session_start();
ob_start();

include("php/dbconn.php");
include("php/fuggvenyek.php");

// Paraméterek biztonságos átvétele
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$oldal = isset($_GET['oldal']) ? intval($_GET['oldal']) : 1;
$laponkent = isset($_GET['laponkent']) ? intval($_GET['laponkent']) : 5;
$kat1 = isset($_GET['kat1']) ? intval($_GET['kat1']) : 0;
$kat2 = isset($_GET['kat2']) ? intval($_GET['kat2']) : 0;
$kat3 = isset($_GET['kat3']) ? intval($_GET['kat3']) : 0;
$jogosultsag = isset($_GET['jogosultsag']) ? $_GET['jogosultsag'] : 0;
$mitkeres = isset($_GET['mitkeres']) ? urldecode($_GET['mitkeres']) : "";
$irany = isset($_GET['irany']) ? $_GET['irany'] : "";

// Termék ID ellenőrzése
if($id < 1) {
    header("Location: index.php?error=invalid_id");
    exit;
}

// Termék lekérdezése
$sql = "SELECT * FROM arucikk WHERE id = $id";
$eredmeny = mysqli_query($kapcsolat, $sql);

if(mysqli_num_rows($eredmeny) == 0) {
    header("Location: index.php?error=product_not_found");
    exit;
}

$sor = mysqli_fetch_array($eredmeny);

// Termék megtekintésének rögzítése
if ($id > 0) {
    $ugyfel_id = isset($_SESSION['webshop_id']) ? intval($_SESSION['webshop_id']) : 0;
    $session_id = session_id();
    
    // Hibakereséshez: naplózzuk a session_id-t
    error_log("Session ID: " . $session_id);
    
    $sql_check = "SELECT id FROM megtekintve 
                  WHERE arucikk_id = $id 
                  AND (
                      (ugyfel_id = $ugyfel_id AND ugyfel_id > 0) 
                      OR 
                      (session_id = '$session_id' AND ugyfel_id = 0)
                  ) 
                  AND mikor > NOW() - INTERVAL 1 DAY
                  LIMIT 1";
                  
    $check_result = mysqli_query($kapcsolat, $sql_check);
    
    if (!$check_result) {
        error_log("Hiba a lekérdezésben: " . mysqli_error($kapcsolat));
    }
    
    if (mysqli_num_rows($check_result) == 0) {
        $sql_insert = "INSERT INTO megtekintve (ugyfel_id, session_id, arucikk_id) 
                       VALUES ($ugyfel_id, '$session_id', $id)";
        if (!mysqli_query($kapcsolat, $sql_insert)) {
            error_log("Hiba a beszúrásnál: " . mysqli_error($kapcsolat));
        } else {
            error_log("Sikeres beszúrás: " . $sql_insert);
        }
    }
}

// Adatok kinyerése
$nev = htmlspecialchars($sor['nev']);
$nev2 = htmlspecialchars($sor['nev2']);
$foto = htmlspecialchars($sor['foto']);
$raktaron = $sor['raktaron'];
$leiras = nl2br(htmlspecialchars($sor['hosszu_leiras']));
$ar_huf = number_format($sor['ar_huf'], 0, ',', ' ');
$egyseg = htmlspecialchars($sor['egyseg']);

// Vissza URL generálása
$back_params = [
    'oldal' => $oldal,
    'laponkent' => $laponkent,
    'kat1' => $kat1,
    'kat2' => $kat2,
    'kat3' => $kat3,
    'jogosultsag' => $jogosultsag,
    'mitkeres' => $mitkeres,
    'irany' => $irany
];
$back_url = "index.php?".http_build_query($back_params);

// Cache vezérlő fejlécek
header("Pragma: no-cache");
header("Cache-Control: private, no-store, no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Wimu Webshop - <?= $nev ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="styles/leiras.css">
    <style>
        .product-image { max-width: 400px; margin: 20px; }
        .price { font-size: 24px; color: #d00; margin: 20px 0; }
        .description { margin: 30px 0; }
        div{
            color: black;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("form.add-to-cart").submit(function(event) {
                event.preventDefault();
                var form = $(this);
                $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(response) {
                        alert("A termék sikeresen hozzáadva a kosárhoz!");
                        $("#kosar").attr("src", "kosar.php"); // Reload the cart iframe
                    },
                    error: function() {
                        alert("Hiba történt a termék kosárba helyezése során.");
                    }
                });
            });
        });
    </script>
</head>
<body>

<?php include("teteje.php"); ?>

<div class="container mt-4">
    <a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-secondary mb-4">&laquo; Vissza a webshophoz</a>

    <div class="row">
        <div class="col-md-6">
            <img src="img/<?= $foto ?>" class="img-fluid product-image img-thumbnail shadow-lg " alt="<?= $nev ?>">
        </div>
        
        <div class="col-md-6 p-5">
            <h1><?= $nev ?></h1>
            <h3 class="text-muted"><?= $nev2 ?></h3>
            
            <div class="price fw-bold p-2">Ár: <?= $ar_huf ?> HUF</div>
            
            <form class="add-to-cart" action="kosarba_tesz.php" method="POST" class="mb-4">
                <input type="hidden" name="arucikk_id" value="<?= $id ?>">
                
                <div class="input-group mb-3 shadow-lg" style="max-width: 140px;">
                    <input type="number" name="db" class="form-control" value="1" min="1" max="<?= $raktaron ?>">
                    <span class="input-group-text"><?= $egyseg ?></span>
                </div>
                
                <div class="mt-3" style="margin-bottom: 20px;">
                    <?php if($raktaron > 0): ?>
                        <span class="badge bg-success fs-5">Raktáron: <?= $raktaron ?> <?= $egyseg ?></span>
                    <?php else: ?>
                        <span class="badge bg-danger">Elfogyott!</span>
                    <?php endif; ?>
                </div>
                <button type="submit" class="kosarbtn"><span>Kosárba</span>
                </button>
                
            </form>
        </div>
    </div>

    <div class="description">
        <h3>Termékleírás</h3>
        <div class="card p-3">
            <?= $leiras ?>
        </div>
    </div>
</div>

<?php include("alja.php"); ?>

    <!-- Bootstrap JS bundle (Popper included) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
          crossorigin="anonymous"></script>

</body>
</html>

<?php
// Adatbázis kapcsolat lezárása
mysqli_close($kapcsolat);

// Output buffer ürítése
ob_end_flush();
?>