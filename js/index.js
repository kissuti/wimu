$(document).ready(function() {
    $("form.add-to-cart").submit(function(event) {
      event.preventDefault();
      var form = $(this);
      var button = form.find('button'); // Gomb elem kiválasztása
      
      $.ajax({
        type: form.attr('method'),
        url: form.attr('action'),
        data: form.serialize(),
        success: function(response) {
          alert("A termék sikeresen hozzáadva a kosárhoz!");
          // Kényszerített frissítés időbélyeggel
          $("#kosar").attr("src", "kosar.php?t=" + new Date().getTime());
        },
        error: function(xhr) {
          var errorMsg = xhr.status + ': ' + xhr.statusText;
          if(xhr.responseText) {
            errorMsg += ' - ' + xhr.responseText; // Szerver válasz szövegének hozzáadása
          }
          alert("Hiba történt: " + errorMsg);
        }
      });
    });

    // Mennyiség validálás kliens oldalon
    $('input[name="db"]').on('input', function() {
      var max = $(this).attr('max');
      var min = $(this).attr('min');
      var value = parseInt($(this).val());
      
      if (value < min) $(this).val(min);
      if (value > max) $(this).val(max);

          // Mennyiség módosítás gombok
    $('.minus-btn').click(function() {
        var input = $(this).siblings('input');
        var value = parseInt(input.val());
        if(value > input.attr('min')) {
          input.val(value - 1);
        }
      });
  
      $('.plus-btn').click(function() {
        var input = $(this).siblings('input');
        var value = parseInt(input.val());
        if(value < input.attr('max')) {
          input.val(value + 1);
        }
      });
  
      // Kosárba helyezés
      $("form.add-to-cart").submit(function(event) {
        event.preventDefault();
        var form = $(this);
        
        $.ajax({
          type: form.attr('method'),
          url: form.attr('action'),
          data: form.serialize(),
          success: function(response) {
            $("#kosar").attr("src", "kosar.php?t=" + new Date().getTime());
            alert("Termék hozzáadva a kosárhoz!");
          },
          error: function(xhr) {
            alert("Hiba: " + xhr.responseText);
          }
        });
      });
    });
  });