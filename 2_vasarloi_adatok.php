<?php
ob_start();
session_start();

include("php/dbconn.php");
include("php/fuggvenyek.php");

header("Pragma: no-cache"); 
Header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

$webshop_email = $_COOKIE['webshop_email'] ?? "";
$webshop_jelszo = $_COOKIE['webshop_jelszo'] ?? "";

$belepve = 0;
$most = date("Y-m-d H:i:s");

if ($webshop_email != "" && $webshop_jelszo != "") {
  $parancs = "SELECT * FROM ugyfel WHERE email='$webshop_email' AND jelszo='$webshop_jelszo'";
  $eredmeny = mysqli_query($kapcsolat, $parancs);
  if (mysqli_num_rows($eredmeny) > 0) {
    $sor = mysqli_fetch_array($eredmeny);
    $webshop_id = $sor["id"];
    $webshop_nev = $sor["nev"];
    $telefon = $sor["telefon"];
    $kulfoldi = $sor["kulfoldi"];
    $orszag = $sor["orszag"];
    $irszam = $sor["irszam"];
    $varos = $sor["varos"];
    $utca = $sor["utca"];
    $sz_nev = $sor["sz_nev"];
    $sz_irszam = $sor["sz_irszam"];
    $sz_varos = $sor["sz_varos"];
    $sz_utca = $sor["sz_utca"];

    if ($kulfoldi == 0 && $orszag == "") {
      $orszag = "Magyarország";
    }
    if ($kulfoldi == 1 && $orszag == "") {
      $orszag = "";
    }
    if ($irszam == "") {
      $irszam = "Ir.szám";
    }
    if ($varos == "") {
      $varos = "Város v. Helységnév";
    }
    if ($utca == "") {
      $utca = "utca, házszám stb.";
    }
    if ($sz_irszam == "") {
      $sz_irszam = "ir.szám";
    }
    if ($sz_varos == "") {
      $sz_varos = "Város v. Helységnév";
    }
    if ($sz_utca == "") {
      $sz_utca = "utca, házszám stb.";
    }

    $belepve = 1;
  }
}

if ($belepve == 1) {
  ?>

  <html>

  <head>
    <title>Wimu Webshop</title>
    <meta name="cache-control" content="private, no-store, no-cache, must-revalidate">
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/btn_gombok.css">
    <link rel="stylesheet" href="styles/form.css">
    <meta http-equiv="Content-Type" content="text/html">

    <script type="text/javascript">
      function isEmpty(mit) {
        return (mit.length == 0 || mit == null);
      }

      function ellenoriz() {
        let mehet = true;

        if (mehet && isEmpty(document.urlap.nev.value)) {
          mehet = false;
          document.urlap.nev.focus();
          alert('Kérlek add meg a nevedet!');
        }

        if (mehet && (isEmpty(document.urlap.telefon.value) || document.urlap.telefon.value == '06-')) {
          mehet = false;
          document.urlap.telefon.focus();
          alert('Kérlek add meg a telefonszámodat!');
        }

        if (mehet && document.urlap.kulfoldi[1].checked && (isEmpty(document.urlap.orszag.value) || document.urlap.orszag.value == 'ország neve')) {
          mehet = false;
          document.urlap.orszag.focus();
          alert('Kérlek add meg az ország nevét!');
        }

        if (mehet && (isEmpty(document.urlap.irszam.value) || document.urlap.irszam.value == 'ir.szám')) {
          mehet = false;
          document.urlap.irszam.focus();
          alert('Kérlek add meg az irányítószámot!');
        }

        if (mehet && (isEmpty(document.urlap.varos.value) || document.urlap.varos.value == 'város v. helységnév')) {
          mehet = false;
          document.urlap.varos.focus();
          alert('Kérlek add meg a város nevét is!');
        }

        if (mehet && (isEmpty(document.urlap.utca.value) || document.urlap.utca.value == 'utca, házszám stb.')) {
          mehet = false;
          document.urlap.utca.focus();
          alert('Kérlek add meg a pontos címedet!');
        }

        if (mehet && isEmpty(document.urlap.sz_nev.value)) {
          mehet = false;
          document.urlap.sz_nev.focus();
          alert('Kérlek add meg a számlázási nevet!');
        }

        if (mehet && (isEmpty(document.urlap.sz_irszam.value) || document.urlap.sz_irszam.value == 'ir.szám')) {
          mehet = false;
          document.urlap.sz_irszam.focus();
          alert('A számlázási címnél nem adtad meg az irányítószámot!');
        }

        if (mehet && (isEmpty(document.urlap.sz_varos.value) || document.urlap.sz_varos.value == 'város v. helységnév')) {
          mehet = false;
          document.urlap.sz_varos.focus();
          alert('A számlázási címnél add meg a város nevét is!');
        }

        if (mehet && (isEmpty(document.urlap.sz_utca.value) || document.urlap.sz_utca.value == 'utca, házszám stb.')) {
          mehet = false;
          document.urlap.sz_utca.focus();
          alert('Utcanév és házszám is kell a számlázási címhez!');
        }

        if (mehet) {
          document.urlap.submit();
        } else {
          document.urlap.tovabb.value = 'A fizetési mód kiválasztása >>';
          document.urlap.tovabb.disabled = false;
        }
      }
    </script>

  </head>

  <body>
    <?php include("teteje.php"); ?>

    <div class="container mt-4">

      <h2 class="text-dark">Vásárlói adatok megadása</h2>
      
      <form name="urlap" action="3_fizetesi_modok.php" method="POST" class="bg-light p-4 rounded border">
        <input type="hidden" name="osszeg" value="<?= $_POST['osszeg'] ?? '' ?>">
        <h4>Rendeléshez szükséges adatok:</h4>

        <div class="mb-3">
          <label for="nev" class="form-label">Név/Cégnév:</label>
          <input type="text" name="nev" class="form-control border-3" value="<?= $webshop_nev ?>">
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">E-mail cím:</label>
          <input type="email" name="email" class="form-control border-3" value="<?= $webshop_email ?>" readonly>
        </div>

        <div class="mb-3">
          <label for="telefon" class="form-label">Telefonszám:</label>
          <input type="text" name="telefon" class="form-control border-3" value="<?= $telefon ?>" placeholder="+36">
        </div>

        <!-- HTML rész az ország mezővel -->
        <div class="mb-3">
          <label for="orszag" class="form-label">Ország:</label>
          <div>
            <input type="radio" name="kulfoldi" value="0" onclick="document.getElementById('orszag').value='Magyarország'; document.getElementById('orszag').style.display='none'" <?php if ($kulfoldi == 0) { print "checked"; } ?>> Magyarország
            &nbsp;&nbsp;
            <input type="radio" name="kulfoldi" value="1" onclick="document.getElementById('orszag').value=''; document.getElementById('orszag').style.display=''" <?php if ($kulfoldi == 1) { print "checked"; } ?>> Külföld
            &nbsp;&nbsp;
            <input <?php if ($kulfoldi == 0) { print 'style="display:none"'; } ?> type="text" name="orszag" id="orszag" class="form-control d-inline-block border-3" style="width: auto;" value="<?= htmlspecialchars($orszag) ?>" onfocus="if (this.value=='ország neve') {this.value=''}">
          </div>
        </div>

        <div class="mb-3">
          <label for="irszam" class="form-label">Postázási cím:</label>
          <div class="d-flex">
            <input type="text" name="irszam" class="form-control me-2 border-3" style="width: 100px;" value="<?= $irszam ?>" onfocus="if (this.value=='ir.szám') {this.value=''}">
            <input type="text" name="varos" class="form-control me-2 border-3" value="<?= $varos ?>" onfocus="if (this.value=='város v. helységnév') {this.value=''}">
            <input type="text" name="utca" class="form-control border-3" value="<?= $utca ?>" onfocus="if (this.value=='utca, házszám stb.') {this.value=''}">
          </div>
        </div>

        <div class="mb-3">
          <p>A fenti név és postacím alapján címezzük meg a csomagot, ezért kérlek úgy add meg az adatokat, hogy a postás biztosan megtaláljon!</p>
        </div>

        <h4>Az alábbi mezőkben adhatod meg az ÁFÁ-s számlára kerülő adatokat:</h4>

        <div class="mb-3">
          <a style="cursor:pointer" onclick="document.urlap.sz_nev.value=document.urlap.nev.value; document.urlap.sz_irszam.value=document.urlap.irszam.value; document.urlap.sz_varos.value=document.urlap.varos.value; document.urlap.sz_utca.value=document.urlap.utca.value;">
            <u>Kattints ide, ha a számlázási név és cím megegyezik a fenti névvel és címmel!</u>
          </a>
        </div>

        <div class="mb-3">
          <label for="sz_nev" class="form-label">Számlázási név:</label>
          <input type="text" name="sz_nev" class="form-control border-3" value="<?= $sz_nev ?>">
        </div>

        <div class="mb-3">
          <label for="sz_irszam" class="form-label">Számlázási cím:</label>
          <div class="d-flex">
            <input type="text" name="sz_irszam" class="form-control me-2 border-3" style="width: 100px;" value="<?= $sz_irszam ?>" onfocus="if (this.value=='ir.szám') {this.value=''}">
            <input type="text" name="sz_varos" class="form-control me-2 border-3" value="<?= $sz_varos ?>" onfocus="if (this.value=='város v. helységnév') {this.value=''}">
            <input type="text" name="sz_utca" class="form-control border-3" value="<?= $sz_utca ?>" onfocus="if (this.value=='utca, házszám, emelet, ajtó') {this.value=''}">
          </div>
        </div>

        <div class="mb-3">
          <button type="button" name="tovabb" id="tovabb" class="btngombok w-100" onclick="this.value='Ellenőrzés folyamatban...'; this.disabled=true; ellenoriz()">A fizetési mód kiválasztása >></button>
        </div>
      </form>
    </div>

    <?php include("alja.php"); ?>
            <!-- Bootstrap JS bundle (Popper included) -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
          crossorigin="anonymous"></script>
  </body>
  </html>

  <?php
} else {
  header("Location: index.php");
}

mysqli_close($kapcsolat);
ob_end_flush();
?>
