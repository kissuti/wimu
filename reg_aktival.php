<?php
ob_start();

include("php/dbconn.php");

// Autoload a Composer által telepített csomagokhoz
require 'vendor/autoload.php';
use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;

// Cache beállítások
header("Pragma: no-cache"); 
header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// Például GET vagy POST paraméterként érkeznek ezek a változók
$id  = $_REQUEST['id'] ?? null;
$kod = $_REQUEST['kod'] ?? null;

if ($id && $kod) {

  $sql = "SELECT * FROM ugyfel WHERE id=$id AND kod='$kod'";
  $eredmeny = mysqli_query($kapcsolat, $sql);
  $rendben = 0;

  if (mysqli_num_rows($eredmeny) > 0) {
    $sor = mysqli_fetch_array($eredmeny);
    $email      = $sor["email"];
    $kod        = $sor["kod"];
    $megerositve = $sor["megerositve"];

    // Ha még nem volt aktiválva a regisztráció
    if ($megerositve == "0000-00-00 00:00:00") {
      $idopont = date("Y-m-d H:i:s");
      $sqlUpdate = "UPDATE ugyfel SET megerositve='$idopont' WHERE id=$id";
      mysqli_query($kapcsolat, $sqlUpdate);

      $ujsor = "\r\n";
      $uzenet = "Gratulálunk, regisztrációd sikeres volt!" . $ujsor . $ujsor;
      $uzenet .= "Jelszavad: " . $kod . $ujsor . $ujsor;
      $uzenet .= "Belépés: http://webshop.oktato2.info/belepes.php?email=" . $email . $ujsor . $ujsor;

      // MailerSend használata az email elküldéséhez
      $fromEmail = "webshop@oktato2.info";
      $fromName  = "Gyakorló WEBshop";
      $recipient = new Recipient($email);
      $emailParams = (new EmailParams())
        ->setFrom($fromEmail)
        ->setFromName($fromName)
        ->setRecipients([$recipient])
        ->setSubject("Sikeres regisztráció")
        ->setText($uzenet);

      // Inicializáljuk a MailerSend objektumot az API kulccsal
      $mailersend = new MailerSend(['api_key' => 'YOUR_API_KEY']);
      $mailersend->email->send($emailParams);
    }
    
    $rendben = 1;
  }
  ?>
  <html>
  <head>
    <title>Wimu Webshop</title>
    <meta name="cache-control" content="private, no-store, no-cache, must-revalidate" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2" />
  </head>
  <?php include("php/teteje.php"); ?>
  <div style="width:100%; text-align:right">
    <a href="index.php"><font color="<?= $hatter ?>">visszatérés a webshophoz</font></a>
  </div>
  <font style="font-size:14pt;color:<?= $sotet ?>"><b>Regisztráció aktiválása</b></font>
  <br><br><br>
  <?php if ($rendben == 1): ?>
    <b>A regisztrációd sikeresen aktiválva lett!</b>
  <?php else: ?>
    <font color="#800000"><b>Hibás kódot adtál meg!</b></font>
    <br><br>
    Az aktiválás sikertelen. Kérjük, kattints az emailben kapott aktiváló linkre.
    <br><br><br>
    Ha segítségre van szükséged, írj a <a href="mailto:webshop@oktato2.info">webshop@oktato2.info</a> címre!
  <?php endif; ?>
  <?php include("alja.php"); ?>
  <?php
  mysqli_close($kapcsolat);
} else {
  header("Location: index.php");
}
ob_end_flush();
?>
