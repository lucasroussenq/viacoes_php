<?php
declare(strict_types=1);

/** @var list<array{
 * }> $historico
 */
?>
<link rel="stylesheet" href="/css/layout.css">

<div class="container">

    <div class="page-header">
        <h1>Histórico de edições</h1>
        <a href="/viacoes" class="btn btn-ghost">← Voltar</a>
    </div>

    <div class="card">
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>ID Usuário</th>
                <th>ID Viação</th>
                <th>Ação</th>
                <th>Dados</th>
                <th>Criado em</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($historico as $h): ?>
                <tr>
                    <td><?= (int) $h['id'] ?></td>
                    <td><?= (int) $h['usuario_id'] ?></td>
                    <td><?= (int) $h['viacao_id'] ?></td>
                    <td><?= htmlspecialchars($h['acao'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($h['dados'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($h['data_criacao'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>