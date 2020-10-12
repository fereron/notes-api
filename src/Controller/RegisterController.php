<?php

namespace App\Controller;

use App\Command\UserCreateCommand;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterController extends ApiController
{
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private CommandBus $bus;

    public function __construct(CommandBus $bus, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->bus = $bus;
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $command = $this->serializer->deserialize($request->getContent(), UserCreateCommand::class, 'json');
        $violations = $this->validator->validate($command);

        if ($violations->count() > 0) {
            return $this->respondValidationErrors($violations);
        }

        $this->bus->handle($command);

        return $this->respondWithSuccess('User successfully created');
    }

}