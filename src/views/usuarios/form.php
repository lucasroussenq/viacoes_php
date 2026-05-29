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
            <label for="status">Status</label>
            <?php

            $statusAtual = $old['status'] ?? ($usuario->status ?? 'ativo');

            $isAtivo    = ($statusAtual === 'ativo' || $statusAtual === true || $statusAtual === 1 || $statusAtual === '1');
            $isDeletado = ($statusAtual === 'deletado' || (!empty($usuario->data_exclusao) && !$isAtivo));
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