<link rel="stylesheet" href="layout.css">

<div class="container">
    <form
            method="post"
            action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>"
            enctype="multipart/form-data"
    >
        <?php if ($method !== null): ?>
            <input type="hidden" name="_method" value="<?= htmlspecialchars($method, ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="viacao">Nome da marca</label>
            <input
                    id="viacao"
                    type="text"
                    name="nome"
                    value="<?= htmlspecialchars($old['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    required
                    maxlength="255"
            >
        </div>

        <div class="form-group">
            <label for="url">Site</label>
            <input
                    id="url"
                    type="url"
                    name="url"
                    value="<?= htmlspecialchars($old['url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    maxlength="500"
                    placeholder="https://www.exemplo.com.br"
            >
        </div>

        <div class="form-group">
            <label for="cidade">Cidade</label>
            <input
                    id="cidade"
                    type="text"
                    name="cidade"
                    value="<?= htmlspecialchars($old['cidade'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <div class="form-group">
            <label for="logo_file">Logo </label>

            <?php if (!empty($old['logo'])): ?>
                <div class="logo-preview">
                    <img
                            src="src/public/uploads//<?php $viacao->logo; ?>"
                            alt="Logo atual"
                    >
                </div>
            <?php endif; ?>

            <input type="hidden" name="logo_atual" value="<?= htmlspecialchars($old['logo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <input
                    id="logo_file"
                    type="file"
                    name="logo"
                    accept="image/png, image/jpeg, image/svg+xml"
            >
            <p class="form-help-text">
                PNG, JPG ou SVG. A imagem será exibida em 160×60 px.
            </p>
        </div>

        <div class="form-group">
            <label>Status</label>
            <div class="radio-group">
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
        </div>

        <button type="submit">Salvar</button>
    </form>
</div>

<style>
    /* Additional styles for form responsiveness */
    .form-group {
        margin-bottom: var(--spacing-md); /* Use responsive spacing */
    }

    .form-group label {
        margin-bottom: var(--spacing-xs);
        color: #000000/* Space between label and input */
    }

    .form-help-text {
        font-size: var(--font-size-p); /* Use responsive font size */
        color: var(--color-text-muted); /* Use muted color variable */
        margin-top: var(--spacing-xs);
        margin-bottom: 0;
    }

    .logo-preview {
        margin-bottom: var(--spacing-sm);
        border: 1px solid var(--border-color); /* Use border variable */
        display: inline-block; /* To contain the image properly */
        padding: 4px; /* Small padding around the image */
        border-radius: 4px;
    }

    .logo-preview img {
        max-width: 160px;
        height: 60px;
        object-fit: contain;
        display: block; /* Remove extra space below image */
    }

    .radio-group {
        display: flex;
        flex-wrap: wrap;
        gap: var(--spacing-md); /* Space between radio options */
        margin-top: var(--spacing-xs);
    }

    .radio-group label {
        display: flex;
        align-items: center;
        gap: var(--spacing-xs);
        margin-bottom: 0; /* Override default label margin */
    }

    /* Adjustments for smaller screens */
    @media (max-width: 480px) {
        .radio-group {
            flex-direction: column; /* Stack radio buttons vertically */
            gap: var(--spacing-sm);
        }
    }
</style>
