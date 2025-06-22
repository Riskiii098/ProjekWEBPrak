  </div> <!-- end main -->
</div> <!-- end flex -->

<footer class="footer bg-light text-center py-3 mt-auto border-top">
  &copy; 2025 SmartLib • Sistem Perpustakaan Mini
</footer>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Skrip global untuk tombol konfirmasi -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-confirm').forEach(function (el) {
      el.addEventListener('click', function (e) {
        var msg = el.dataset.confirmMsg || 'Anda yakin ingin melakukan aksi ini?';
        if (!window.confirm(msg)) {
          e.preventDefault();
        }
      });
    });
  });
</script>
</body>
</html>
