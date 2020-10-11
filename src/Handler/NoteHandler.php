<?php
declare(strict_types=1);

namespace App\Handler;

use App\Command\Note\NoteCreateCommand;
use App\Command\Note\NoteDeleteCommand;
use App\Command\Note\NoteUpdateCommand;
use App\DependencyInjection\Uploader\FileUploaderInterface;
use App\Entity\Note;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class NoteHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FileUploaderInterface
     */
    private $fileUploader;

    public function __construct(EntityManagerInterface $entityManager, FileUploaderInterface $fileUploader)
    {
        $this->entityManager = $entityManager;
        $this->fileUploader = $fileUploader;
    }

    public function handleNoteCreate(NoteCreateCommand $command): void
    {
        $note = new Note;

        $note
            ->setTitle($command->title)
            ->setBody($command->body)
            ->setAuthor(
                $this->entityManager->getReference(User::class, $command->author_id)
            )
            ->setCreatedAt(new \DateTime)
            ->setUpdatedAt(new \DateTime);

        if ($command->image) {
            $image = new Note\Image($this->fileUploader->upload($command->image));
            $note->setImage($image);
        }

        $this->entityManager->persist($note);
        $this->entityManager->flush();
    }

    public function handleNoteUpdate(NoteUpdateCommand $command): void
    {
        $note = $this->entityManager->find(Note::class, $command->id);

        $note
            ->setTitle($command->title)
            ->setBody($command->body)
            ->setUpdatedAt(new \DateTime);

        $this->entityManager->persist($note);
        $this->entityManager->flush();
    }

    public function handleNoteDelete(NoteDeleteCommand $command): void
    {
        $note = $this->entityManager->find(Note::class, $command->id);

        $this->entityManager->remove($note);
        $this->entityManager->flush();
    }

}