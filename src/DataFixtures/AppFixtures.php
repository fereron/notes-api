<?php

namespace App\DataFixtures;

use App\Entity\Note;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User;
        $user
            ->setEmail('test@test.com')
            ->setPassword($this->encoder->encodePassword($user, '123456'));

        $manager->persist($user);

        $note = new Note();
        $note->setTitle('Test title')
            ->setBody('Test Body')
            ->setAuthor($user)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        $manager->persist($note);

        $manager->flush();
    }
}
