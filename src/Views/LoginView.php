<?php ob_start(); ?>
    <section style="min-width: 500px;">
        <h1 class="text-center mb-5">Login</h1>
        <form action="/login/verify" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </section>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout.php';