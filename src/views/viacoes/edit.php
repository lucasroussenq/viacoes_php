<?php

declare(strict_types=1);

use App\Models\Viacao;

/** @var Viacao $marca */
/** @var list<string> $errors */
/** @var array{nome: string, cidade: string, Status: bool} $old */

?>

<h1>Editar marca #<?= (int) $marca->id ?></h1>

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

<form method="post" action="/viacoes/<?= (int) $marca->id ?>" >
    <div>
        <label for="nome">Nome da marca</label><br>
        <input
            id="nome"
            type="text"
            name="nome"
            value="<?= htmlspecialchars($old['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            required
            maxlength="255"
        >
    </div>

    <div>
        <label for="cidade">Descrição</label><br>
        <textarea id="cidade" name="cidade" rows="4" cols="60"><?= htmlspecialchars($old['cidade'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div>
        <label>
            <input type="checkbox" name="Status" value="1" <?= !empty($old['Status']) ? 'checked' : '' ?>>
            Importada (marca estrangeira)
        </label>
    </div>

    <button type="submit">Salvar alterações</button>
</form>
