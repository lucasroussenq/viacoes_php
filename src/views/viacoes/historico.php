<?php

declare(strict_types=1);

use App\Models\Historico;

/**
 * @var list<Historico> $historico
 * @var string $title
 */

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

?>
<link rel="stylesheet" href="/css/layout.css">
<style>
    /* Estilos específicos desta página */
    .historico-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    .historico-table th, .historico-table td { padding: 10px 12px; border-bottom: 1px solid var(--color-border, #e5e7eb); vertical-align: top; }
    .historico-table thead th { background: var(--color-surface, #f9fafb); font-weight: 600; }

    /* Bloco de diff: exibe antes e depois lado a lado */
    .diff-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; min-width: 360px; }
    .diff-col { padding: 8px 10px; border-radius: 6px; }
    .diff-col.antes  { background: #fef2f2; border: 1px solid #fecaca; }  /* vermelho claro */
    .diff-col.depois { background: #f0fdf4; border: 1px solid #bbf7d0; }  /* verde claro */
    .diff-col.unico  { background: var(--color-surface, #f9fafb); border: 1px solid var(--color-border, #e5e7eb); grid-column: 1 / -1; }

    .diff-col h4 { margin: 0 0 6px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; }
    .diff-col table { width: 100%; font-size: 0.85rem; border-collapse: collapse; }
    .diff-col table td { padding: 2px 4px; }
    .diff-col table td:first-child { color: #6b7280; white-space: nowrap; padding-right: 10px; }

    /* Destaca campos que mudaram */
    .campo-alterado td { font-weight: 600; color: #111; }
</style>

<div class="container">

    <div class="page-header">
        <h1>Histórico de edições</h1>
        <a href="/viacoes" class="btn btn-ghost">← Voltar</a>
    </div>

    <?php if (count($historico) === 0): ?>
        <div class="card empty-state">
            <p>Nenhuma ação registrada ainda.</p>
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
                                <br><small style="color:#9ca3af">#<?= (int) $h->usuario_id ?></small>
                            <?php else: ?>
                                <span style="color:#9ca3af">#<?= (int) $h->usuario_id ?></span>
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
                                        <p style="color:#9ca3af; font-size:0.8rem; grid-column:1/-1; margin:4px 0 0">
                                            Nenhum campo alterado (apenas logo ou ação sem mudança de texto).
                                        </p>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <!-- Fallback para registros antigos sem estrutura antes/depois -->
                                    <div class="diff-col unico">
                                        <pre style="margin:0; font-size:0.8rem; white-space:pre-wrap"><?=
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