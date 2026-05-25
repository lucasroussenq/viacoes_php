<?php

declare(strict_types=1);

use App\Models\Usuario;

/** @var Usuario $usuario */
/** @var list<string> $errors */
/** @var array{nome: string, email: string, status: string} $old */

$action = "/usuarios/{$usuario->id}";
$method = "PUT";
$old    = $old ?? [];
?>
    <title>Editar Usuário</title>

    <h1>Editar usuário #<?= (int) $usuario->id ?></h1>

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