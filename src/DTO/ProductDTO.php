<?php

namespace App\DTO;

use App\Requests\BaseRequest;
use Symfony\Component\Validator\Constraints as Assert;

class ProductDTO extends BaseRequest
{
    #[Assert\NotBlank(message: "Name field is required")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Name cannot contain more than 255 characters"
    )]
    public string $name;

    #[Assert\NotBlank(message: 'Price field is required')]
    public int $price;

    public ?string $description = null;
//    public ?string $image = null;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}
