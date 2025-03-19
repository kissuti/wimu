<?php
ob_start();

// Adatbázis kapcsolat (dbconn.php-ban definiált $kapcsolat)
include("php/dbconn.php");

// MailerSend SDK betöltése
require 'vendor/autoload.php';
use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;

// Konfiguráció
$fromEmail = "webshop@oktato2.info";
$fromName  = "Gyakorló WEBshop";

// Inicializáljuk a MailerSend SDK-t az API kulccsal
$mailersend = new MailerSend(['api_key' => 'mlsn.7c7a7da4209379ee1b3acbd89fdc9fcea2767be971d6d43544c0f474551775ae']);

// Cache beállítások
header("Pragma: no-cache"); 
header("Cache-control: private, no-store, no-cache, must-revalidate");  
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// A kért változók (például POST vagy GET paraméterként)
$emailcim   = $_REQUEST['emailcim'] ?? null;
$id         = $_REQUEST['id'] ?? null;
$kod        = $_REQUEST['kod'] ?? null;
$uj_jelszo  = $_REQUEST['uj_jelszo'] ?? "";
$nev        = $_REQUEST['nev'] ?? "";

// Ha az új email cím, az ID és a régi aktivációs kód meg lett adva:
if ($emailcim && $id && $kod) {

    // Keresés az ugyfel táblában
    $sql = "SELECT * FROM ugyfel WHERE id=$id AND kod='$kod'";
    $eredmeny = mysqli_query($kapcsolat, $sql);

    if (mysqli_num_rows($eredmeny) > 0) {
        $sor = mysqli_fetch_array($eredmeny);
        $regiEmail  = $sor["email"];
        $regiJelszo = $sor["jelszo"];

        $ujcimFlag = false;
        $ujjelszoFlag = false;

        // Ha az új e-mail cím eltér a régitől, akkor az email módosítás folyamatát indítjuk el:
        if ($emailcim != $regiEmail) {
            // Aktivációs kód generálása (például egy 8 számjegyű kód)
            $ujkod = rand(10000000, 99999999);

            // Frissítjük az adatbázisban a felhasználó rekordját:
            $sqlUpdate = "UPDATE ugyfel SET uj_kod='$ujkod', uj_email='$emailcim' WHERE id=$id";
            mysqli_query($kapcsolat, $sqlUpdate);
            $ujcimFlag = true;

            // Összeállítjuk az aktivációs üzenetet
            $ujsor = "\r\n";
            $uzenet = "Üdvözlünk!" . $ujsor . $ujsor;
            $uzenet .= "Ezt az üzenetet azért kaptad, mert új e-mail címet adtál meg profilod beállításainál:" . $ujsor . $ujsor;
            $uzenet .= "$emailcim" . $ujsor . $ujsor;
            $uzenet .= "Az új e-mail címed aktiválásához add meg ezt a kódot: $ujkod" . $ujsor . $ujsor;
            $uzenet .= "Vagy kattints erre a linkre:" . $ujsor;
            $uzenet .= "https://yourdomain.com/profil_modosit_2.php?id=$id&ujkod=$ujkod" . $ujsor . $ujsor;
            $uzenet .= "Üdvözlettel," . $ujsor;
            $uzenet .= "Gyakorló WEBshop";

            // Küldjük el az aktivációs emailt a megadott új e-mail címre
            $recipient = new Recipient($emailcim);
            $emailParams = (new EmailParams())
                ->setFrom($fromEmail)
                ->setFromName($fromName)
                ->setRecipients([$recipient])
                ->setSubject("Új e-mail címed aktiválása (kód: $ujkod)")
                ->setText($uzenet);

            $mailersend->email->send($emailParams);
        }

        // Ha a jelszó is módosult (és nem üres), illetve az új jelszó (md5) eltér a régitől:
        if (!empty($uj_jelszo) && md5($uj_jelszo) != $regiJelszo) {
            $ujjelszoFlag = true;

            $ujsor = "\r\n";
            $uzenet = "Üdvözlünk!" . $ujsor . $ujsor;
            $uzenet .= "A jelszavad módosítása sikeres volt. Az új jelszavad:" . $ujsor . $ujsor;
            $uzenet .= "$uj_jelszo" . $ujsor . $ujsor;
            $uzenet .= "Bejelentkezéskor ezt az új jelszót használd!" . $ujsor . $ujsor;
            $uzenet .= "Üdvözlettel," . $ujsor;
            $uzenet .= "Gyakorló WEBshop";

            // Küldjük el a jelszó módosító emailt a régi, megerősített email címre
            $recipient = new Recipient($regiEmail);
            $emailParams = (new EmailParams())
                ->setFrom($fromEmail)
                ->setFromName($fromName)
                ->setRecipients([$recipient])
                ->setSubject("Új jelszavad")
                ->setText($uzenet);

            $mailersend->email->send($emailParams);

            // Frissítjük az adatbázisban a jelszót (itt md5-t használunk, de jobb megoldás: password_hash)
            $uj_jelszo_md5 = md5($uj_jelszo);
            $sqlUpdate = "UPDATE ugyfel SET jelszo='$uj_jelszo_md5' WHERE id=$id";
            mysqli_query($kapcsolat, $sqlUpdate);
        }

        // Ha megadtad a nevet, frissítjük azt is
        if (!empty($nev)) {
            $sqlUpdate = "UPDATE ugyfel SET nev='$nev' WHERE id=$id";
            mysqli_query($kapcsolat, $sqlUpdate);
        }
    } else {
        // Érvénytelen ID vagy kód esetén vissza a kezdőlapra
        header("Location: index.php");
        exit();
    }
    ?>

    <!-- HTML visszajelzés -->
    <html>
    <head>
      <title>Gyakorló WEBshop</title>
      <meta charset="utf-8" />
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    </head>
    <body>
      <div class="container mt-5">
        <?php if ($ujcimFlag): ?>
          <h2>Új e-mail cím aktiválása</h2>
          <p>Kérjük, ellenőrizd az új e-mail címedet (<strong><?= htmlspecialchars($emailcim) ?></strong>) és add meg az aktiváló kódot!</p>
          <!-- Itt egy űrlap is elhelyezhető a kód megadásához -->
        <?php elseif ($ujjelszoFlag): ?>
          <h2>Jelszó módosítás sikeres</h2>
          <p>Az új jelszavadat elküldtük az e-mail címedre (<strong><?= htmlspecialchars($regiEmail) ?></strong>).</p>
        <?php else: ?>
          <h2>Sikeres módosítás</h2>
          <p>A beállításaidat sikeresen módosítottad.</p>
        <?php endif; ?>
        <a href="index.php" class="btn btn-primary">Visszatérés a webshophoz</a>
      </div>
    </body>
    </html>
    <?php
}
// Ha csak az ID és az aktiváló kód (ujkod) érkezik (az aktiváló linkből)
elseif ($id && isset($_REQUEST['ujkod'])) {
    $ujkod = $_REQUEST['ujkod'];
    $sql = "SELECT * FROM ugyfel WHERE id=$id AND uj_kod='$ujkod'";
    $eredmeny = mysqli_query($kapcsolat, $sql);

    if (mysqli_num_rows($eredmeny) > 0) {
        $sor = mysqli_fetch_array($eredmeny);
        $uj_email = $sor["uj_email"];
        $jelszo   = $sor["jelszo"];

        // Frissítjük a felhasználó email címét és töröljük az ideiglenes mezőket
        $sqlUpdate = "UPDATE ugyfel SET email=uj_email, uj_email='', uj_kod='' WHERE id=$id";
        mysqli_query($kapcsolat, $sqlUpdate);

        // Küldünk visszaigazoló emailt az új email címre
        $ujsor = "\r\n";
        $uzenet = "Az e-mail cím módosítása sikeres volt!" . $ujsor . $ujsor;
        $uzenet .= "Bejelentkezéskor az alábbi adatokkal lépj be:" . $ujsor;
        $uzenet .= "E-mail: $uj_email" . $ujsor;
        $uzenet .= "Jelszó: $jelszo" . $ujsor . $ujsor;
        $uzenet .= "Üdvözlettel," . $ujsor;
        $uzenet .= "Gyakorló WEBshop";

        $recipient = new Recipient($uj_email);
        $emailParams = (new EmailParams())
            ->setFrom($fromEmail)
            ->setFromName($fromName)
            ->setRecipients([$recipient])
            ->setSubject("Sikeres módosítás")
            ->setText($uzenet);
        
        $mailersend->email->send($emailParams);
        $aktivacioSikeres = true;
    } else {
        $aktivacioSikeres = false;
    }
    ?>
    <html>
    <head>
      <title>Gyakorló WEBshop</title>
      <meta charset="utf-8" />
    </head>
    <body>
      <div class="container mt-5">
        <?php if ($aktivacioSikeres): ?>
          <h2>Az új e-mail címed sikeresen aktiválva!</h2>
        <?php else: ?>
          <h2><font color="#800000">Hiba!</font></h2>
          <p>Az aktiválás sikertelen. Lehet, hogy rossz kódot adtál meg vagy az új e-mail címedet már korábban aktiválták.</p>
          <p>Ha segítségre van szükséged, írj a <a href="mailto:webshop@oktato2.info">webshop@oktato2.info</a> címre!</p>
        <?php endif; ?>
        <a href="index.php" class="btn btn-primary">Visszatérés a webshophoz</a>
      </div>
    </body>
    </html>
    <?php
} else {
    // Hiányos paraméterek esetén visszairányítás a kezdőlapra
    header("Location: index.php");
    exit();
}

mysqli_close($kapcsolat);
ob_end_flush();
?>
