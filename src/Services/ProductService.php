<?php

namespace App\Services;

use App\Repository\ProductRepository;

class ProductService
{
    public function __construct(protected ProductRepository $productRepository)
    {

    }
    public function getProducts(): array
    {
        return $this->productRepository->findAll();
    }
}
