<?php
ob_start();

include("../dbconn.php");
include("../fuggvenyek.php");

header("Pragma: no-cache"); 
Header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

$vilagos = "#E9D1D1"; // Define the $vilagos variable

if (!isset($lista)) {
  $lista = "aktualis";
}

if (!isset($mit)) {
  $mit="";
}

if (!isset($rend_id)) {
  $rend_id="";
}

$mavan = date("Y-m-d",time());
$mostvan = date("Y-m-d H:i:s",time());

if ($mit=="" || $mit=="keres") {

  if ($mit=="keres") {
    if ($rend_id!="") {
      $szuro = "WHERE id=$rend_id";
    }
    else {
      $szuro = "WHERE id=0";
    }
  }
  else {
    $szuro = " order by id desc";
    if ($lista=="aktualis") {
      $szuro = "WHERE torolve=0 AND (kifizetve='0000-00-00' OR teljesitve='0000-00-00') ORDER BY id DESC";
      $cimke = "AKTUÁLIS RENDELÉSEK";
    }

    elseif ($lista=="osszes") {
      $szuro = "WHERE torolve=0 ORDER BY id DESC";
      $cimke = "AZ ÖSSZES RENDELÉS";
    }
    
    elseif ($lista=="torolve") {
      $szuro = "WHERE torolve=1 ORDER BY id DESC";
      $cimke = "TÖRÖLT RENDELÉSEK";
    }
    
    elseif ($lista=="kifizetve") {
      $szuro = "WHERE torolve=0 AND kifizetve>'0000-00-00' ORDER BY kifizetve DESC";
      $cimke = "KIFIZETVE";
    }
    
    elseif ($lista=="teljesiteni") {
      $szuro = "WHERE torolve=0 AND teljesitve='0000-00-00' AND (((fizetesi_mod=1 OR fizetesi_mod=3) AND kifizetve>'0000-00-00') OR fizetesi_mod=2) ORDER BY kifizetve DESC, id DESC";
      $cimke = "TELJESÍTENI";
    }

    elseif ($lista=="teljesitve") {
      $szuro = "WHERE torolve=0 AND teljesitve>'0000-00-00' ORDER BY teljesitve DESC";
      $cimke = "TELJESÍTVE";
    }

    elseif ($lista=="atutal_nemfiz") {
      $szuro = "WHERE torolve=0 AND fizetesi_mod=1 AND kifizetve='0000-00-00' ORDER BY teljesitve DESC";
      $cimke = "ÁTUTALÁS - NINCS KIFIZETVE";
    }

    elseif ($lista=="utanvet_nemfiz") {
      $szuro = "WHERE torolve=0 AND fizetesi_mod=2 AND teljesitve>'0000-00-00' AND kifizetve='0000-00-00' ORDER BY teljesitve DESC";
      $cimke = "UTÁNVÉTES - NINCS KIFIZETVE";
    }

    elseif ($lista=="bankkartya_nemfiz") {
      $szuro = "WHERE torolve=0 AND fizetesi_mod=3 AND kifizetve='0000-00-00' ORDER BY teljesitve DESC";
      $cimke = "BANKKÁRTYÁS - NINCS KIFIZETVE";
    }

  }

  ?>
  <html>

  <head>
    <META NAME="cache-control" CONTENT="private, no-store, no-cache, must-revalidate">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Rendelések</title>

    <style type="text/css">
      td.sor {
        border-top:1px #868686 solid;
      }
    </style>
  </head>

  <body style="font-family:tahoma; font-size:8pt;">
  

    <div class="container mt-4">
      <h1 class="mb-4"><?= $cimke?></h1>
      <a href="../admin-index.php" class="btn btn-primary btn-danger">Vissza az admin főoldalra</a>

      <form name="urlap" action="rendeles_lista.php" method="POST" class="bg-light p-4 rounded">
        <input type="hidden" name="mit" value="vegrehajt">
        <input type="hidden" name="lista" value="<?= $lista?>">

        <div class="mb-3">
          <label for="rend_id" class="form-label fs-4">Azonosító:</label>
          <input name="rend_id" class="form-control" style="width:100px; display:inline-block;">
          <button type="button" class="btn btn-primary" onClick="document.urlap.mit.value='keres';document.urlap.submit()">Keresés</button>
        </div>

        <div class="mb-3">
          <button type="button" class="btn btn-success" onClick="document.urlap.submit()">Módosítások végrehajtása</button>
        </div>

        <div class="mb-3">
          <div class="row">
            <div class="col-md-4"><a href="rendeles_lista.php?lista=aktualis" class="btn btn-link">aktuális rendelések</a></div>
            <div class="col-md-4"><a href="rendeles_lista.php?lista=osszes" class="btn btn-link">összes rendelés</a></div>
            <div class="col-md-4"><a href="rendeles_lista.php?lista=torolve" class="btn btn-link">törölt rendelések</a></div>
          </div>
          <div class="row">
            <div class="col-md-4"><a href="rendeles_lista.php?lista=kifizetve" class="btn btn-link">kifizetve</a></div>
            <div class="col-md-4"><a href="rendeles_lista.php?lista=teljesiteni" class="btn btn-link">teljesíteni</a></div>
            <div class="col-md-4"><a href="rendeles_lista.php?lista=teljesitve" class="btn btn-link">teljesítve</a></div>
          </div>
          <div class="row">
            <div class="col-md-4"><a href="rendeles_lista.php?lista=atutal_nemfiz" class="btn btn-link">átutalás nincs fizetve</a></div>
            <div class="col-md-4"><a href="rendeles_lista.php?lista=utanvet_nemfiz" class="btn btn-link">utánvétes nincs fizetve</a></div>
            <div class="col-md-4"><a href="rendeles_lista.php?lista=bankkartya_nemfiz" class="btn btn-link">bankkártyás nincs fizetve</a></div>
          </div>
        </div>

        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Rendelés</th>
              <th>Megrendelő adatai</th>
              <th>Megrendelt termékek</th>
              <th>Fizetendő</th>
              <th>Számla</th>
              <th>Teljesítés</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sql = "SELECT * FROM rendelesek $szuro";
            $eredmeny = mysqli_query($kapcsolat, $sql);

            $szin = $vilagos;
            $hanyadik = 0;
            
            while ($sor = mysqli_fetch_array($eredmeny)) {

              $hanyadik = $hanyadik+1;

              if ($szin==$vilagos) {
                $szin = "white";
              }
              else {
                $szin = $vilagos;
              }

              $id = $sor["id"];
              $email = $sor["email"];
              $nev = $sor["nev"];
              $telefon = $sor["telefon"];
              $postacim = $sor["irszam"] . " " . $sor["varos"] . ", " . $sor["utca"] . "<br>(" . $sor["orszag"] . ")";
              $sz_nev = $sor["sz_nev"];
              $szamlacim = $sor["sz_irszam"] . " " . $sor["sz_varos"] . ", " . $sor["sz_utca"];
              $idopont = $sor["idopont"];
              $fizetendo = $sor["fizetendo"];
              $fizetesi_mod = $sor["fizetesi_mod"];
              $szamla_id = $sor["szamla_id"];
              $szamla_kod = $sor["szamla_kod"];
              $szamla_sorszam = $sor["szamla_sorszam"];
              $szamla_tomb = $sor["szamla_tomb"];
              $kifizetve = $sor["kifizetve"];
              $torolve = $sor["torolve"];
              $teljesitve = $sor["teljesitve"];
              $megjegyzes = $sor["megjegyzes"];

              if ($kifizetve=="0000-00-00") {
                $kifizet = "nincs kifizetve";
                $fizetendo = "<b>" . $fizetendo . " Ft</b>";
                $kifiz = 0;
              }
              else {
                $kifizet = $kifizetve;
                $fizetendo = "<font color='mediumseagreen'><b>" . $fizetendo . " Ft</b></font>";
                $kifiz = 1;
              }

              if ($fizetesi_mod==1) {
                $fizmod = "banki átutalás";
              }
              if ($fizetesi_mod==2) {
                $fizmod = "postai utánvét";
              }
              if ($fizetesi_mod==3) {
                $fizmod = "bankkártyás fizetés<br><img src='../img/logo_ccVisa.gif'> <img src='../img/logo_ccMC.gif'>";
              }
              
              $mitrendelt = "";

              $sql = "SELECT * FROM kosar WHERE rendeles_id=$id";
              $rskosar = mysqli_query($kapcsolat, $sql);

              while ($egysor=mysqli_fetch_array($rskosar)) {
                $arucikk_id = $egysor["arucikk_id"];
                $db = $egysor["db"];
                
                $parancs = "SELECT * FROM arucikk WHERE id=$arucikk_id";
                $rs = mysqli_query($kapcsolat, $parancs);

                if (mysqli_num_rows($rs)>0) {
                  $sora = mysqli_fetch_array($rs);
                  $rovidnev = $sora["rovidnev"];

                  $mitrendelt .= "$rovidnev ($db db)<br>";
                }
              }

              ?>
              <tr valign="top" bgcolor="<?= $szin?>">
                <td class="sor">
                  <font size="3">#<?= $hanyadik?></font>
                </td>
                <td class="sor" width="120">
                  <b>ID: <?= $id?></b>  
                  <br><font style="font-size:8pt;color:#444444"><?= $idopont?></font>
                  <?php
                  if ($torolve==0 && $kifizetve=="0000-00-00" && $teljesitve=="0000-00-00") {
                    ?>
                    <font style="font-size:4pt"><br><br></font>
                    <font style="font-size:8pt;color:#D60270"><b>törlés</b></font>
                    <input type="checkbox" name="torles[]" style="font-size:8pt" value="update rendelesek set torolve=1 where id=<?= $id?>">
                    <?php
                  }
                  ?>
                </td>
                <td class="sor">
                  <b><?= $nev?></b><br><?= $postacim?>
                  <font style="font-size:4pt"><br><br></font>e-mail: <a href="mailto:<?= $email?>"><?= $email?></a>
                  </b><br>Tel.: <?= $telefon?></font>
                </td>
                <td class="sor">
                  <?= $mitrendelt?>
                </td>
                <td class="sor">
                  <?= $fizetendo?><br><font style="font-size:8pt;color:#444444"><?= $kifizet?><br><?= $fizmod?></font>
                  <?php
                  if ($kifizetve=="0000-00-00") {
                    ?>
                    <font style="font-size:4pt"><br><br></font>
                    <select name="kifizetve[]" class="form-select form-select-sm" style="font-size:8pt">
                      <option value="">&nbsp;</option>
                      <?php
                      for ($i=0;$i<20;$i++) {
                        $minusznap = $i * 86400;
                        ?>
                        <option value="update rendelesek set kifizetve='<?= date("Y-m-d",time()-$minusznap)?>' where id=<?= $id?>"> <?= date("Y-m-d",time()-$minusznap)?> </option>
                        <?php
                      }  
                      ?>
                    </select>
                    <?php
                  }
                  ?>
                </td>
                <td class="sor">
                  <font style="font-size:8pt;color:#444444">
                    <b><?= $sz_nev?></b>
                    <br>
                    <?= $szamlacim?>
                  </font>
                </td>
                <td class="sor" style="font-size:8pt">
                  teljesítve:
                  <?php
                  if ($teljesitve != "0000-00-00") {
                    print $teljesitve;
                  }
                  else {
                    ?>
                    <select name="teljesitve[]" class="form-select form-select-sm" style="font-size:8pt">
                      <option value="">&nbsp;</option>
                      <?php
                      for ($i=0;$i<10;$i++) {
                        $minusznap = $i * 86400;
                        ?>
                        <option value="update rendelesek set teljesitve='<?= date("Y-m-d",time()-$minusznap)?>' where id=<?= $id?>"> <?= date("Y-m-d",time()-$minusznap)?> </option>
                        <?php
                      }  
                      ?>
                    </select>
                    <?php
                  }
                  ?>
                </td>
              </tr>
              <?php
            }
            ?>
          </tbody>
        </table>

        <div class="mb-3">
          <button type="button" class="btn btn-success" onClick="document.urlap.submit()">Módosítások végrehajtása</button>
        </div>
      </form>
    </div>
  </body>
  </html>

  <?php
}

// a változtatások (törlés, kifizetés, teljesítés) végrehajtása
//===================================================================================
elseif ($mit=="vegrehajt") {

  ?>
  <a href="rendeles_lista.php?lista=<?= $lista?>"><b>VISSZATÉRÉS A LISTÁHOZ</b></a>
  <?php
  
  if (isset($torles)) {
    foreach ($torles as $elem) {
      mysqli_query($kapcsolat, stripslashes($elem));
    }
  }
  
  if (isset($kifizetve)) {
    foreach ($kifizetve as $elem) {
      mysqli_query($kapcsolat, stripslashes($elem));
    }
  }
  
  if (isset($teljesitve)) {
    foreach ($teljesitve as $elem) {
      mysqli_query($kapcsolat, stripslashes($elem));
    }
  }
  
}

mysqli_close($kapcsolat);
ob_end_flush();
?>
