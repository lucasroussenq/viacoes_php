<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Services\CoffeeBrandService;

/** Controla o fluxo HTTP de marcas e delega persistencia ao CoffeeBrandService. */
final class CoffeeBrandsController
{
    /** Service usado para consultar e alterar marcas. */
    private CoffeeBrandService $coffeeBrandService;

    /** @param CoffeeBrandService|null $coffeeBrandService Permite injecao em testes. */
    public function __construct(?CoffeeBrandService $coffeeBrandService = null)
    {
        $this->coffeeBrandService = $coffeeBrandService ?? new CoffeeBrandService();
    }

    /** Lista marcas e renderiza a tela principal. */
    public function index(): void
    {
        $brands = $this->coffeeBrandService->all();

        View::render('brands/index', [
            'title' => 'Marcas de café',
            'brands' => $brands,
        ]);
    }

    /** Exibe o formulario de criacao. */
    public function create(): void
    {
        View::render('brands/create', [
            'title' => 'Criar marca',
            'errors' => [],
            'old' => [
                'brand' => '',
                'description' => '',
                'is_imported' => false,
            ],
        ]);
    }

    /** Processa o POST de criacao com PRG (Post Redirect Get). */
    public function store(): void
    {
        $brandName = trim((string) ($_POST['brand'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $isImported = isset($_POST['is_imported']) && (string) $_POST['is_imported'] === '1';

        $errors = $this->validate($brandName);

        if ($errors !== []) {
            View::render('brands/create', [
                'title' => 'Criar marca',
                'errors' => $errors,
                'old' => [
                    'brand' => $brandName,
                    'description' => $description,
                    'is_imported' => $isImported,
                ],
            ]);
            return;
        }

        $id = $this->coffeeBrandService->create(
            $brandName,
            $description !== '' ? $description : null,
            $isImported
        );

        View::flash('success', 'Marca criada com sucesso (#' . $id . ').');
        View::redirect('/brands');
    }

    /** Exibe o formulario de edicao de uma marca. */
    public function edit(int $id): void
    {
        $brand = $this->coffeeBrandService->find($id);

        if ($brand === null) {
            http_response_code(404);
            echo 'Marca não encontrada.';
            return;
        }

        View::render('brands/edit', [
            'title' => 'Editar marca',
            'marca' => $brand,
            'errors' => [],
            'old' => [
                'brand' => $brand->brand,
                'description' => $brand->description ?? '',
                'is_imported' => $brand->isImported,
            ],
        ]);
    }

    /** Processa o POST de atualizacao. */
    public function update(int $id): void
    {
        $brand = $this->coffeeBrandService->find($id);

        if ($brand === null) {
            http_response_code(404);
            echo 'Marca não encontrada.';
            return;
        }

        $brandName = trim((string) ($_POST['brand'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $isImported = isset($_POST['is_imported']) && (string) $_POST['is_imported'] === '1';

        $errors = $this->validate($brandName);

        if ($errors !== []) {
            View::render('brands/edit', [
                'title' => 'Editar marca',
                'marca' => $brand,
                'errors' => $errors,
                'old' => [
                    'brand' => $brandName,
                    'description' => $description,
                    'is_imported' => $isImported,
                ],
            ]);
            return;
        }

        $this->coffeeBrandService->update(
            $id,
            $brandName,
            $description !== '' ? $description : null,
            $isImported
        );

        View::flash('success', 'Marca atualizada com sucesso.');
        View::redirect('/brands');
    }

    /** Remove uma marca via POST para evitar delete por GET. */
    public function destroy(int $id): void
    {
        $brand = $this->coffeeBrandService->find($id);

        if ($brand === null) {
            http_response_code(404);
            echo 'Marca não encontrada.';
            return;
        }

        $this->coffeeBrandService->delete($id);

        View::flash('success', 'Marca removida com sucesso.');
        View::redirect('/brands');
    }

    /** @return list<string> Retorna erros de validacao do nome da marca. */
    private function validate(string $brandName): array
    {
        $errors = [];

        if ($brandName === '') {
            $errors[] = 'O nome da marca é obrigatório.';
        }

        if (strlen($brandName) > 255) {
            $errors[] = 'O nome da marca deve ter no máximo 255 caracteres.';
        }

        return $errors;
    }
}
