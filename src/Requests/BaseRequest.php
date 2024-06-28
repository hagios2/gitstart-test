<?php

namespace App\Requests;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest
{
    public int $errorsCount;
    public array $errorMessage;

    public function __construct(protected Request $request, protected ValidatorInterface $validator)
    {
        $this->populate();
        $this->validate();
    }

    public function validate(): void
    {
        $errors = $this->validator->validate($this);
        $messages = ['message' => 'Validation failed', 'errors' => []];

        /** @var ConstraintViolation $errors */
        foreach ($errors as $message) {
            $messages['errors'][] = [
                'property' => $message->getPropertyPath(),
                'message' => $message->getMessage(),
            ];
        }

        $this->errorsCount = count($messages['errors']);
        $this->errorMessage = $messages;
    }

    public function sendResponse(): JsonResponse
    {
        return new JsonResponse($this->errorMessage, Response::HTTP_BAD_REQUEST);
    }

    protected function populate(): void
    {
        foreach ($this->request->toArray() as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }
}
