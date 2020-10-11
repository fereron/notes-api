<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Validator\Constraints as Assert;

class UserCreateCommand
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min=6)
     */
    public $password;

}