<?php
declare(strict_types=1);

namespace App\Entity\Note;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable()
 */
class Image
{
    private const DIRECTORY = 'uploads/notes/';

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $image;

    public function __construct(string $image)
    {
        $this->image = $image;
    }

    public function getFilename(): string
    {
        return $this->image;
    }

    public function getWebPath(): string
    {
        return self::DIRECTORY . $this->image;
    }

}