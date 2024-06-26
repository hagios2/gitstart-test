<?php

namespace App\Services;

use App\DTO\ProductDTO;
use App\Entity\Product;
use App\Repository\ProductRepository;

class ProductService
{
    public function __construct(protected ProductRepository $productRepository)
    {

    }

    public function create(ProductDTO $DTO): Product
    {
        $product = new Product();
        $product->setName($DTO->name);
        $product->setPrice($DTO->price);
        $product->setDescription($DTO->description);

        $this->productRepository->save($product, true);

        return $product;
    }
    public function getProducts(): array
    {
        return $this->productRepository->findAll();
    }

    public function updateProduct($productId, ProductDTO $DTO): Product
    {
        $product = $this->productRepository->find($productId);

        $product->setName($DTO->name);
        $product->setPrice($DTO->price);
        $product->setDescription($DTO->description);
        $this->productRepository->save($product, true);

        return $product;
    }

    public function deleteProduct(Product $product): void
    {
        $this->productRepository->remove($product, true);
    }
}
