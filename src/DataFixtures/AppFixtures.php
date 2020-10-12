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

        $note = (new Note)
            ->setTitle('Test title')
            ->setBody('Test Body')
            ->setAuthor($user)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        $manager->persist($note);

        $note2 = (new Note)
            ->setTitle('Test title 2')
            ->setBody('Test Body')
            ->setAuthor($user)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        $manager->persist($note2);

        $note3 = (new Note)
            ->setTitle('Я лучший кандидат!')
            ->setBody('Я лучший кандидат из за того что стараюсь всегда находить наилучшие решения и практики для выполнения поставленных задач. Имею хорошие аналитические способности. Умею работать в команде, находить общий язык с каждым членом команды. Мне очень понравились ваши проекты и мне было бы очень приятно разрабатывать их вместе =)')
            ->setAuthor($user)
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime());

        $manager->persist($note3);

        $manager->flush();
    }
}
