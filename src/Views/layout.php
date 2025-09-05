<?php

use App\Models\Auth;

?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>FoodSoul</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
<main class="container d-flex justify-content-center align-items-center relative" style="min-height: 100vh;">
    <?= $content ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger position-fixed" style="bottom:20px; left:50%; transform:translateX(-50%);" role="alert">
            <?php echo htmlspecialchars($error, ENT_QUOTES); ?>
        </div>
    <?php endif; ?>
    <?php if (Auth::check()): ?>
        <a type="button" href="/logout" class="btn btn-secondary position-fixed" style="right: 20px; bottom: 20px;" >Logout</a>
    <?php endif; ?>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>
</html>