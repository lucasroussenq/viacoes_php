<?php

declare(strict_types=1);

use App\Models\Viacao;

/** @var Viacao $viacao */
/** @var list<string> $errors */
/** @var array{nome: string, cidade: string, status: bool} $old */

$action = "/viacoes/store";
$method = null;
$viacao = null;
?>

<h1>Editar viacao #<?= (int) $viacao->id ?></h1>

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
