<?php
ob_start();

include("php/dbconn.php");

header("Pragma: no-cache"); 
Header("Cache-control: private, no-store, no-cache, must-revalidate");
?>

<html>

<head>
  <title>Wimu Webshop</title>
  <meta name="cache-control" content="private, no-store, no-cache, must-revalidate" />
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <style>
    div{
      color: black;
    }
  </style>
  
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
      
      if (isEmpty(document.urlap.nev.value)) {
        mehet = 0;
        document.urlap.nev.focus();
        alert('Kérlek add meg a neved!');
      }

      if (mehet == 1 && !helyescim(document.urlap.emailcim.value)) {
        mehet = 0;
        document.urlap.emailcim.focus();
        alert('A megadott e-mail cím helytelen!');
      }

      if (mehet == 1 && document.urlap.uj_jelszo.value != document.urlap.uj_jelszo_2.value) {
        mehet = 0;
        document.urlap.uj_jelszo_2.focus();
        alert('A megadott két jelszó nem egyezik, kérlek javítsd ki őket!');
      }
    
      if (mehet == 1) {
        document.urlap.submit();
      } else {
        document.urlap.elkuldgomb.value = 'Kattints ide, ha jóvá szeretnéd hagyni a módosításokat!';
        document.urlap.elkuldgomb.disabled = false;
      }
    }

    function jelszoTeszt(passwd) {
      if (passwd != "") {
        var erosseg = 0;
        var eredmeny = "nagyon gyenge";
        
        // JELSZÓ HOSSZA
        if (passwd.length < 5) {
          erosseg += 3;
        } else if (passwd.length > 4 && passwd.length < 8) {
          erosseg += 6;
        } else if (passwd.length > 7 && passwd.length < 16) {
          erosseg += 12;
        } else if (passwd.length > 15) {
          erosseg += 18;
        }
        
        // BETŰK
        if (passwd.match(/[a-z]/)) {
          erosseg += 1;
        }
        
        if (passwd.match(/[A-Z]/)) {
          erosseg += 5;
        }
        
        // SZÁMOK
        if (passwd.match(/\d+/)) {
          erosseg += 5;
        }
        
        if (passwd.match(/(.*[0-9].*[0-9].*[0-9])/)) {
          erosseg += 5;
        }
        
        // SPECIÁLIS KARAKTEREK
        if (passwd.match(/.[!,@,#,$,%,^,&,*,?,_,~]/)) {
          erosseg += 5;
        }
        
        if (passwd.match(/(.*[!,@,#,$,%,^,&,*,?,_,~].*[!,@,#,$,%,^,&,*,?,_,~])/)) {
          erosseg += 5;
        }
      
        // KOMBINÁCIÓK
        if (passwd.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) {
          erosseg += 2;
        }

        if (passwd.match(/([a-zA-Z])/) && passwd.match(/([0-9])/)) {
          erosseg += 2;
        }
        
        if (passwd.match(/([a-zA-Z0-9].*[!,@,#,$,%,^,&,*,?,_,~])|([!,@,#,$,%,^,&,*,?,_,~].*[a-zA-Z0-9])/)) {
          erosseg += 2;
        }
      
        if (erosseg < 6) {
          eredmeny = "nagyon gyenge";
        } else if (erosseg < 12) {
          eredmeny = "gyenge";
        } else if (erosseg < 18) {
          eredmeny = "közepes erősségű";
        } else if (erosseg < 30) {
          eredmeny = "erős";
        } else {
          eredmeny = "nagyon erős";
        }

        document.getElementById("erosseg").innerHTML = '<span class="text-danger"><b>' + eredmeny + '</b></span>';
      } else {
        document.getElementById("erosseg").innerHTML = '';
      }
    }
  </script>

</head>

<?php include("teteje.php"); ?>

<div class="container mt-4 d-flex justify-content-center shadow">
  <div class="col-md-8">
    <?php
    // ha az ügyfél be van lépve:
    if ($belepve == 1) {
      $parancs = "SELECT * from ugyfel WHERE id=$webshop_id";
      $eredmeny = mysqli_query($kapcsolat, $parancs);

      if (mysqli_num_rows($eredmeny) > 0) {
        $sor = mysqli_fetch_array($eredmeny);
        $nev = $sor["nev"];
        $email = $sor["email"];
        $kod = $sor["kod"];
        $jelszo = $sor["jelszo"];
        ?>

        <div class="text-center mb-4">
          <a href="index.php" class="btn btn-secondary">Visszatérés a webshop-hoz</a>
        </div>
        <h2 class="mb-4 text-center">Felhasználó adatainak módosítása</h2>
        
        <form name="urlap" action="profil_modosit_2.php" method="POST" class="bg-light p-4 rounded">
          <input name="id" type="hidden" value="<?= $webshop_id ?>">
          <input name="kod" type="hidden" value="<?= $kod ?>">

          <div class="mb-3">
            <label for="nev" class="form-label">Neved:</label>
            <input id="nev" name="nev" class="form-control" value="<?= $nev ?>">
          </div>
          <div class="mb-3">
            <label for="emailcim" class="form-label">E-mail címed:</label>
            <input id="emailcim" name="emailcim" class="form-control" value="<?= $email ?>">
          </div>
          <?php if (md5($kod) == $jelszo) { ?>
            <div class="alert alert-warning">
              <b>Kérlek változtasd meg a jelszavad!</b>
            </div>
          <?php } ?>
          <div class="mb-3">
            <label for="uj_jelszo" class="form-label">Új jelszó:</label>
            <input type="password" id="uj_jelszo" name="uj_jelszo" class="form-control" oncontextmenu="return false" ondragstart="return false" onselectstart="return false" onkeypress="jelszoTeszt(this.value)" onkeyup="jelszoTeszt(this.value)" onchange="jelszoTeszt(this.value)">
            <div id="erosseg" class="mt-2"></div>
          </div>
          <div class="mb-3">
            <label for="uj_jelszo_2" class="form-label">Új jelszó mégegyszer:</label>
            <input type="password" id="uj_jelszo_2" name="uj_jelszo_2" class="form-control" oncontextmenu="return false" ondragstart="return false" onselectstart="return false">
          </div>

          <button id="elkuldgomb" name="elkuldgomb" type="button" class="btn btn-primary w-100" onclick="this.disabled='disabled';this.value='Kis türelmet kérek, az ellenőrzés folyamatban van...';ellenoriz()">Kattints ide, ha jóvá szeretnéd hagyni a módosításokat!</button>
        </form>

        <?php
      } else {
        // visszaküldjük a nyitólapra
        header("Location: index.php");
      }
    } else {
      // visszaküldjük a nyitólapra
      header("Location: index.php");
    }
    ?>
  </div>
</div>

<?php include("alja.php"); ?>

<?php
mysqli_close($kapcsolat);
ob_end_flush();
?>
