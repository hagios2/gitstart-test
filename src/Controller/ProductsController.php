<?php

namespace App\Controller;

use App\DTO\ProductDTO;
use App\Entity\Product;
use App\Services\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductsController extends AbstractController
{
    public function __construct(
        protected ProductService $productService,
        protected ValidatorInterface $validator
    ) {
    }

    #[Route('/api/products', name: 'api_create_products', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $payload = $request->getPayload();
        $dto = new ProductDto($request, $this->validator);
        $dto->name = $payload->get('name') ?? null;
        $dto->price = $payload->get('price') ?? null;
        $dto->description = $payload->get('description');

        if ($dto->errorsCount) {
            return $dto->sendResponse();
        }

        return $this->json([
            'message' => 'Product created successfully',
            'data' => $this->productService->create($dto)
        ]);
    }

    #[Route('/api/products', name: 'api_list_products', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Fetched products successfully',
            'data' => $this->productService->getProducts()
        ]);
    }

    #[Route('/api/products/{id}', name: 'api_get_a_product', methods: ['GET'])]
    public function getProduct(Product $product): JsonResponse
    {
        return $this->json([
            'message' => 'Fetched product successfully',
            'data' => $product
        ]);
    }

    #[Route('/api/products/{id}', name: 'api_update_a_product', methods: ['PUT'])]
    public function updateProduct(Product $product, Request $request): JsonResponse
    {
        $payload = $request->getPayload();
        $dto = new ProductDto($request, $this->validator);
        $dto->name = $payload->get('name') ?? null;
        $dto->price = $payload->get('price') ?? null;
        $dto->description = $payload->get('description');

        if ($dto->errorsCount) {
            return $dto->sendResponse();
        }

        return $this->json([
            'message' => 'Product updated successfully',
            'data' => $this->productService->updateProduct($product->getId(), $dto)
        ]);
    }

    #[Route('/api/products/{id}', name: 'api_delete_a_product', methods: ['DELETE'])]
    public function delete(Product $product): JsonResponse
    {
        $this->productService->deleteProduct($product);

        return $this->json([
            'message' => 'Product deleted successfully',
        ]);
    }
}
