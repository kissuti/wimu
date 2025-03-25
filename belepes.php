<?php
ob_start();
session_start();

include("php/dbconn.php");
include("php/fuggvenyek.php");

header("Pragma: no-cache"); 
header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

$mit = isset($_POST['mit']) ? $_POST['mit'] : "";

if ($mit == "") {
  $email = isset($_POST['email']) ? $_POST['email'] : "";
  ?>
  <html>
  <head>
    <title>Wimu Webshop</title>
    <meta charset="UTF-8">
    <meta name="cache-control" content="private, no-store, no-cache, must-revalidate" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="styles/belepes.css">
    <link rel="stylesheet" href="styles/preloader.css">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <script type="text/javascript">
      function helyescim(emcim) {
        if (emcim.length < 5) {
          return false;
        } else if (emcim.indexOf("@") < 1) {
          return false;
        } else if (emcim.length - emcim.indexOf("@") < 6) {
          return false;
        } else {
          return true;
        }
      }

      function isEmpty(mit) {
        if ((mit.length == 0) || (mit == null)) {
          return true;
        } else {
          return false;
        }
      }

      function ellenoriz() {
        var mehet = 1;
        
        if (mehet == 1 && !helyescim(document.urlap.email.value)) {
          mehet = 0;
          document.urlap.email.focus();
          alert('A megadott e-mail cím helytelen!');
        }

        if (mehet == 1 && isEmpty(document.urlap.jelszo.value)) {
          mehet = 0;
          document.urlap.jelszo.focus();
          alert('Kérlek add meg a jelszavad!');
        }
      
        if (mehet == 1) {
          document.urlap.submit();
        } else {
          document.urlap.elkuldgomb.value = 'Belépés';
          document.urlap.elkuldgomb.disabled = false;
        }
      }
    </script>
    <script src="js/preloader.js"></script>
  </head>
  <?php include("teteje.php"); ?>
    <!-- Preloader -->
    <div id="preloader">
      <div class="spinner"></div>
    </div>
  <div class="container mt-4 d-flex justify-content-center" id="main-content">
    <div class="col-md-8">
      <form name="urlap" action="belepes.php" method="POST" class="bg-light p-4 rounded belepesform">
        <p class="szovegbel fs-4 bi bi-box-arrow-in-right"> Belépés</p>
        <input type="hidden" name="mit" value="ellenoriz">
        <input type="hidden" name="elkuld" value="">
        <div class="mb-3">
          <input name="email" class="form-control beiras border-5" value="<?= $email ?>" placeholder="E-mail cím">
        </div>
        <div class="mb-3">
          <input type="password" name="jelszo" class="form-control beiras border-5" placeholder="Jelszó">
        </div>
        <?php
        if (isset($_GET['hiba'])) {
          ?>
          <div class="alert alert-danger" role="alert">
            Hibás e-mail címet vagy jelszót adtál meg!
          </div>
          <?php
        }
        elseif (isset($_GET['tilos'])) {
          ?>
          <div class="alert alert-danger" role="alert">
            Az elmúlt 30 percben legalább 3 sikertelen belépési kísérleted volt!
          </div>
          <?php
        }
        elseif (isset($_GET['marbevan'])) {
          ?>
          <div class="alert alert-danger" role="alert">
            Már be vagy jelentkezve egy másik gépen!
          </div>
          <?php
        }
        ?>
        <button id="elkuldgomb" name="elkuldgomb" type="button" class="belepesbtn" onclick="this.disabled='disabled';this.value='Kis türelmet kérek, az ellenőrzés folyamatban van...';ellenoriz()">Belépés</button>
        <div class="mt-4 text-center">Még nincs fiókja? <a href="reg.php" class="text-primary">Regisztrálj!</a></div>
      </form>
    </div>
  </div>
    <!-- Bootstrap JS bundle (Popper included) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
            crossorigin="anonymous"></script>
            <?php include("alja.php"); ?>

<?php
} elseif ($mit == "ellenoriz") {
$email = isset($_POST['email']) ? $_POST['email'] : "";
$jelszo = isset($_POST['jelszo']) ? $_POST['jelszo'] : "";

$most = date("Y-m-d H:i:s");

$parancs = "SELECT * from ugyfel WHERE email='$email'";
$eredmeny = mysqli_query($kapcsolat, $parancs);

if (mysqli_num_rows($eredmeny) > 0) {
  $sor = mysqli_fetch_array($eredmeny);
  if (password_verify($jelszo, $sor['jelszo'])) {
    $feloraja = date("Y-m-d H:i:s", time() - 1800);

    $sql = "SELECT count(*) as darab FROM naplo WHERE email='$email' AND mikor>='$feloraja' AND sikertelen=1";
    $rs_naplo = mysqli_query($kapcsolat, $sql);
    $naplo_sor = mysqli_fetch_array($rs_naplo);
    $darab = $naplo_sor["darab"];

    if ($darab >= 3) {
      header("Location: belepes.php?tilos=1&email=$email");
    } else {
      setcookie("webshop_email", $email, time() + 86400 * 7);
      setcookie("webshop_jelszo", $sor['jelszo'], time() + 86400 * 7);
      
      $id = $sor["id"];
      $kod = $sor["kod"];
      $jelszo = $sor["jelszo"];

      $ervenyes = date("Y-m-d H:i:s", time() + 3600);

      $sql = "UPDATE ugyfel SET session_id='" . session_id() . "', ervenyes='$ervenyes' WHERE id=$id";
      mysqli_query($kapcsolat, $sql);

      $sql = "insert into naplo (email, mikor, sikeres) values ('$email', '$most', 1)";
      mysqli_query($kapcsolat, $sql);

      $sql = "UPDATE kosar SET ugyfel_id=$id WHERE session_id='" . session_id() . "' AND rendeles_id=0";
      mysqli_query($kapcsolat, $sql);

      header("Location: index.php");
    }
  } else {
    $sql = "insert into naplo (email, mikor, sikertelen) values ('$email', '$most', 1)";
    mysqli_query($kapcsolat, $sql);

    header("Location: belepes.php?hiba=1&email=$email");
  }
} else {
  header("Location: belepes.php?hiba=1&email=$email");
}
}

mysqli_close($kapcsolat);
ob_end_flush();
?>
