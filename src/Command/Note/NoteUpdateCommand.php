<?php
declare(strict_types=1);

namespace App\Command\Note;

use Symfony\Component\Validator\Constraints as Assert;

class NoteUpdateCommand
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(max=255)
     */
    public $title;

    /**
     * @var string
     * @Assert\Type("string")
     */
    public $body;

    /**
     * @Assert\Image()
     */
    public $image;

    public function __construct(int $id = null)
    {
        $this->id = $id;
    }

}