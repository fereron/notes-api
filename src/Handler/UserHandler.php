<?php
declare(strict_types=1);

namespace App\Handler;

use App\Command\UserCreateCommand;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserHandler
{
    private UserPasswordEncoderInterface $passwordEncoder;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    public function handleUserCreate(UserCreateCommand $command)
    {
        $user = new User;
        $user
            ->setEmail($command->email)
            ->setPassword($this->passwordEncoder->encodePassword($user, $command->password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

}