<!DOCTYPE html>
<html lang="hu">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wimu Webshop - ÁSZF & Szerzői Jogok</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/preloader.css">
  </head>
  <script src="js/preloader.js"></script>
  <body>
  <?php include("teteje.php") ?>
    <!-- Preloader -->
    <div id="preloader">
    <div class="spinner"></div>
  </div>
  <div class="container" id="main-content">
      
      <!-- Main Container -->
      <div class="container my-5">
        <!-- ÁSZF Section -->
        <section id="aszf">
          <h2 class="mb-4">Általános Szerződési Feltételek (ÁSZF)</h2>
          <p>A Wimu Webshop használatára vonatkozó általános szerződési feltételek (ÁSZF) alábbiak szerint kerülnek meghatározásra. Az oldalon történő böngészéssel és vásárlással a felhasználó elfogadja az alábbi feltételeket.</p>
          
          <h4>1. A szerződés tárgya</h4>
          <p>A Wimu Webshop által kínált termékek és szolgáltatások megrendelése a jelen ÁSZF elfogadásával jön létre. A szerződés tartalmazza a termékek specifikációját, árát, szállítási feltételeit, valamint a fizetés módját.</p>
          
          <h4>2. Megrendelés és fizetés</h4>
          <p>A megrendelés leadása kizárólag a webshopon keresztül történik. A fizetés a felhasználó által kiválasztott, biztonságos fizetési módon történik. A megrendelés részleteit, valamint a fizetési és szállítási költségeket a vásárlási folyamat során ismertetjük.</p>
          
          <h4>3. Szállítás és elállási jog</h4>
          <p>A termékek szállítása a megadott címre történik. A vásárló a termék átvételétől számított 14 napon belül jogosult a vásárlástól való elállásra, amennyiben a termék eredeti állapotban és csomagolásban kerül visszaküldésre.</p>
          
          <h4>4. Jogviták és irányadó jog</h4>
          <p>A jelen ÁSZF-re és a vásárlási szerződéses viszonyokra a magyar jog az irányadó. Az esetleges vitás kérdések rendezésére a Wimu Webshop székhelye szerinti illetékes bíróság rendelkezik joghatósággal.</p>
          
          <p class="mt-3">Wimu fenntartja a jogot a jelen ÁSZF egyoldalú módosítására, mely módosításról a weboldalon megfelelő tájékoztatást adunk.</p>
        </section>
        
        <hr class="my-5">
        
        <!-- Szerzői Jogok Section -->
        <section id="copyright">
          <h2 class="mb-4">Szerzői Jogok</h2>
          <p>Minden, a Wimu Webshopon található tartalom – beleértve a szövegeket, képeket, grafikákat, logókat és dizájn elemeket – szerzői jogvédelem alatt áll, és a Wimu tulajdonát képezi, vagy harmadik felektől származó felhasználási engedéllyel került felhasználásra.</p>
          
          <p>Bármilyen másolás, terjesztés, módosítás vagy egyéb felhasználás kizárólag a Wimu előzetes, írásbeli engedélyével lehetséges. A Wimu Webshop nevével vagy logójával való visszaélés esetén jogi lépések tehetők a jogsértők ellen.</p>
          
          <p class="mt-3">&copy; <?= date("Y") ?> Wimu Webshop. Minden jog fenntartva.</p>
        </section>
      </div>  
    </div>
      
      <!-- Bootstrap JS Bundle -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoYqne9zTnYAP4n0MQDa1RjFUGifYVg6VbLIY7Hf0QKZr9/" crossorigin="anonymous"></script>
    </body>
    </html>
    