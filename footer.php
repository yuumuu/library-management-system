    </div> <!-- End container -->
    
    <script>
        function toggleTheme() {
            const body = document.getElementById('body');
            if (body.hasAttribute('data-theme')) {
                body.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
            } else {
                body.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            }
        }

        // Load saved theme
        if (localStorage.getItem('theme') === 'dark') {
            document.getElementById('body').setAttribute('data-theme', 'dark');
        }
    </script>
</body>
</html>
