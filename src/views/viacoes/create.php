<?php

declare(strict_types=1);

/** @var list<string> $errors */
/** @var array{nome: string, cidade: string, status: bool, url: string, logo: string} $old */

$action = "/viacoes";
$method = null;
$viacao = null;
?>


<h1>Criar marca</h1>
<?php require __DIR__ . '/form.php'; ?>

<?php if ($errors !== []): ?>
    <div class="alert alert--danger">
        <p><strong>Corrija os erros:</strong></p>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

