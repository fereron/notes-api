<?php
declare(strict_types=1);

namespace App\Command\Note;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class NoteCreateCommand
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     * @Assert\NotBlank()
     */
    public $author_id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @Assert\Length(max=255)
     */
    public $title;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    public $body;

    /**
     * @var UploadedFile|null
     * @Assert\Image()
     */
    public $image;

    public function __construct(array $properties)
    {
        foreach ($properties as $property => $value) {
            $this->$property = $value;
        }
    }

}