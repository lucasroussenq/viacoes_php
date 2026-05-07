<?php


//esse arquivo não está implementado!!


declare(strict_types=1);
 ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/style.css">
    <title>Histórico</title>
</head>
<body>

<div class="container">

    <div class="page-header">
        <h1>Histórico de edições</h1>
        <a href="/index" class="btn btn-ghost">← Voltar</a>
    </div>

    <div class="card">
        <table>
            <thead>
            <tr>
                <th>Viação</th>
                <th>Nome anterior</th>
                <th>Cidade anterior</th>
                <th>Status anterior</th>
                <th>Editado em</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($historico as $h): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($h['nome_atual']) ?></strong></td>
                    <td><?= htmlspecialchars($h['nome']) ?></td>
                    <td><?= htmlspecialchars($h['cidade']) ?></td>

                    <td>
                        <?php if ($h['status']): ?>
                            <span class="badge badge-active">Ativo</span>
                        <?php else: ?>
                            <span class="badge badge-inactive">Inativo</span>
                        <?php endif; ?>
                    </td>

                    <td class="date-text"><?= $h['salvo_em'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>
    </div>

</div>

</body>
</html>
