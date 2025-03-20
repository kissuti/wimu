<?php
session_start();
ob_start();

include("php/dbconn.php");

header("Pragma: no-cache"); 
header("Cache-Control: private, no-store, no-cache, must-revalidate");
?>

<html>
<head>
  <title>Wimu Webshop</title>
  <meta name="cache-control" content="private, no-store, no-cache, must-revalidate" />
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="styles/profilmodosit.css">
  
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

      if (mehet && !helyescim(form.emailcim.value)) {
        alert('A megadott e-mail cím helytelen!');
        form.emailcim.focus();
        mehet = false;
      }

      if (mehet && form.uj_jelszo.value !== form.uj_jelszo_2.value) {
        alert('A két jelszó nem egyezik!');
        form.uj_jelszo_2.focus();
        mehet = false;
      }

      return mehet;
    }

    function jelszoTeszt(passwd) {
      // ... (marad a régi függvény, nincs változás)
    }
  </script>
</head>

<body>
  <?php include("teteje.php"); ?>

  <div class="container mt-4 d-flex justify-content-center">
    <div class="col-md-8">
      <?php
      if (isset($_SESSION['hiba'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['hiba'] . '</div>';
        unset($_SESSION['hiba']);
      }
      if (isset($_SESSION['siker'])) {
        echo '<div class="alert alert-success">' . $_SESSION['siker'] . '</div>';
        unset($_SESSION['siker']);
      }

      if ($_SESSION['belepve'] ?? 0 === 1) {
        $id = $_SESSION['webshop_id'];
        $stmt = $kapcsolat->prepare("SELECT * FROM ugyfel WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $eredmeny = $stmt->get_result();

        if ($eredmeny->num_rows > 0) {
          $sor = $eredmeny->fetch_assoc();
          ?>
          <form name="urlap" action="profil_modosit_2.php" method="POST" onsubmit="return ellenoriz()" class="bg-light p-4 rounded">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="kod" value="<?= $sor['kod'] ?>">

            <div class="mb-3">
              <label>Név:</label>
              <input type="text" name="nev" class="form-control" value="<?= htmlspecialchars($sor['nev']) ?>" required>
            </div>

            <div class="mb-3">
              <label>E-mail:</label>
              <input type="email" name="emailcim" class="form-control" value="<?= htmlspecialchars($sor['email']) ?>" required>
            </div>

            <?php if (md5($sor['kod']) === $sor['jelszo']) { ?>
              <div class="alert alert-warning">Kérlek változtasd meg a jelszavad!</div>
            <?php } ?>

            <div class="mb-3">
              <label>Új jelszó:</label>
              <input type="password" name="uj_jelszo" class="form-control" oninput="jelszoTeszt(this.value)">
              <div id="erosseg" class="mt-2"></div>
            </div>

            <div class="mb-3">
              <label>Jelszó megerősítése:</label>
              <input type="password" name="uj_jelszo_2" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Módosítások mentése</button>
          </form>
          <?php
        }
      } else {
        header("Location: index.php");
        exit();
      }
      ?>
    </div>
  </div>

  <?php include("alja.php"); ?>
</body>
</html>
<?php
mysqli_close($kapcsolat);
ob_end_flush();
?>