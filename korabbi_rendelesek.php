<?php
ob_start();

include("php/dbconn.php");
include("php/fuggvenyek.php");

header("Pragma: no-cache"); 
Header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

if (!isset($id)) {
  ?>

  <html>

  <head>
    <title>Wimu Webshop</title>
    <meta name="cache-control" content="private, no-store, no-cache, must-revalidate" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

  </head>

  <body>
    <?php include("teteje_2.php"); ?>


      <h2 class="text-dark">Korábbi rendeléseid</h2>
      
      <form name="urlap" action="korabbi_rendelesek.php" method="POST">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Dátum és időpont</th>
                <th>Összeg</th>
                <th>Állapot</th>
                <th>Részletek</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM rendelesek WHERE ugyfel_id=$webshop_id ORDER BY idopont";
              $eredmeny = mysqli_query($kapcsolat, $sql);

              $volt = 0;
              
              while ($sor = mysqli_fetch_array($eredmeny)) {
                
                $volt = 1;

                $id = $sor["id"];
                $idopont = $sor["idopont"];
                $fizetendo = $sor["fizetendo"];
                $fizetesi_mod = $sor["fizetesi_mod"];
                $kifizetve = $sor["kifizetve"];
                $teljesitve = $sor["teljesitve"];
                if ($fizetesi_mod == 2) {
                  if ($kifizetve == "0000-00-00") {
                    if ($teljesitve == "0000-00-00") {
                      $allapot = "feldolgozás alatt";
                    } else {
                      $allapot = "<span class='text-danger'>Utánvétes csomag postázva</span>";
                    }
                  } else {
                    $allapot = "<span class='text-success'><b>Teljesítve</b></span>";
                  }
                } else {
                  if ($kifizetve == "0000-00-00") {
                    $allapot = "<span class='text-danger'>Fizetésre várva</span>";
                  } else {
                    if ($teljesitve == "0000-00-00") {
                      $allapot = "Feldolgozás alatt";
                    } else {
                      $allapot = "<span class='text-success'><b>teljesítve</b></span>";
                    }
                  }
                }
                ?>
                <tr>
                  <td class="text-center">#<?= $id ?></td>
                  <td class="text-center"><?= $idopont ?></td>
                  <td class="text-end"><?= szampontos($fizetendo) ?> HUF</td>
                  <td class="text-center"><?= $allapot ?></td>
                  <td class="text-center"><a href="korabbi_rendelesek.php?id=<?= $id ?>&uid=<?= $webshop_id ?>">kattints ide...</a></td>
                </tr>
                <?php
              }
              ?>
            </tbody>
          </table>
        </div>

        <?php
        if ($volt == 0) {
          ?>
          <div class="alert alert-info text-center">
            <b>Még nem adtál le rendelést ebben a webshopban...</b>
          </div>
          <?php
        }  
        ?>
        
      </form>

    <?php include("alja_2.php"); ?>
  </body>
  </html>

  <?php
} elseif (isset($id) && isset($uid)) {
  ?>

  <html>

  <head>
    <title>Wimu Webshop</title>
    <meta name="cache-control" content="private, no-store, no-cache, must-revalidate">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>

  <body>
    <?php include("teteje_2.php"); ?>

    <div class="container mt-4">
      <h2 class="text-dark">A rendelés részletes adatai</h2>

      <form name="urlap" action="5_rendeles_elkuld.php" method="POST">
        <h4 class="mt-4">A megrendelt termékek:</h4>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="table-dark">
              <tr>
                <th>Termék</th>
                <th>Egységár</th>
                <th>Darabszám</th>
                <th>Összeg</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $parancs = "SELECT * from rendelesek WHERE id=$id AND ugyfel_id=$uid";
              $eredmeny = mysqli_query($kapcsolat, $parancs);
              $sor = mysqli_fetch_array($eredmeny);
              $fizetendo = $sor["fizetendo"];
              $fizetesi_mod = $sor["fizetesi_mod"];
              $nev = $sor["nev"];
              $email = $sor["email"];
              $telefon = $sor["telefon"];
              $orszag = $sor["orszag"];
              $irszam = $sor["irszam"];
              $varos = $sor["varos"];
              $utca = $sor["utca"];
              $sz_nev = $sor["sz_nev"];
              $sz_irszam = $sor["sz_irszam"];
              $sz_varos = $sor["sz_varos"];
              $sz_utca = $sor["sz_utca"];

              $kifizetve = $sor["kifizetve"];
              $teljesitve = $sor["teljesitve"];
              if ($fizetesi_mod == 2) {
                if ($kifizetve == "0000-00-00") {
                  if ($teljesitve == "0000-00-00") {
                    $allapot = "Hamarosan postázzuk a csomagot.";
                  } else {
                    $allapot = "Utánvétes csomag postázva: $teljesitve";
                  }
                } else {
                  $allapot = "A rendelés teljesítve (postázva:$teljesitve, kifizetve:$kifizetve).";
                }
              } else {
                if ($kifizetve == "0000-00-00") {
                  $allapot = "Fizetésre várva...";
                } else {
                  if ($teljesitve == "0000-00-00") {
                    $allapot = "Hamarosan postázzuk a csomagot (kifizetve:$kifizetve).";
                  } else {
                    $allapot = "A rendelés teljesítve (kifizetve:$kifizetve, postázva:$teljesitve).";
                  }
                }
              }

              $sql = "SELECT * FROM kosar WHERE ugyfel_id=$uid AND rendeles_id=$id";
              $eredmeny = mysqli_query($kapcsolat, $sql);

              while ($sor = mysqli_fetch_array($eredmeny)) {
                $arucikk_id = $sor["arucikk_id"];
                $db = $sor["db"];
                
                $parancs = "SELECT * FROM arucikk WHERE id=$arucikk_id";
                $rs = mysqli_query($kapcsolat, $parancs);

                if (mysqli_num_rows($rs) > 0) {
                  $egysor = mysqli_fetch_array($rs);
                  $nev = $egysor["nev"];
                  $nev2 = $egysor["nev2"];
                  $ar_huf = $egysor["ar_huf"];
                  ?>
                  <tr>
                    <td><?= $nev ?> <?= $nev2 ?></td>
                    <td class="text-end"><?= szampontos($ar_huf) ?> HUF</td>
                    <td class="text-center">x <?= $db ?></td>
                    <td class="text-end"><?= szampontos($db * $ar_huf) ?> HUF</td>
                  </tr>
                  <?php
                }
              }
              ?>
              <tr>
                <td colspan="3" class="text-end"><b>Összesen:</b></td>
                <td class="text-end"><b><?= szampontos($fizetendo) ?> HUF</b></td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <h4 class="mt-4">Vásárlói adatok:</h4>
        <div class="table-responsive">
          <table class="table table-bordered">
            <tbody>
              <tr>
                <td class="text-end"><b>Név/Cégnév:</b></td>
                <td><?= $nev ?></td>
              </tr>
              <tr>
                <td class="text-end"><b>E-mail cím:</b></td>
                <td><?= $email ?></td>
              </tr>
              <tr>
                <td class="text-end"><b>Telefonszám:</b></td>
                <td><?= $telefon ?></td>
              </tr>
              <tr>
                <td class="text-end"><b>Ország:</b></td>
                <td><?= $orszag ?></td>
              </tr>
              <tr>
                <td class="text-end"><b>Postázási cím:</b></td>
                <td><?= $irszam ?> <?= $varos ?>, <?= $utca ?></td>
              </tr>
              <tr>
                <td class="text-end"><b>Számlázási név:</b></td>
                <td><?= $sz_nev ?></td>
              </tr>
              <tr>
                <td class="text-end"><b>Számlázási cím:</b></td>
                <td><?= $sz_irszam ?> <?= $sz_varos ?>, <?= $sz_utca ?></td>
              </tr>
            </tbody>
          </table>
        </div>

        <h4 class="mt-4">Választott fizetési mód:</h4>
        <p class="text-dark">
          <?php
          if ($fizetesi_mod == 1) {
            print "Banki átutalás (vagy postai befizetés)";
          } else if ($fizetesi_mod == 2) {
            print "Postai utánvét";
          } else if ($fizetesi_mod == 3) {
            print "Bankkártyás fizetés (PayPal)";
          }
          ?>
        </p>

        <h4 class="mt-4">A rendelés állapota:</h4>
        <p class="text-dark"><?= $allapot ?></p>

        <div class="mt-4">
          <a href="korabbi_rendelesek.php" class="btn btn-secondary">&laquo; Vissza a rendelések listájához</a>
        </div>
      </form>
    </div>

    <?php include("alja_2.php"); ?>
  </body>
  </html>

  <?php
} else {
  header('Location:index.php');
}

mysqli_close($kapcsolat);
ob_end_flush();
?>
