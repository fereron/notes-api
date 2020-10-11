<?php
declare(strict_types=1);

namespace App\Controller;

use App\Command\Note\NoteCreateCommand;
use App\Command\Note\NoteDeleteCommand;
use App\Command\Note\NoteUpdateCommand;
use App\Repository\NoteRepository;
use App\Resource\NoteResource;
use App\Security\NoteAccess;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
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

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(NoteRepository $noteRepository, CommandBus $bus, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        parent::__construct($serializer);
        $this->bus = $bus;
        $this->validator = $validator;
        $this->serializer = $serializer;
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
            'image' => $request->files->get('image')
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
     * @Route("/api/notes/{id}/update", name="notes.update", methods={"PUT"})
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $note = $this->noteRepository->find($id);

        if (!$note) {
            return $this->respondNotFound();
        }

        $this->denyAccessUnlessGranted(NoteAccess::MANAGE, $note);

        $command = $this->serializer->deserialize($request->getContent(), NoteUpdateCommand::class, 'json', [
            'object_to_populate' => new NoteUpdateCommand($id),
        ]);

        $violations = $this->validator->validate($command);

        if ($violations->count() > 0) {
            return $this->respondValidationErrors($violations);
        }

        $this->bus->handle($command);

        return $this->respondWithSuccess("Note {$command->title} successfully updated");
    }

    /**
     * @Route("/api/notes/{id}/delete", name="notes.delete", methods={"DELETE"})
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        $note = $this->noteRepository->find($id);

        if (!$note) {
            return $this->respondNotFound();
        }

        $this->denyAccessUnlessGranted(NoteAccess::MANAGE, $note);

        $command = new NoteDeleteCommand($id);

        $this->bus->handle($command);

        return $this->respondWithSuccess("Note successfully deleted");
    }

    /**
     * @todo check Note by DI
     * @Route("/api/notes/{id}", name="notes.show", methods={"GET"})
     *
     * @param int $id
     * @param NoteResource $resource
     * @return JsonResponse
     */
    public function show(int $id, NoteResource $resource): JsonResponse
    {
        $note = $this->noteRepository->find($id);

        if (!$note) {
            throw new NotFoundHttpException();
        }

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