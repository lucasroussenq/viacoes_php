<?php

declare(strict_types=1);

use App\Core\View;

/** @var string $content */
/** @var string|null $title */

$flash = View::pullFlash();

?><!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="header.css">

</head>
<body>
<header>
    <div >
        <nav class="container-nav">
            <a class="btn-headerlay" href="/viacoes">Viacoes</a>
            <a class="btn-headerlay" href="/viacoes/create">Nova viacao</a>
        </nav>
    </div>
</header>

<?php if ($flash !== null): ?>
    <div>
        <div = htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>">
            <strong><?= htmlspecialchars(strtoupper($flash['type']), ENT_QUOTES, 'UTF-8') ?>:</strong>
            <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    </div>
<?php endif; ?>

<main>
    <div class="container">
        <?= $content ?>
    </div>
</main>
</body>
</html>
