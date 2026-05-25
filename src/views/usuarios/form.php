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
            <label for="usuario">Nome do usuário</label>
            <input
                id="usuario"
                type="text"
                name="nome"
                value="<?= htmlspecialchars($old['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                required
                maxlength="255"
            >
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input
                id="email"
                type="text"
                name="email"
                value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <div class="form-group">
            <label for="senha">Senha</label>
            <input
                id="senha"
                type="password" name="senha"    value="<?= htmlspecialchars($old['senha'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                required
                maxlength="255"
            >
        </div>

        <div class="form-group">
            <label>Status</label>
            <div class="radio-group">
                <label>
                    <input
                        type="radio"
                        name="status"
                        value="1"
                        <?= (!isset($old['status']) || $old['status'] === '1' || $old['status'] === 'ativo') ? 'checked' : '' ?>
                    >
                    Ativo
                </label>

                <label>
                    <input
                        type="radio"
                        name="status"
                        value="0"
                        <?= (isset($old['status']) && ($old['status'] === '0' || $old['status'] === 'inativo')) ? 'checked' : '' ?>
                    >
                    Inativo
                </label>
            </div>
        </div>

        <button type="submit">Salvar</button>
    </form>
</div>