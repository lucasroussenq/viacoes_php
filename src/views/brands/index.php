<?php

declare(strict_types=1);

use App\Models\CoffeeBrands;

/** @var list<CoffeeBrands> $brands */

?>

<h1>Marcas</h1>

<p><a href="/brands/create">Criar marca</a></p>

<?php if (count($brands) === 0): ?>
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
        <?php foreach ($brands as $brand): ?>
            <tr>
                <td><?= (int) $brand->id ?></td>
                <td><?= htmlspecialchars($brand->brand, ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= $brand->isImported ? 'Importado' : 'Nacional' ?></td>
                <td><?= htmlspecialchars($brand->createdAt, ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <span class="actions">
                        <a href="/brands/<?= (int) $brand->id ?>/edit">Editar</a>

                        <form method="post" action="/brands/<?= (int) $brand->id ?>/delete" onsubmit="return confirm('Remover esta marca?');">
                            <button type="submit">Excluir</button>
                        </form>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
