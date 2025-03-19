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
    $belepve = 1;
  }
}

if ($belepve == 1) {
  ?>

  <html>

  <head>
    <title>Wimu Webshop - Rendelés összegzése</title>
    <meta name="cache-control" content="private, no-store, no-cache, must-revalidate">
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/order_summary.css">
    <meta http-equiv="Content-Type" content="text/html">
    <script src="https://www.paypal.com/sdk/js?client-id=YOUR_PAYPAL_CLIENT_ID"></script>
  </head>

  <body>
    <?php include("teteje_2.php"); ?>

    <div class="container mt-4">
      <h2 class="text-dark">Rendelés összegzése</h2>
      
      <div class="bg-light p-4 rounded border">
        <h4>Rendelés részletei:</h4>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="thead-dark">
              <tr>
                <th>Termék</th>
                <th class="text-center">Darabszám</th>
                <th class="text-end">Egységár</th>
                <th class="text-end">Összeg</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $osszeg = 0;
              $sql = "SELECT * FROM kosar WHERE ugyfel_id=$webshop_id AND rendeles_id=0";
              $eredmeny = mysqli_query($kapcsolat, $sql);
              while ($sor = mysqli_fetch_array($eredmeny)) {
                $arucikk_id = $sor["arucikk_id"];
                $db = $sor["db"];
                
                $parancs = "SELECT * FROM arucikk WHERE id=$arucikk_id";
                $rs = mysqli_query($kapcsolat, $parancs);

                if (mysqli_num_rows($rs) > 0) {
                  $egysor = mysqli_fetch_array($rs);
                  $nev = $egysor["nev"];
                  $ar_huf = $egysor["ar_huf"];
                  $osszeg += $db * $ar_huf;
                  ?>
                  <tr>
                    <td><b><?= $nev ?></b></td>
                    <td class="text-center"><?= $db ?></td>
                    <td class="text-end"><?= szampontos($ar_huf) ?> HUF</td>
                    <td class="text-end"><?= szampontos($db * $ar_huf) ?> HUF</td>
                  </tr>
                  <?php
                }
              }
              ?>
              <tr>
                <td colspan="3" class="text-end"><b>Összesen:</b></td>
                <td class="text-end"><b><?= szampontos($osszeg) ?> HUF</b></td>
              </tr>
            </tbody>
          </table>
        </div>

        <form name="urlap" action="5_rendeles_elkuld.php" method="POST">
          <input type="hidden" name="osszeg" value="<?= $_POST['osszeg'] ?>">
          <div class="mt-4">
            <div id="paypal-button-container"></div>
          </div>
        </form>
      </div>
    </div>

    <?php include("alja_2.php"); ?>

    <script>
      paypal.Buttons({
        createOrder: function(data, actions) {
          return actions.order.create({
            purchase_units: [{
              amount: {
                value: '<?= $_POST['osszeg'] / 350 ?>' // Convert HUF to USD (example conversion rate)
              }
            }]
          });
        },
        onApprove: function(data, actions) {
          return actions.order.capture().then(function(details) {
            alert('Transaction completed by ' + details.payer.name.given_name);
            // Redirect to the order confirmation page
            window.location.href = '5_rendeles_elkuld.php';
          });
        }
      }).render('#paypal-button-container');
    </script>
  </body>
  </html>

  <?php
} else {
  header("Location: index.php");
}

mysqli_close($kapcsolat);
ob_end_flush();
?>
