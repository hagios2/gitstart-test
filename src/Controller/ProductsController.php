<?php

namespace App\Controller;

use App\Entity\Product;
use App\Services\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductsController extends AbstractController
{
    public function __construct(
        protected ProductService $productService,
        protected ValidatorInterface $validator
    )
    {

    }

    #[Route('/api/products', name: 'api_create_products', methods: ['POST'])]
    public function store(): JsonResponse
    {
        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }

        $this->productService->create();
    }

    #[Route('/api/products', name: 'api_list_products', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json(
            $this->productService->getProducts(),
        );
    }

    #[Route('/api/products/{product}', name: 'api_get_a_product', methods: ['GET'])]
    public function getProduct(Product $product): JsonResponse
    {
        return $this->json($product);

    }

    #[Route('/api/products/{product}', name: 'api_update_a_product', methods: ['PUT'])]
    public function updateProduct()
    {

    }

    #[Route('/api/products/{product}', name: 'api_delete_a_product', methods: ['DELETE'])]
    public function delete()
    {

    }
}
