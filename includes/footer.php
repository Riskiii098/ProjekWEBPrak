  </div> <!-- end main -->
</div> <!-- end flex -->
<footer class="footer">&copy; 2025 SmartLib</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
 <!-- Skrip global untuk konfirmasi pop-up -->
  <script>
  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.js-confirm').forEach(function(el){
      el.addEventListener('click', function(e){
        // Jika atribut data-confirm-msg tidak ada, pakai default
        var msg = el.dataset.confirmMsg 
                || 'Anda yakin ingin melakukan aksi ini?';
        if(!window.confirm(msg)){
          e.preventDefault();
        }
      });
    });
  });
  </script>
</html>
