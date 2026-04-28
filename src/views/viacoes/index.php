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
            <th>Nacionalidade</th>
            <th>Criada em</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($viacoes as $nome): ?>
            <tr>
                <td><?= (int) $nome->id ?></td>
                <td><?= htmlspecialchars($nome->nome, ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= $nome->cidade ? 'Importado' : 'Nacional' ?></td>
                <td><?= htmlspecialchars($nome->data_criacao, ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <span class="actions">
                        <a href="/viacoes/<?= (int) $nome->id ?>/edit">Editar</a>

                        <form method="post" action="/viacoes/<?= (int) $nome->id ?>/delete" onsubmit="return confirm('Remover esta marca?');">
                            <button type="submit">Excluir</button>
                        </form>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
