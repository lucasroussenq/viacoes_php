<?php
declare(strict_types=1);

/** @var list<array{
 *   user_id: int,
 *   nome_anterior: string,
 *   nome_atual: string,
 *   cidade_anterior: string,
 *   cidade_atual: string,
 *   status_anterior: bool,
 *   status_atual: bool,
 *   salvo_em: string
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
                <th>ID Usuário</th>
                <th>Nome anterior</th>
                <th>Nome atual</th>
                <th>Cidade anterior</th>
                <th>Cidade atual</th>
                <th>Status anterior</th>
                <th>Status atual</th>
                <th class="date-text">Editado em</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($historico as $h): ?>
                <tr>
                    <td><strong><?= (int) $h['user_id'] ?></strong></td>
                    <td><?= htmlspecialchars($h['nome_anterior'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($h['nome_atual'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($h['cidade_anterior'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($h['cidade_atual'], ENT_QUOTES, 'UTF-8') ?></td>

                    <td>
                        <?php if ($h['status_anterior']): ?>
                            <span class="badge badge-active">Ativo</span>
                        <?php else: ?>
                            <span class="badge badge-inactive">Inativo</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php if ($h['status_atual']): ?>
                            <span class="badge badge-active">Ativo</span>
                        <?php else: ?>
                            <span class="badge badge-inactive">Inativo</span>
                        <?php endif; ?>
                    </td>

                    <td class="date-text"><?= htmlspecialchars($h['salvo_em'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>