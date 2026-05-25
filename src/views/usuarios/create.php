<?php

declare(strict_types=1);

/** @var list<string> $errors */
/** @var array{nome: string, email: string, status: string} $old */

$action  = "/usuarios";
$method  = null;
$usuario = null;
$old     = $old ?? [];
?>

    <h1>Criar usuário</h1>

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

<?php require __DIR__ . '/form.php'; ?>