<?php
declare(strict_types=1);
?>
<link rel="stylesheet" href="/css/layout.css">

<div class="container" style="max-width: 420px; margin: 60px auto;">

    <div class="page-header">
        <h1>Entrar</h1>
    </div>

    <?php
    // Mensagem de erro (email/senha inválidos ou campos vazios)
    if (!empty($_SESSION['erro'])): ?>
        <div class="alert alert--danger">
            <?= htmlspecialchars($_SESSION['erro'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <?php
    // Mensagem de sucesso vinda do /setup (primeiro usuário criado)
    if (!empty($_SESSION['setup_ok'])): ?>
        <div class="alert alert--success">
            <?= htmlspecialchars($_SESSION['setup_ok'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php unset($_SESSION['setup_ok']); ?>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST" action="/login">

            <div class="form-group">
                <label for="email">E-mail</label>
                <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="email"
                        required
                >
            </div>

            <div class="form-group">
                <label for="senha">Senha</label>
                <input
                        type="password"
                        id="senha"
                        name="senha"
                        autocomplete="current-password"
                        required
                >
            </div>

            <div class="form-footer">
                <button type="submit">Entrar</button>
            </div>

        </form>
    </div>

    <?php
    // Dica de setup visível apenas quando o banco está vazio.
    // Verificamos direto aqui para não adicionar complexidade ao controller.
    // Em produção, se quiser remover completamente, apague este bloco.
    try {
        $pdo   = \getPdo();
        $total = (int) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn();
        if ($total === 0): ?>
            <p style="text-align:center; margin-top:20px; font-size:.85rem; color:#9ca3af;">
                Nenhum usuário cadastrado ainda.
                <a href="/setup">Criar primeiro usuário →</a>
            </p>
        <?php endif;
    } catch (\Throwable) {
        // Banco indisponível — silencia para não quebrar a tela de login
    }
    ?>

</div>