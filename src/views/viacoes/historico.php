<?php

declare(strict_types=1);

use App\Models\Historico;

/**
 * @var list<Historico> $historico
 * @var string $title
 * @var string $filtroUsuario
 * @var string $filtroViacao
 * @var string $filtroAcao
 */

$filtroUsuario = $filtroUsuario ?? '';
$filtroViacao  = $filtroViacao  ?? '';
$filtroAcao    = $filtroAcao    ?? '';

// Mapa de rótulos para exibição amigável dos campos
$rotulos = [
        'nome'   => 'Nome',
        'url'    => 'Site',
        'cidade' => 'Cidade',
        'status' => 'Status',
        'logo'   => 'Logo',
];

// Formata um valor de campo para exibição legível
$formatar = static function (string $campo, mixed $valor): string {
    if ($valor === null) {
        return '—';
    }
    if ($campo === 'status') {
        return $valor ? 'Ativo' : 'Inativo';
    }
    if ($campo === 'logo') {
        return $valor !== '' && $valor !== null ? '✔ ' . htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8') : '—';
    }
    $str = (string) $valor;
    return $str !== '' ? htmlspecialchars($str, ENT_QUOTES, 'UTF-8') : '—';
};

// Badge de cor por ação
$badgeAcao = static function (string $acao): string {
    $map = [
            'criar'   => 'badge badge-active',
            'editar'  => 'badge',
            'deletar' => 'badge badge-inactive',
    ];
    $class = $map[$acao] ?? 'badge';
    return '<span class="' . $class . '">' . htmlspecialchars($acao, ENT_QUOTES, 'UTF-8') . '</span>';
};

$temFiltro = $filtroUsuario !== '' || $filtroViacao !== '' || $filtroAcao !== '';

?>
<link rel="stylesheet" href="/css/layout.css">
<div class="container">

    <div class="page-header">
        <h1>Histórico de edições</h1>
        <a href="/viacoes" class="btn btn-ghost">← Voltar</a>
    </div>

    <!-- Filtros -->
    <form method="get" action="/viacoes/historico" class="search-bar search-bar--multi">
        <div class="search-group search-group--multi">
            <input
                    type="search"
                    name="usuario"
                    class="search-input"
                    placeholder="Nome do usuário…"
                    value="<?= htmlspecialchars($filtroUsuario, ENT_QUOTES, 'UTF-8') ?>"
                    autocomplete="off"
            >
            <input
                    type="search"
                    name="viacao"
                    class="search-input"
                    placeholder="Nome da viação…"
                    value="<?= htmlspecialchars($filtroViacao, ENT_QUOTES, 'UTF-8') ?>"
                    autocomplete="off"
            >
            <select name="acao" class="search-select">
                <option value="">Todas as ações</option>
                <option value="criar"   <?= $filtroAcao === 'criar'   ? 'selected' : '' ?>>Criar</option>
                <option value="editar"  <?= $filtroAcao === 'editar'  ? 'selected' : '' ?>>Editar</option>
                <option value="deletar" <?= $filtroAcao === 'deletar' ? 'selected' : '' ?>>Deletar</option>
            </select>
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <?php if ($temFiltro): ?>
                <a href="/viacoes/historico" class="btn btn-ghost">Limpar</a>
            <?php endif; ?>
        </div>
        <?php if ($temFiltro): ?>
            <p class="search-info">
                <?= count($historico) ?> registro(s) encontrado(s)
                <?php
                $partes = [];
                if ($filtroUsuario !== '') $partes[] = 'usuário "<strong>' . htmlspecialchars($filtroUsuario, ENT_QUOTES, 'UTF-8') . '</strong>"';
                if ($filtroViacao  !== '') $partes[] = 'viação "<strong>'  . htmlspecialchars($filtroViacao,  ENT_QUOTES, 'UTF-8') . '</strong>"';
                if ($filtroAcao    !== '') $partes[] = 'ação "<strong>'    . htmlspecialchars($filtroAcao,    ENT_QUOTES, 'UTF-8') . '</strong>"';
                echo 'para ' . implode(', ', $partes);
                ?>
            </p>
        <?php endif; ?>
    </form>

    <?php if (count($historico) === 0): ?>
        <div class="card empty-state">
            <?php if ($temFiltro): ?>
                <p>Nenhum registro encontrado para os filtros aplicados.</p>
            <?php else: ?>
                <p>Nenhuma ação registrada ainda.</p>
            <?php endif; ?>
        </div>
    <?php else: ?>

        <div class="table-card">
            <table class="historico-table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Usuário</th>
                    <th>Viação ID</th>
                    <th>Ação</th>
                    <th>Alterações</th>
                    <th>Data</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($historico as $h): ?>

                    <?php
                    // Extrai os snapshots do campo dados
                    $antes  = $h->dados['antes']  ?? null;   // array ou null
                    $depois = $h->dados['depois'] ?? null;   // array ou null

                    // Para edição: calcula quais campos realmente mudaram
                    $camposAlterados = [];
                    if ($antes !== null && $depois !== null) {
                        foreach ($rotulos as $campo => $_) {
                            if (($antes[$campo] ?? null) !== ($depois[$campo] ?? null)) {
                                $camposAlterados[] = $campo;
                            }
                        }
                    }
                    ?>

                    <tr>
                        <td><strong><?= (int) $h->id ?></strong></td>

                        <td>
                            <?php if ($h->usuario_nome !== null): ?>
                                <?= htmlspecialchars($h->usuario_nome, ENT_QUOTES, 'UTF-8') ?>
                                <br><small class="dados">#<?= (int) $h->usuario_id ?></small>
                            <?php else: ?>
                                <span class="dados">#<?= (int) $h->usuario_id ?></span>
                            <?php endif; ?>
                        </td>

                        <td><?= (int) $h->viacao_id ?></td>

                        <td><?= $badgeAcao($h->acao) ?></td>

                        <td>
                            <div class="diff-grid">

                                <?php if ($h->acao === 'criar' && $depois !== null): ?>
                                    <!-- CRIAÇÃO: só exibe "depois" (estado inicial) -->
                                    <div class="diff-col unico">
                                        <h4>Dados iniciais</h4>
                                        <table>
                                            <?php foreach ($rotulos as $campo => $rotulo): ?>
                                                <tr>
                                                    <td><?= $rotulo ?>:</td>
                                                    <td><?= $formatar($campo, $depois[$campo] ?? null) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>

                                <?php elseif ($h->acao === 'deletar' && $antes !== null): ?>
                                    <!-- DELEÇÃO: só exibe "antes" (o que foi removido) -->
                                    <div class="diff-col antes">
                                        <h4>Dados removidos</h4>
                                        <table>
                                            <?php foreach ($rotulos as $campo => $rotulo): ?>
                                                <tr>
                                                    <td><?= $rotulo ?>:</td>
                                                    <td><?= $formatar($campo, $antes[$campo] ?? null) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>

                                <?php elseif ($h->acao === 'editar' && $antes !== null && $depois !== null): ?>
                                    <!-- EDIÇÃO: exibe antes e depois lado a lado, destacando o que mudou -->
                                    <div class="diff-col antes">
                                        <h4>Antes</h4>
                                        <table>
                                            <?php foreach ($rotulos as $campo => $rotulo): ?>
                                                <tr <?= in_array($campo, $camposAlterados, true) ? 'class="campo-alterado"' : '' ?>>
                                                    <td><?= $rotulo ?>:</td>
                                                    <td><?= $formatar($campo, $antes[$campo] ?? null) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                    <div class="diff-col depois">
                                        <h4>Depois</h4>
                                        <table>
                                            <?php foreach ($rotulos as $campo => $rotulo): ?>
                                                <tr <?= in_array($campo, $camposAlterados, true) ? 'class="campo-alterado"' : '' ?>>
                                                    <td><?= $rotulo ?>:</td>
                                                    <td><?= $formatar($campo, $depois[$campo] ?? null) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                    <?php if (count($camposAlterados) === 0): ?>
                                        <p class="mensagem">
                                            Nenhum campo alterado (apenas logo ou ação sem mudança de texto).
                                        </p>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <!-- Fallback para registros antigos sem estrutura antes/depois -->
                                    <div class="diff-col unico">
                                        <pre class="json"><?=
                                            htmlspecialchars(
                                                    json_encode($h->dados, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                            )
                                            ?></pre>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </td>

                        <td style="white-space:nowrap">
                            <?= htmlspecialchars($h->criado_em ?? '—', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                    </tr>

                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

</div>