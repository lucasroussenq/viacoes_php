<?php

declare(strict_types=1);

namespace App\Core;

/** Renderiza views com layout e suporte a flash message. */
final class View
{
    /** @param array<string, mixed> $data Dados passados para a view. */
    public static function render(string $view, array $data = []): void
    {
        // Define o caminho base do projeto (pasta src)
        $basePath = dirname(__DIR__);
        // Define o caminho para o arquivo específico da view
        $viewFile = $basePath . '/views/' . $view . '.php';
        // Define o caminho para o layout principal que envolve a view
        $layoutFile = $basePath . '/views/_layout.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'View não encontrada: ' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8');
            return;
        }

        if (!is_file($layoutFile)) {
            http_response_code(500);
            echo 'Layout não encontrado.';
            return;
        }

        // Transforma chaves do array em variáveis locais (ex: ['nome' => 'João'] vira $nome)
        extract($data, EXTR_SKIP);

        // Inicia o buffer de saída: nada é enviado ao navegador ainda, fica guardado na memória
        ob_start();
        // Executa o arquivo da view, que agora tem acesso às variáveis extraídas acima
        require $viewFile;
        $content = (string) ob_get_clean();

        require $layoutFile;
    }

    /** Redireciona para um path e encerra a requisicao. */
    public static function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    /** Salva uma flash message na sessao para o proximo request. */
    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }

    /** @return array{type: string, message: string}|null Le e remove a flash message. */
    public static function pullFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        if (!is_array($flash)) {
            return null;
        }

        if (!isset($flash['type'], $flash['message'])) {
            return null;
        }

        return [
            'type' => (string) $flash['type'],
            'message' => (string) $flash['message'],
        ];
    }
}
