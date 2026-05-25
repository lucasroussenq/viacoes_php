<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\UsuarioService;

final class UsuarioController
{
    private UsuarioService $usuarioService;

    public function __construct(?UsuarioService $usuarioService = null)
    {
        $this->usuarioService = $usuarioService ?? new UsuarioService();
    }

    /**
     * Apenas recebe os parâmetros de busca e passa para o Service
     */
    public function index(): void
    {
        $filtros = [
            'nome'   => $_GET['nome'] ?? '',
            'email'  => $_GET['email'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];

        $temFiltro = ($filtros['nome'] !== '' || $filtros['email'] !== '' || $filtros['status'] !== '');

        // O Service agora é quem se vira para trazer filtrado
        $usuarios = $this->usuarioService->all($filtros);

        require __DIR__ . '/../views/usuarios/index.php';
    }

    public function create(): void
    {
        $errors = [];
        $old = [];
        require __DIR__ . '/../views/usuarios/create.php';
    }

    public function store(): void
    {
        $nome   = $_POST['nome'] ?? '';
        $email  = $_POST['email'] ?? '';
        $senha  = $_POST['senha'] ?? '';
        $status = $_POST['status'] ?? 'ativo';

        if ($nome === '' || $email === '' || $senha === '') {
            $errors = ['Preencha todos os campos obrigatórios.'];
            $old = ['nome' => $nome, 'email' => $email, 'status' => $status];
            require __DIR__ . '/../views/usuarios/create.php';
            return;
        }

        $this->usuarioService->create($nome, $email, $senha, $status);

        header('Location: /usuarios');
        exit;
    }

    public function edit(int $id): void
    {
        $usuario = $this->usuarioService->find($id);

        if (!$usuario) {
            header('Location: /usuarios');
            exit;
        }

        $errors = [];

        require __DIR__ . '/../views/usuarios/edit.php';
    }

    public function update(int $id): void
    {
        $nome   = trim((string)($_POST['nome'] ?? ''));
        $email  = trim((string)($_POST['email'] ?? ''));
        $senha  = $_POST['senha'] ?? ''; // Captura a senha do POST

        $status = (isset($_POST['status']) && $_POST['status'] === '1') ? 'ativo' : 'inativo';

        if ($nome === '' || $email === '') {
            $usuario = $this->usuarioService->find($id);
            $errors = ['Nome e E-mail não podem ficar vazios.'];
            require __DIR__ . '/../views/usuarios/edit.php';
            return;
        }

        $this->usuarioService->update($id, $nome, $email, $status, $senha !== '' ? $senha : null);

        header('Location: /usuarios');
        exit;
    }

    public function destroy(int $id): void
    {
        $this->usuarioService->delete($id);

        header('Location: /usuarios');
        exit;
    }
}