<?php
declare(strict_types=1);

namespace App\Controller;

use App\Command\Note\NoteCreateCommand;
use App\Command\Note\NoteDeleteCommand;
use App\Command\Note\NoteUpdateCommand;
use App\Entity\Note;
use App\Repository\NoteRepository;
use App\Resource\NoteResource;
use App\Security\NoteAccess;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NotesController extends ApiController
{
    /**
     * @var NoteRepository
     */
    private $noteRepository;

    /**
     * @var CommandBus
     */
    private $bus;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(NoteRepository $noteRepository, CommandBus $bus, ValidatorInterface $validator)
    {
        $this->bus = $bus;
        $this->validator = $validator;
        $this->noteRepository = $noteRepository;
    }

    /**
     * @Route("/api/notes/create", name="notes.create", methods={"POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $data = array_replace($request->request->all(), [
            'author_id' => $this->getUser()->getId(),
            'image'     => $request->files->get('image')
        ]);

        $command = new NoteCreateCommand($data);
        $violations = $this->validator->validate($command);

        if ($violations->count() > 0) {
            return $this->respondValidationErrors($violations);
        }

        $this->bus->handle($command);

        return $this->respondWithSuccess('Note successfully created');
    }

    /**
     * @Route("/api/notes/{id}/update", name="notes.update", methods={"POST"})
     *
     * @param Note $note
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Note $note, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted(NoteAccess::MANAGE, $note);

        $data = array_replace($request->request->all(), [
            'id' => $note->getId(),
            'image' => $request->files->get('image')
        ]);

        $command = new NoteUpdateCommand($data);
        $violations = $this->validator->validate($command);

        if ($violations->count() > 0) {
            return $this->respondValidationErrors($violations);
        }

        $this->bus->handle($command);

        return $this->respondWithSuccess("Note \"{$command->title}\" successfully updated");
    }

    /**
     * @Route("/api/notes/{id}/delete", name="notes.delete", methods={"DELETE"})
     *
     * @param Note $note
     * @return JsonResponse
     */
    public function delete(Note $note): JsonResponse
    {
        $this->denyAccessUnlessGranted(NoteAccess::MANAGE, $note);

        $command = new NoteDeleteCommand($note->getId());
        $this->bus->handle($command);

        return $this->respondWithSuccess("Note successfully deleted");
    }

    /**
     * @Route("/api/notes/{id}", name="notes.show", methods={"GET"})
     *
     * @param Note $note
     * @param NoteResource $resource
     * @return JsonResponse
     */
    public function show(Note $note, NoteResource $resource): JsonResponse
    {
        $this->denyAccessUnlessGranted(NoteAccess::MANAGE, $note);

        return $this->json([
            'note' => $resource->transform($note)
        ]);
    }

    /**
     * @Route("/api/notes", name="notes", methods={"GET"})
     *
     * @param NoteResource $resource
     * @return JsonResponse
     */
    public function list(NoteResource $resource): JsonResponse
    {
        $notes = $this->noteRepository->getUserNotes($this->getUser()->getId());

        return $this->json([
            'notes' => $resource->transform($notes)
        ]);
    }

}