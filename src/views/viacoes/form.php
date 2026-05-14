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
                    <!-- Correção: 'echo' adicionado e barra extra removida -->
                    <img src="src/public/uploads/<?= $viacao->logo; ?>" alt="Logo atual">
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

