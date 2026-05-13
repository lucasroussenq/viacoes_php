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
    <title><?= htmlspecialchars($title ?? 'Task App', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/layout.css">
    <!-- Assuming home.css is also loaded on pages using this layout if needed -->

</head>
<body>
<header>
    <div class="container">
        <nav>
            <a href="/tasks">Tasks</a>
            | <a href="/tasks/create">Nova task</a>
            | <a href="/viacoes">Viacoes</a>
            | <a href="/viacoes/create">Nova viacao</a>
            | <a href="/upgrade-acesso">Login admim</a>
            | <a href="/downgrade-acesso">login comum</a>
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
