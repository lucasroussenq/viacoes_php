<?php

declare(strict_types=1);

use App\Models\Viacao;

/** @var Viacao $viacao */
/** @var list<string> $errors */
/** @var array{nome: string, cidade: string, status: bool} $old */

?>

<h1>Editar viacao #<?= (int) $viacao->id ?></h1>

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

<form method="post" action="/viacoes/<?= (int) $viacao->id ?>">
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
        <label for="url">Site</label><br>
        <input
                id="url"
                type="url"
                name="url"
                value="<?= htmlspecialchars($old['url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                maxlength="500"
                placeholder="https://www.exemplo.com.br"
        >
    </div>

    <div>
        <label for="cidade">cidade</label><br>
        <input id="cidade" name="cidade" ><?= htmlspecialchars($old['cidade'] ?? '', ENT_QUOTES, 'UTF-8') ?></input>
    </div>

    <div>
        <label for="logo_file">Logo</label><br>

        <?php if (!empty($old['logo_path'])): ?>
            <div style="margin-bottom: 8px;">
                <img
                        src="<?= htmlspecialchars($old['logo_path'], ENT_QUOTES, 'UTF-8') ?>"
                        alt="Logo atual"
                        style="width: 160px; height: 60px; object-fit: contain; border: 1px solid #ddd;"
                >
            </div>
        <?php endif; ?>

        <input
                id="logo_file"
                type="file"
                name="logo_file"
                accept="image/png, image/jpeg, image/svg+xml"
        >
        <p style="font-size: 12px; color: #666;">
            PNG, JPG ou SVG. A imagem será exibida em 160×60 px.
        </p>
    </div>

    <div>
        <label>Status</label><br>

        <!-- Campo oculto garante que "inativo" seja enviado se nenhum botão for clicado -->
        <label>
            <input
                    type="radio"
                    name="status"
                    value="1"
                    <?= !empty($old['status']) ? 'checked' : '' ?>
            >
            Ativo
        </label>

        <label>
            <input
                    type="radio"
                    name="status"
                    value="0"
                    <?= empty($old['status']) ? 'checked' : '' ?>
            >
            Inativo
        </label>
    </div>

    <button type="submit">Salvar</button>
</form>