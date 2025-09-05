<?php ob_start();
?>
    <section style="min-width: 500px;">
        <h1 class="display-5 text-center mb-3">Shorten URL</h1>
        <form style="min-width: 500px;" method="POST" action="/url/create">
            <div class="mb-3">
                <label for="longUrl" class="form-label">Paste the URL to be shortened</label>
                <input type="url" class="form-control" id="longUrl" name="url" required
                       placeholder="Enter the link here">
            </div>
            <button type="submit" class="btn btn-primary">Shorten URL</button>
        </form>

        <div class="mt-5">
            Your api token:<br>
            <i><?= $token ?? "" ?></i>
        </div>
        <?php if ($urls): ?>
            <div class="mt-5">
                <div class="list-group">
                    <?php foreach ($urls as $url): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <a class="list-group-item-heading d-block"
                                   href="<?= htmlspecialchars($url['short_url']) ?>" target="_blank">
                                    <?= htmlspecialchars($url['short_url']) ?>
                                </a>
                                <a class="list-group-item-text d-block text-secondary"
                                   href="<?= htmlspecialchars($url['long_url']) ?>" target="_blank">
                                    <?= htmlspecialchars($url['long_url']) ?>
                                </a>
                            </div>
                            <form method="POST" action="/url/delete" style="margin:0;">
                                <input type="hidden" name="id" value="<?= $url['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>
<?php $content = ob_get_clean();
require_once __DIR__ . '/layout.php';