<?php ob_start(); ?>
    <section style="min-width: 500px;">
        <h1 class="text-center mb-2">Verify</h1>
        <form action="/login/verify" method="POST">
            <div class="mb-3">
                <div class="form-text mb-2">We've sent one time password on your <strong><?php echo htmlspecialchars($email ?? "", ENT_QUOTES); ?></strong> email.</div>
                <label for="code" class="form-label">OTP from email letter:</label>
                <input type="number" maxlength="6" class="form-control" id="code" name="code">
                <input type="hidden" value="<?php echo htmlspecialchars($email ?? "", ENT_QUOTES); ?>" hidden name="email" >
            </div>
            <button type="submit" class="btn btn-primary">Verify</button>
        </form>
    </section>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/layout.php';