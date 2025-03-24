<?php
session_start();
ob_start();

include("php/dbconn.php");

header("Pragma: no-cache"); 
header("Cache-Control: private, no-store, no-cache, must-revalidate");

// Ellenőrizzük, hogy be van-e jelentkezve a felhasználó
if (!isset($_SESSION['belepve']) || $_SESSION['belepve'] != 1) {
    header("Location: belepes.php");
    exit();
}

// Adatbázisból a felhasználó adatainak lekérdezése
$id = $_SESSION['webshop_id'];
$stmt = $kapcsolat->prepare("SELECT * FROM ugyfel WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$eredmeny = $stmt->get_result();

if ($eredmeny->num_rows == 0) {
    $_SESSION['hiba'] = "Felhasználó nem található!";
    header("Location: index.php");
    exit();
}

$sor = $eredmeny->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Profil szerkesztése</title>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">ű
  <link rel="stylesheet" href="styles/profilmodosit.css">
  <script type="text/javascript">
    function helyescim(emcim) {
      return emcim.length >= 5 && emcim.indexOf("@") > 0 && emcim.length - emcim.indexOf("@") >= 6;
    }

    function isEmpty(mit) {
      return mit.trim().length === 0;
    }

    function ellenoriz() {
      const form = document.forms['urlap'];
      let mehet = true;

      if (isEmpty(form.nev.value)) {
        alert('Kérlek add meg a neved!');
        form.nev.focus();
        mehet = false;
      }

      if (mehet && !helyescim(form.email.value)) {
        alert('A megadott e-mail cím helytelen!');
        form.email.focus();
        mehet = false;
      }

      if (mehet && form.uj_jelszo.value !== form.uj_jelszo_2.value) {
        alert('A két jelszó nem egyezik!');
        form.uj_jelszo_2.focus();
        mehet = false;
      }

      return mehet;
    }
  </script>
</head>

<body style="margin-top: -39px;">
  <?php include("teteje.php"); ?>

  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <?php if (isset($_SESSION['hiba'])): ?>
          <div class="alert alert-danger"><?= $_SESSION['hiba'] ?></div>
          <?php unset($_SESSION['hiba']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['siker'])): ?>
          <div class="alert alert-success"><?= $_SESSION['siker'] ?></div>
          <?php unset($_SESSION['siker']); ?>
        <?php endif; ?>

          <div class="card-body">
            <form name="urlap" class="modositform bg-light p-4 rounded" action="profil_modosit_2.php" method="POST" onsubmit="return ellenoriz()">
              <input type="hidden" name="id" value="<?= $id ?>">
              <p class="modositszoveg fs-4 bi bi-gear-fill"> Profil módosítás</p>
              <div class="mb-3">
                <label class="form-label">Teljes név:</label>
                <input type="text" name="nev" class="form-control modositinput border-5" value="<?= htmlspecialchars($sor['nev']) ?>" required>
              </div>

              <div class="mb-3">
                <label class="form-label">E-mail cím:</label>
                <input type="email" name="email" class="form-control modositinput border-5" value="<?= htmlspecialchars($sor['email']) ?>" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Új jelszó (hagyja üresen, ha nem szeretne változtatni):</label>
                <input type="password" name="uj_jelszo" class="form-control modositinput border-5">
              </div>

              <div class="mb-3">
                <label class="form-label">Jelszó megerősítése:</label>
                <input type="password" name="uj_jelszo_2" class="form-control modositinput border-5">
              </div>
                <button type="submit" class="modositbtn" style="width: 305px;">Mentés</button>
                <a href="index.php" class="modositbtn link-offset-2 link-underline link-underline-opacity-0">Mégse</a>
            </form>
          </div>
      </div>
    </div>
  </div>

      <!-- Bootstrap JS bundle (Popper included) -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
            crossorigin="anonymous"></script>
  <?php include("alja.php"); ?>
</body>
</html>
<?php
mysqli_close($kapcsolat);
ob_end_flush();
?>