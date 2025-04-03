<?php
ob_start();
session_start();

include("php/dbconn.php");
include("php/fuggvenyek.php");

header("Pragma: no-cache"); 
Header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// Initialize variables
$nev = $_POST['nev'] ?? '';
$telefon = $_POST['telefon'] ?? '';
$kulfoldi = $_POST['kulfoldi'] ?? 0;
$orszag = $_POST['orszag'] ?? 'Magyarország';
$irszam = $_POST['irszam'] ?? '';
$varos = $_POST['varos'] ?? '';
$utca = $_POST['utca'] ?? '';
$sz_nev = $_POST['sz_nev'] ?? '';
$sz_irszam = $_POST['sz_irszam'] ?? '';
$sz_varos = $_POST['sz_varos'] ?? '';
$sz_utca = $_POST['sz_utca'] ?? '';

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

    $sql = "UPDATE ugyfel SET nev='$nev', telefon='$telefon', kulfoldi=$kulfoldi, orszag='$orszag', irszam='$irszam', varos='$varos', utca='$utca', sz_nev='$sz_nev', sz_irszam='$sz_irszam', sz_varos='$sz_varos', sz_utca='$sz_utca' WHERE id=$webshop_id";
    mysqli_query($kapcsolat, $sql);

    $belepve = 1;
  }
}

if ($belepve == 1) {
  ?>

  <html>

  <head>
    <title>Wimu Webshop</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="cache-control" content="private, no-store, no-cache, must-revalidate">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/payment.css">
    <link rel="stylesheet" href="styles/btn_gombok.css">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">
    <script src="https://www.paypal.com/sdk/js?client-id=YOUR_PAYPAL_CLIENT_ID"></script>
    <script type="text/javascript">
      function ellenoriz(mit) {
        if (document.urlap.fizet[0].checked || document.urlap.fizet[1].checked || document.urlap.fizet[2].checked) {
          document.urlap.submit();
        } else {
          alert('Kérlek válassz ki egy fizetési módot!');
          mit.value = 'A rendelés összegzése >>';
          mit.disabled = false;
        }
      }
    </script>
  </head>

  <body>
    <?php include("teteje.php"); ?>

    <div class="container mt-4">
      <h2 class="text-dark">Fizetési mód kiválasztása</h2>

      <form name="urlap" action="4_rendeles_osszegzes.php" method="POST" class="bg-light p-4 rounded border">
        <input type="hidden" name="osszeg" value="<?= $_POST['osszeg'] ?>">
        <h4>Kérlek jelöld be, hogy milyen módon szeretnél fizetni:</h4>

        <div class="form-check mb-3">
          <input class="form-check-input" type="radio" name="fizet" value="1" id="bankiAtutalas">
          <label class="form-check-label" for="bankiAtutalas">
            <b>Banki átutalás</b> (vagy postai befizetés)
            <p class="small">Banki átutalás esetén a fizetendő összeget a bankszámlánkra <b>előre kérjük átutalni</b> vagy a bankban befizetni. Ha a banki átutalást nem tudod elintézni, a pénzt <b>feladhatod a címünkre a postán is</b>, rózsaszínű csekken (belföldi utalvány)! A megrendelt termékeket a pénz beérkezése után postázzuk.</p>
          </label>
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="radio" name="fizet" value="2" id="postaiUtavnet">
          <label class="form-check-label" for="postaiUtavnet">
            <b>Postai utánvét</b>
            <p class="small">A megrendelt termékeket a <b>Magyar Posta</b> kézbesíti, a fizetendő összeget a csomag átvételekor kell kifizetned a postásnak.</p>
          </label>
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="radio" name="fizet" value="3" id="bankkartyasFizetes">
          <label class="form-check-label" for="bankkartyasFizetes">
            <b>Bankkártyás fizetés</b> (PayPal)
            <p class="small">Bankkártyás fizetés esetén a fizetést a <b>PayPal angol nyelvű</b> webes felületén tudod elintézni. A bankkártyád adatait csak a PayPal látja, így adataid biztonságban lesznek! A megrendelt termékeket a sikeres fizetés után azonnal postázzuk.</p>
            <div id="paypal-button-container"></div>
          </label>
        </div>

        <div class="mt-4">
          <button type="button" name="tovabb" id="tovabb" class="btn btngombok w-100" onclick="this.value='Ellenőrzés folyamatban...'; this.disabled=true; ellenoriz(this)">A rendelés összegzése >></button>
        </div>
      </form>
    </div>

    <?php include("alja.php"); ?>
            <!-- Bootstrap JS bundle (Popper included) -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
          integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
          crossorigin="anonymous"></script>

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
            // Redirect to the order summary page
            window.location.href = '4_rendeles_osszegzes.php';
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