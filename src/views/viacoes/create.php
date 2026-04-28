<?php

declare(strict_types=1);

/** @var list<string> $errors */
/** @var array{viacao: string, cidade: string, Status: bool} $old */

?>

<h1>Criar marca</h1>

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

<form method="post" action="/viacoes">
    <div>
        <label for="viacao">Nome da marca</label><br>
        <input
            id="viacao"
            type="text"
            name="viacao"
            value="<?= htmlspecialchars($old['viacao'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            required
            maxlength="255"
        >
    </div>

    <div>
        <label for="cidade">Descrição</label><br>
        <input id="cidade" name="cidade" ><?= htmlspecialchars($old['cidade'] ?? '', ENT_QUOTES, 'UTF-8') ?></input>
    </div>

    <div>
        <label>
            <input type="checkbox" name="Status" value="1" <?= !empty($old['Status']) ? 'checked' : '' ?>>
            Ativo
        </label>
    </div>

    <button type="submit">Salvar</button>
</form>
