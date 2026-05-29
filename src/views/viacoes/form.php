<link rel="stylesheet" href="/css/layout.css">

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
            <label for="logo_file">Logo</label>

            <?php if (!empty($old['logo'])): ?>
                <div class="logo-preview">
                    <img
                            src="/uploads/<?= htmlspecialchars($old['logo'], ENT_QUOTES, 'UTF-8') ?>"
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
            <p class="form-help-text">PNG, JPG ou SVG. A imagem será exibida em 160×60 px.</p>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <?php
            $statusAtual = $old['status'] ?? ($viacao->status ?? 'ativo');

            $isAtivo    = ($statusAtual === 'ativo' || $statusAtual === true || $statusAtual === 1 || $statusAtual === '1');
            $isDeletado = ($statusAtual === 'deletado' || (!empty($viacao->data_exclusao) && !$isAtivo));
            $isInativo  = (!$isAtivo && !$isDeletado);
            ?>
            <select name="status" id="status" class="search-select" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ccc; background-color: #fff; font-size: 14px; margin-top: 5px;">
                <option value="ativo" <?= $isAtivo ? 'selected' : '' ?>>
                    Ativo
                </option>

                <option value="inativo" <?= $isInativo ? 'selected' : '' ?>>
                    Inativo
                </option>

                <option value="deletado" <?= $isDeletado ? 'selected' : '' ?>>
                    Excluído (Deletado)
                </option>
            </select>
        </div>

        <button type="submit">Salvar</button>
    </form>
</div>