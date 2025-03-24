<?php
ob_start();
session_start();

include("php/dbconn.php");
include("php/fuggvenyek.php");

header("Pragma: no-cache"); 
header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// Felhasználó azonosítás
$belepve = 0;
$webshop_id = 0;

if(isset($_COOKIE['webshop_email']) && isset($_COOKIE['webshop_jelszo'])) {
    $email = mysqli_real_escape_string($kapcsolat, $_COOKIE['webshop_email']);
    $jelszo = mysqli_real_escape_string($kapcsolat, $_COOKIE['webshop_jelszo']);
    
    $sql = "SELECT * FROM ugyfel WHERE email='$email' AND jelszo='$jelszo'";
    $eredmeny = mysqli_query($kapcsolat, $sql);
    
    if(mysqli_num_rows($eredmeny) > 0) {
        $sor = mysqli_fetch_array($eredmeny);
        $webshop_id = $sor['id'];
        $belepve = 1;
    }
}

// Kosárkezelés
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['torles'])) {
        if($_POST['torles'] == 'osszes') {
            // Teljes kosár törlése
            $condition = ($belepve) ? "ugyfel_id=$webshop_id" : "session_id='".session_id()."'";
            
            // Raktár visszaállítás
            $sql = "SELECT arucikk_id, db FROM kosar WHERE $condition AND rendeles_id=0";
            $eredmeny = mysqli_query($kapcsolat, $sql);
            
            while($sor = mysqli_fetch_assoc($eredmeny)) {
                mysqli_query($kapcsolat, 
                    "UPDATE arucikk 
                     SET raktaron=raktaron+".intval($sor['db'])." 
                     WHERE id=".intval($sor['arucikk_id']));
            }
            
            // Kosár ürítése
            mysqli_query($kapcsolat, "DELETE FROM kosar WHERE $condition AND rendeles_id=0");
            
        } elseif($_POST['torles'] == 'egy' && isset($_POST['arucikk_id'])) {
            // Egy elem törlése
            $arucikk_id = intval($_POST['arucikk_id']);
            $db = intval($_POST['db']);
            
            // Raktár visszaállítás
            mysqli_query($kapcsolat, 
                "UPDATE arucikk 
                 SET raktaron=raktaron+$db 
                 WHERE id=$arucikk_id");
            
            // Elem törlése
            $condition = ($belepve) ? "ugyfel_id=$webshop_id" : "session_id='".session_id()."'";
            mysqli_query($kapcsolat, 
                "DELETE FROM kosar 
                 WHERE $condition 
                 AND arucikk_id=$arucikk_id 
                 AND rendeles_id=0 
                 LIMIT 1");
        }
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

// Kosár tartalom lekérdezése
$condition = ($belepve) ? "ugyfel_id=$webshop_id" : "session_id='".session_id()."'";
$sql = "SELECT * FROM kosar WHERE $condition AND rendeles_id=0";
$kosarEredmeny = mysqli_query($kapcsolat, $sql);
$sorok = mysqli_num_rows($kosarEredmeny);
$osszeg = 0;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Kosár megtekintése</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="styles/btn_gombok.css">
</head>
  <?php include("teteje.php"); ?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header" style="background-color: #bbbbbb;">
            <h3 class="mb-0"><i class="bi bi-cart"></i> Kosár tartalma</h3>
        </div>
        
        <div class="card-body">
            <?php if($sorok > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Termék</th>
                                <th class="text-end">Ár</th>
                                <th class="text-center">Mennyiség</th>
                                <th class="text-end">Összesen</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($sor = mysqli_fetch_array($kosarEredmeny)): 
                                $termek = mysqli_fetch_array(mysqli_query(
                                    $kapcsolat, 
                                    "SELECT * FROM arucikk WHERE id=".intval($sor['arucikk_id'])
                                ));
                                $osszeg += $sor['db'] * $termek['ar_huf'];
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($termek['nev']) ?></td>
                                <td class="text-end"><?= szampontos($termek['ar_huf']) ?> HUF</td>
                                <td class="text-center"><?= $sor['db'] ?></td>
                                <td class="text-end"><?= szampontos($sor['db'] * $termek['ar_huf']) ?> HUF</td>
                                <td class="text-end">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="torles" value="egy">
                                        <input type="hidden" name="arucikk_id" value="<?= $termek['id'] ?>">
                                        <input type="hidden" name="db" value="<?= $sor['db'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <form method="POST">
                            <input type="hidden" name="torles" value="osszes">
                            <button type="submit" class="btngombok">
                                <i class="bi bi-cart-x"></i> Teljes kosár törlése
                            </button>
                        </form>
                    </div>
                    
                    <div class="col-md-6 text-end">
                        <h4>Összesen: <span class="text-primary"><?= szampontos($osszeg) ?> HUF</span></h4>
                        <div class="mt-3">
                            <?php if($belepve): ?>
                                <a href="1_kosar_tartalma.php" class="btngombok link-offset-2 link-underline link-underline-opacity-0">
                                    <i class="bi bi-credit-card"></i> Tovább a fizetéshez
                                </a>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    A vásárlás befejezéséhez <a href="belepes.php" class="alert-link">jelentkezz be</a>!
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <h2 class="text-muted mb-4"><i class="bi bi-cart"></i> A kosár üres</h2>
                    <a href="index.php" class="btngombok link-offset-2 link-underline link-underline-opacity-0">
                        <i class="bi bi-arrow-left"></i> Vissza a termékekhez
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

  <!-- Bootstrap JS bundle (Popper included) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
          crossorigin="anonymous"></script>

</body>
</html>
<?php
mysqli_close($kapcsolat);
ob_end_flush();
?>