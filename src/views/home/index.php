<?php

declare(strict_types=1);

use App\Models\Viacao;

/** @var list<Viacao> $viacoes */

?>

<h1>Marcas</h1>

<p><a href="/viacoes/create">Criar marca</a></p>

<?php if (count($viacoes) === 0): ?>
    <p>Nenhuma marca cadastrada.</p>
<?php else: ?>
    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Cidade</th>
            <th>logo</th>
            <th>Status</th>
            <th>Criada em</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($viacoes as $viacao): ?>
            <tr>
                <td><?= (int) $viacao->id ?></td>
                <td><?= htmlspecialchars($viacao->nome, ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= $viacao->cidade ?></td>
                <td><?= $viacao->logo ?></td>
                <td><?= $viacao->status ? 'ativo' : 'inativo' ?></td>
                <td><?= htmlspecialchars($viacao->data_criacao, ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <span class="actions">
                        <a href="/viacoes/<?= (int) $viacao->id ?>/edit">Editar</a>

                        <form method="post" action="/viacoes/<?= (int) $viacao->id ?>/delete" onsubmit="return confirm('Remover esta marca?');">
                            <button type="submit">Excluir</button>
                        </form>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
