<?php
declare(strict_types=1);
use App\Models\Viacao;
/** @var list<Viacao> $viacoes */
?>

<main class="container">
    <header class="page-header">
        <div>
            <h1>Viação / Marcas</h1>
            <nav class="actions">
                <a href="/home" class="url-link">Home</a>
                <span class="muted">•</span>
                <a href="/viacoes/historico" class="url-link">Histórico</a>
            </nav>
        </div>
        <a href="/viacoes/create" class="btn btn-primary">
            + Criar viação
        </a>
        <?php if (isset($_SESSION['user_nivel']) && $_SESSION['user_nivel'] === 1): ?>
            <div class="modoADM">
                <strong>Você é administrador</strong>
            </div>
        <?php endif; ?>
    </header>

    <?php if (count($viacoes) === 0): ?>
        <div class="card empty-state">
            <p>Nenhuma viação cadastrada no sistema.</p>
        </div>
    <?php else: ?>
        <div class="table-card">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Cidade</th>
                    <th>Logo</th>
                    <th>Link</th>
                    <th>Status</th>
                    <th class="date-text">Criada em</th>
                    <th style="text-align: right;">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($viacoes as $viacao): ?>
                    <tr>
                        <td><strong><?= (int) $viacao->id ?></strong></td>
                        <td><?= htmlspecialchars($viacao->nome, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($viacao->cidade, ENT_QUOTES, 'UTF-8') ?></td>
                        <td> <?= htmlspecialchars(substr($viacao->logo, 0, 20), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a href="<?= htmlspecialchars($viacao->url, ENT_QUOTES, 'UTF-8') ?>"
                               target="_blank" class="url-link">
                                Ver site
                            </a>
                        </td>
                        <td>
                            <?php if ($viacao->status): ?>
                                <span class="badge badge-active">Ativo</span>
                            <?php else: ?>
                                <span class="badge badge-inactive">Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td class="date-text">
                            <?= htmlspecialchars($viacao->data_criacao, ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td>
                            <div class="actions" style="justify-content: flex-end;">
                                <a href="/viacoes/<?= (int) $viacao->id ?>/edit" class="btn btn-ghost">
                                    Editar
                                </a>

                                <form method="post" action="/viacoes/<?= (int) $viacao->id ?>/delete"
                                      onsubmit="return confirm('Remover esta marca?');">
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