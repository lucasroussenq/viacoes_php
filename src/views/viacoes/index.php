<?php

declare(strict_types=1);

use App\Models\CoffeeBrands;

/** @var CoffeeBrands $marca */
/** @var list<string> $errors */
/** @var array{brand: string, description: string, is_imported: bool} $old */

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

<form method="post" action="/brands/<?= (int) $marca->id ?>" >
    <div>
        <label for="brand">Nome da marca</label><br>
        <input
            id="brand"
            type="text"
            name="brand"
            value="<?= htmlspecialchars($old['brand'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            required
            maxlength="255"
        >
    </div>

    <div>
        <label for="description">Descrição</label><br>
        <textarea id="description" name="description" rows="4" cols="60"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
    </div>

    <div>
        <label>
            <input type="checkbox" name="is_imported" value="1" <?= !empty($old['is_imported']) ? 'checked' : '' ?>>
            Importada (marca estrangeira)
        </label>
    </div>

    <button type="submit">Salvar alterações</button>
</form>
