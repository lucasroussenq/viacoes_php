<?php
declare(strict_types=1);
use App\Models\Usuario;

/** @var list<Usuario>  $usuarios */
/** @var string $busca */

$busca = $busca ?? '';

?>
<title>Usuários</title>

<div>
<link rel="stylesheet" href="/css/layout.css">

<main class="container">
    <header class="page-header">
        <div>
            <h1 style="color: white">Usuários</h1>
            <nav class="actions">
                <a class="btn btn-primary" href="/home">Home</a>
                <a class="btn btn-primary"  href="/historico/historico">Histórico</a>
                <a class="btn btn-primary" href="/viacoes/">Viações</a>
                <a class="btn btn-primary"  href="/logout">Logout</a>
            </nav>
        </div>
        <a href="/usuarios/create" class="btn btn-primary">
            + Criar usuário
        </a>
        <?php if (isset($_SESSION['user_nivel']) && $_SESSION['user_nivel'] === 1): ?>
            <div class="modoADM">
                <strong>Você é administrador</strong>
            </div>
        <?php endif; ?>
    </header>

    <!-- Barra de busca -->
    <?php
    $filtros   = $filtros   ?? [];
    $temFiltro = $temFiltro ?? false;
    ?>

    <!--filtros-->
    <form method="get" action="/usuarios" class="search-bar search-bar--multi">
        <div class="search-group search-group--multi">

            <input type="search" name="nome"
                   class="search-input"
                   placeholder="Nome do usuário…"
                   value="<?= htmlspecialchars($filtros['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <input type="search" name="e-mail"
                   class="search-input"
                   placeholder="E-mail…"
                   value="<?= htmlspecialchars($filtros['e-mail'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <select name="status" class="search-select">
                <option value="">Todos os status</option>
                <option value="1" <?= ($filtros['status'] ?? '') === '1' ? 'selected' : '' ?>>Ativo</option>
                <option value="0" <?= ($filtros['status'] ?? '') === '0' ? 'selected' : '' ?>>Inativo</option>
            </select>

            <button type="submit" class="btn btn-primary">Filtrar</button>

            <?php if ($temFiltro): ?>
                <a href="/usuarios" class="btn btn-ghost">Limpar</a>
            <?php endif; ?>
        </div>

        <?php if ($temFiltro): ?>
            <p class="search-info">
                <?= count($usuarios) ?> resultado(s) encontrado(s)
            </p>
        <?php endif; ?>
    </form>

    <?php if (count($usuarios) === 0): ?>
        <div class="card empty-state">
            <?php if ($busca !== ''): ?>
                <p>Nenhum usuário encontrado para a busca "<strong><?= htmlspecialchars($busca, ENT_QUOTES, 'UTF-8') ?></strong>".</p>
            <?php else: ?>
                <p>Nenhum usuário cadastrado no sistema.</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-card">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Senha</th>
                    <th>Status</th>
                    <th class="date-text">Criada em</th>
                    <th style="text-align: center;">Ações</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><strong><?= (int) $usuario['id'] ?></strong></td>
                        <td><?= htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(substr($usuario['senha'] ?? '', 0, 20), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= $usuario['status']?></td>

                        <td class="date-text">
                            <?= htmlspecialchars($usuario['data_criacao'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td>
                            <div class="actions" style="justify-content: flex-end;">
                                <a href="/usuarios/<?= (int) $usuario['id'] ?>/edit" class="btn btn-ghost">
                                    Editar
                                </a>

                                <form method="post" action="/usuarios/<?= (int) $usuario['id'] ?>/delete"
                                      onsubmit="return confirm('Remover este usuário?');">
                                    <button type="submit" class="btn btn-danger">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>