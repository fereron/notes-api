<?php

namespace App\Entity;

use App\Entity\Note\Image;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("notes")
 * @ORM\Entity(repositoryClass="App\Repository\NoteRepository")
 */
class Note
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $body;

    /**
     * @ORM\Embedded(class="App\Entity\Note\Image", columnPrefix=false)
     * @var Image
     */
    private $image;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notes")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(Image $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->updated_at;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->created_at = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updated_at = $updatedAt;
        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor($user): self
    {
        $this->author = $user;
        return $this;
    }

}
