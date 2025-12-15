</main>

<script>
    function toggleSubmenu(id) {
        var submenu = document.getElementById(id);
        if (submenu.classList.contains('open')) {
            submenu.classList.remove('open');
        } else {
            submenu.classList.add('open');
        }
    }
</script>
</body>
</html>
