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
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     *
     * @param Request $request
     * @param CommandBus $bus
     * @return JsonResponse
     */
    public function register(Request $request, CommandBus $bus): JsonResponse
    {
        $command = $this->serializer->deserialize($request->getContent(), UserCreateCommand::class, 'json');
        $violations = $this->validator->validate($command);

        if ($violations->count() > 0) {
            $json = $this->serializer->serialize($violations, 'json');

            return $this->respondValidationError($json);
        }

        $bus->handle($command);

        return $this->respondWithSuccess('User successfully created');
    }

}