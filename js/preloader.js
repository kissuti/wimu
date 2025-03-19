var preloaderStart = new Date().getTime();
window.addEventListener('load', function() {
  var elapsed = new Date().getTime() - preloaderStart;
  var remaining = 750 - elapsed; // Legalább 0.75 másodperc
  if (remaining < 0) {
    remaining = 0;
  }
  setTimeout(function() {
    var preloader = document.getElementById('preloader');
    var mainContent = document.getElementById('main-content');
    if (preloader) {
      preloader.classList.add('fade-out');
    }
    if (mainContent) {
      mainContent.classList.add('fade-in');
    }
    // A transition befejeződése után (kb. 500ms) a preloader véglegesen eltűnik
    setTimeout(function() {
      if (preloader) {
        preloader.style.display = 'none';
      }
    }, 500);
  }, remaining);
});
