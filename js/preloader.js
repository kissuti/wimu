document.addEventListener('DOMContentLoaded', function() {
  var preloader = document.getElementById('preloader');
  var mainContent = document.getElementById('main-content');

  // Ha már korábban megjelent a preloader a munkamenet során...
  if (sessionStorage.getItem("preloaderShown") === "true") {
    if (preloader) {
      preloader.style.display = "none";
    }
    // Azonnal láthatóvá tesszük a main-content-et
    if (mainContent) {
      mainContent.style.opacity = "1";
    }
  } else {
    // Első betöltés: várunk legalább 750ms-t, majd végrehajtjuk az animációt
    var preloaderStart = new Date().getTime();

    window.addEventListener('load', function() {
      var elapsed = new Date().getTime() - preloaderStart;
      var remaining = Math.max(750 - elapsed, 0); // minimum 750ms várakozás
      setTimeout(function() {
        if (preloader) {
          preloader.classList.add("fade-out");
          // Az animációs idő után (500ms) végleg elrejti a preloader-t
          setTimeout(function() {
            preloader.style.display = "none";
          }, 500);
        }
        // Beállítjuk, hogy a preloader már megjelent a sessionStorage-ban
        sessionStorage.setItem("preloaderShown", "true");
        // A main-content lassan átlátszóvá válik (CSS: fade-in)
        if (mainContent) {
          mainContent.classList.add("fade-in");
        }
      }, remaining);
    });
  }
});
