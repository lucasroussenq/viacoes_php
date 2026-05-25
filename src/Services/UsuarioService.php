<?php
declare(strict_types=1);

namespace App\Services;

use PDO;

final class UsuarioService
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? \getPdo();
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

        return (int)$this->pdo->lastInsertId();
    }

    /** listar todos os Usuários (Esconde os deletados e aceita filtros) */
    public function all(array $filtros = []): array
    {
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** buscar um único Usuário pelo ID */
    public function find(int $id): ?\App\Models\Usuario
    {
        $stmt = $this->pdo->prepare("SELECT id, nome, email, senha, status, data_criacao FROM viacoes.usuarios WHERE id = :id AND status != 'deletado'");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? \App\Models\Usuario::fromRow($row) : null;
    }

    /** ATUALIZAR USUÁRIO (Adicionado para resolver o erro) */
    public function update(int $id, string $nome, string $email, string $status, ?string $novaSenha = null): bool
    {
        // Se uma nova senha foi preenchida, atualiza ela com hash. Senão, mantém a atual.
        if ($novaSenha !== null) {
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

        return $stmt->execute($params);
    }

    /** soft delete nos registros */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare("UPDATE viacoes.usuarios SET status = 'deletado' WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    /** poder restaurar registros marcados como deletados */
    public function restore(int $id): void
    {
        $stmt = $this->pdo->prepare("UPDATE viacoes.usuarios SET status = 'ativo' WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    private function gravarLog(int $entidadeId, string $acao, array $antes = null, array $depois = null): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Resgata o ID do administrador logado na sessão
        $alteradoPor = $_SESSION['usuario_id'] ?? 1;

        // Estrutura o JSON contendo opcionalmente o estado anterior e o atual
        $payload = [];
        if ($antes !== null) {
            $payload['antes'] = $antes;
        }
        if ($depois !== null) {
            $payload['depois'] = $depois;
        }

        $stmt = $this->pdo->prepare("
        INSERT INTO viacoes.historico_alteracoes 
            (entidade_id, entidade_tipo, campo_alterado, valor_antigo, valor_novo, alterado_por, data_alteracao)
        VALUES 
            (:entidade_id, 'usuarios', :campo_alterado, null, :valor_novo, :alterado_por, NOW())
    ");

        $stmt->execute([
            'entidade_id' => $entidadeId,
            'campo_alterado' => $acao, // 'criar', 'editar', ou 'deletar'
            'valor_novo' => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'alterado_por' => $alteradoPor
        ]);
    }
}