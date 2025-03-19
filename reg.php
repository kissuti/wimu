<?php
ob_start();

include("php/dbconn.php");

header("Pragma: no-cache"); 
Header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
?>

<html>

<head>
  <title>Wimu Webshop</title>
  <meta charset="UTF-8">
  <meta name="cache-control" content="private, no-store, no-cache, must-revalidate" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" type="text/css" href="styles/reg.css">
  <link rel="stylesheet" href="styles/preloader.css">
  <meta http-equiv="Content-Type" content="text/html" />
  
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
      mehet = 1;
      if (mehet == 1 && !helyescim(document.urlap.emailcim.value)) {
        mehet = 0;
        document.urlap.emailcim.focus();
        alert('A megadott e-mail cím helytelen!');
      }
      if (mehet == 1 && isEmpty(document.urlap.nev.value)) {
        mehet = 0;
        document.urlap.nev.focus();
        alert('Kérlek add meg a neved!');
      }
      if (mehet == 1 && isEmpty(document.urlap.jelszo.value)) {
        mehet = 0;
        document.urlap.jelszo.focus();
        alert('Kérlek add meg a jelszavad!');
      }
      if (mehet == 1 && document.urlap.jelszo.value !== document.urlap.jelszo2.value) {
        mehet = 0;
        document.urlap.jelszo2.focus();
        alert('A két jelszó nem egyezik!');
      }
      if (mehet == 1) {
        document.urlap.submit();
      } else {
        document.urlap.elkuldgomb.value = 'Kattints ide a regisztrációhoz!';
        document.urlap.elkuldgomb.disabled = false;
      }
    }
  </script>
  <style>
    div {
      color: black;
    }
  </style>
<script src="js/preloader.js"></script>
</head>

<?php include("teteje.php"); ?>

  <!-- Preloader -->
  <div id="preloader">
    <div class="spinner"></div>
  </div>
<div class="container mt-4 d-flex justify-content-center shadow border kulonshadow" id="main-content">
  <div class="col-md-8 kulonshadow">
    
    <form name="urlap" action="reg_ellenoriz.php" method="POST" class="bg-light p-4 rounded regform">
      <p class="szovegreg fs-4">Regisztráció</p>
      <div class="mb-3">
        <input id="emailcim" name="emailcim" class="form-control beiras border-5" placeholder="E-mail cím">
      </div>
      <div class="mb-3">
        <input id="nev" name="nev" class="form-control beiras border-5" placeholder="Név">
      </div>
      <div class="mb-3">
        <input type="password" id="jelszo" name="jelszo" class="form-control beiras border-5" placeholder="Jelszó">
      </div>
      <div class="mb-3">
        <input type="password" id="jelszo2" name="jelszo2" class="form-control beiras border-5" placeholder="Jelszó újra">
      </div>

      <button id="elkuldgomb" name="elkuldgomb" type="button" class="regbtn" onclick="this.disabled='disabled';this.value='Kis türelmet kérek, az ellenőrzés folyamatban van...';ellenoriz()">Kattints ide a regisztrációhoz!</button>
    </form>
  </div>
</div>

<?php include("alja.php"); ?>

<?php
mysqli_close($kapcsolat);
ob_end_flush();
?>
