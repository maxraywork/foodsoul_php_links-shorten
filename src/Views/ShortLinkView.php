<?php
ob_start();
?>
    <section style="min-width: 500px;">
        <h1 class="mb-3">Your short link is:</h1>
        <i id="short-link" class="display-3" style="cursor:pointer"><?php echo $code ?? ''; ?></i>
        <div id="copy-status" style="color:green; display:none; margin-top:5px;">Copied!</div>
        <a class="btn btn-primary d-block mt-3" href="/">Go home</a>
    </section>

    <script>
        document.getElementById('short-link').addEventListener('click', function () {
            const text = this.textContent;
            navigator.clipboard.writeText(text).then(() => {
                const status = document.getElementById('copy-status');
                status.style.display = 'block';
                setTimeout(() => status.style.display = 'none', 2000);
            });
        })
    </script>
<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout.php';