<?php
declare(strict_types=1);

namespace App\Services;

use PDO;

final class UsuarioService
{
    private PDO $pdo;
    private HistoricoService $historicoService;

    public function __construct(?PDO $pdo = null, ?HistoricoService $historicoService = null)
    {
        $this->pdo = $pdo ?? \getPdo();
        // Injeta o HistoricoService para gerenciar os logs no padrão do sistema
        $this->historicoService = $historicoService ?? new HistoricoService($this->pdo);
    }

    /** obter ID do administrador logado na sessão */
    private function getAdminId(): int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return (int)($_SESSION['usuario_id'] ?? 1);
    }

    /** criar usuário */
    public function create(string $nome, string $email, string $senha, string $status): int
    {
        $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

        $stmt = $this->pdo->prepare(
            'INSERT INTO viacoes.usuarios (nome, email, senha, status, data_criacao) 
             VALUES (:nome, :email, :senha, :status, NOW())'
        );
        $stmt->execute([
            'nome' => $nome,
            'email' => $email,
            'senha' => $senhaHash,
            'status' => $status
        ]);

        $novoId = (int)$this->pdo->lastInsertId();

        // Snapshot dos dados iniciais
        $dadosDepois = [
            'nome'   => $nome,
            'email'  => $email,
            'status' => $status
        ];

        // Grava no histórico geral como escopo de 'usuarios'
        $this->historicoService->criar(
            $this->getAdminId(),
            $novoId,
            'CREATE',
            ['depois' => $dadosDepois],
            'usuarios'
        );

        return $novoId;
    }

    /** listar todos os Usuários (Esconde os deletados e aceita filtros) */
    /** listar todos os Usuários (Esconde os deletados e aceita filtros) */
    public function all(array $filtros = []): array
    {
        // Reincluída a coluna 'senha' no SELECT para a Model Usuario::fromRow não quebrar
        $sql = "SELECT id, nome, email, senha, status, data_criacao FROM viacoes.usuarios WHERE status != 'deletado'";
        $params = [];

        if (!empty($filtros['nome'])) {
            $sql .= " AND nome LIKE :nome";
            $params['nome'] = '%' . $filtros['nome'] . '%';
        }

        if (!empty($filtros['email'])) {
            $sql .= " AND email LIKE :email";
            $params['email'] = '%' . $filtros['email'] . '%';
        }

        if (!empty($filtros['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filtros['status'];
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return array_map(
            fn(array $row) => \App\Models\Usuario::fromRow($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /** buscar um único Usuário pelo ID */
    public function find(int $id): ?\App\Models\Usuario
    {
        // Reincluída a coluna 'senha' no SELECT aqui também
        $stmt = $this->pdo->prepare("SELECT id, nome, email, senha, status, data_criacao FROM viacoes.usuarios WHERE id = :id AND status != 'deletado'");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? \App\Models\Usuario::fromRow($row) : null;
    }

    /** ATUALIZAR USUÁRIO */
    public function update(int $id, string $nome, string $email, string $status, ?string $novaSenha = null): bool
    {
        // 1. Captura o estado atual do usuário antes de rodar o update para o Log Diff
        $stmtOld = $this->pdo->prepare("SELECT nome, email, status FROM viacoes.usuarios WHERE id = :id");
        $stmtOld->execute(['id' => $id]);
        $usuarioAntigo = $stmtOld->fetch(PDO::FETCH_ASSOC);

        // Se uma nova senha foi preenchida, atualiza ela com hash. Senão, mantém a atual.
        if ($novaSenha !== null && trim($novaSenha) !== '') {
            $senhaHash = password_hash($novaSenha, PASSWORD_BCRYPT);
            $stmt = $this->pdo->prepare("
                UPDATE viacoes.usuarios 
                SET nome = :nome, email = :email, status = :status, senha = :senha 
                WHERE id = :id
            ");
            $params = [
                'id' => $id,
                'nome' => $nome,
                'email' => $email,
                'status' => $status,
                'senha' => $senhaHash
            ];
        } else {
            $stmt = $this->pdo->prepare("
                UPDATE viacoes.usuarios 
                SET nome = :nome, email = :email, status = :status 
                WHERE id = :id
            ");
            $params = [
                'id' => $id,
                'nome' => $nome,
                'email' => $email,
                'status' => $status
            ];
        }

        $sucesso = $stmt->execute($params);

        // 2. Se salvou no banco, grava a alteração mapeando o antes e o depois
        if ($sucesso && $usuarioAntigo) {
            $dadosAntes = [
                'nome'   => $usuarioAntigo['nome'],
                'email'  => $usuarioAntigo['email'],
                'status' => $usuarioAntigo['status']
            ];

            $dadosDepois = [
                'nome'   => $nome,
                'email'  => $email,
                'status' => $status
            ];

            $this->historicoService->criar(
                $this->getAdminId(),
                $id,
                'UPDATE',
                ['antes' => $dadosAntes, 'depois' => $dadosDepois],
                'usuarios'
            );
        }

        return $sucesso;
    }

    /** soft delete nos registros */
    public function delete(int $id): void
    {
        // Captura dados antes de remover
        $stmtOld = $this->pdo->prepare("SELECT nome, email, status FROM viacoes.usuarios WHERE id = :id");
        $stmtOld->execute(['id' => $id]);
        $usuarioAntigo = $stmtOld->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->pdo->prepare("UPDATE viacoes.usuarios SET status = 'deletado' WHERE id = :id");
        $stmt = $stmt->execute(['id' => $id]);

        if ($usuarioAntigo) {
            $dadosAntes = [
                'nome'   => $usuarioAntigo['nome'],
                'email'  => $usuarioAntigo['email'],
                'status' => $usuarioAntigo['status']
            ];

            $this->historicoService->criar(
                $this->getAdminId(),
                $id,
                'DELETE',
                ['antes' => $dadosAntes],
                'usuarios'
            );
        }
    }

    /** poder restaurar registros marcados como deletados */
    public function restore(int $id): void
    {
        $stmt = $this->pdo->prepare("UPDATE viacoes.usuarios SET status = 'ativo' WHERE id = :id");
        $stmt->execute(['id' => $id]);

        // Captura o estado pós-restauração
        $stmtNew = $this->pdo->prepare("SELECT nome, email, status FROM viacoes.usuarios WHERE id = :id");
        $stmtNew->execute(['id' => $id]);
        $usuarioNovo = $stmtNew->fetch(PDO::FETCH_ASSOC);

        if ($usuarioNovo) {
            $dadosDepois = [
                'nome'   => $usuarioNovo['nome'],
                'email'  => $usuarioNovo['email'],
                'status' => $usuarioNovo['status']
            ];

            $this->historicoService->criar(
                $this->getAdminId(),
                $id,
                'RESTORE',
                ['depois' => $dadosDepois],
                'usuarios'
            );
        }
    }
}