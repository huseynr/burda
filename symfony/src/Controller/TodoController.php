<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Swagger\Annotations as SWG;

/**
 * @Route("/apiv/todos")
 */
class TodoController extends AbstractController
{
    private TodoRepository $todoRepository;
    private ValidatorInterface $validator;
    private LoggerInterface $logger;

    public function __construct(TodoRepository $todoRepository, ValidatorInterface $validator, LoggerInterface $logger)
    {
        $this->todoRepository = $todoRepository;
        $this->validator = $validator;
        $this->logger = $logger;
    }

    /**
     * @Route("/", name="todo_index", methods={"GET"})
     * @SWG\Tag(name="Todos")
     * @SWG\Response(
     *     response=200,
     *     description="Returns the list of todos",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@NelmioModel(type=Todo::class, groups={"todo:read"}))
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $todos = $this->todoRepository->findAll();

        return $this->json($todos, Response::HTTP_OK, [], ['groups' => 'todo:read']);
    }

    /**
     * @Route("/", name="todo_create", methods={"POST"})
     * @SWG\Tag(name="Todos")
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(ref=@NelmioModel(type=Todo::class, groups={"todo:write"}))
     * )
     * @SWG\Response(
     *     response=201,
     *     description="Returns the created todo",
     *     @SWG\Schema(ref=@NelmioModel(type=Todo::class, groups={"todo:read"}))
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Validation error or invalid request"
     * )
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $todo = new Todo();
        $todo->setTitle($data['title'] ?? '');
        $todo->setDescription($data['description'] ?? '');
        $todo->setStatus($data['status'] ?? 'pending');

        // Validate the Todo entity
        $errors = $this->validator->validate($todo);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->todoRepository->save($todo);
        } catch (\Exception $e) {
            $this->logger->error('Failed to save todo: ' . $e->getMessage());
            return $this->json(['message' => 'Failed to save todo'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($todo, Response::HTTP_CREATED, [], ['groups' => 'todo:read']);
    }

    /**
     * @Route("/{id}", name="todo_show", methods={"GET"})
     * @SWG\Tag(name="Todos")
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     required=true,
     *     description="The ID of the todo"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the requested todo",
     *     @SWG\Schema(ref=@NelmioModel(type=Todo::class, groups={"todo:read"}))
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Todo not found"
     * )
     */
    public function show(int $id): JsonResponse
    {
        $todo = $this->todoRepository->findOneById($id);

        if (!$todo) {
            return $this->json(['message' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($todo, Response::HTTP_OK, [], ['groups' => 'todo:read']);
    }

    /**
     * @Route("/{id}", name="todo_update", methods={"PUT"})
     * @SWG\Tag(name="Todos")
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     required=true,
     *     description="The ID of the todo"
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     required=true,
     *     @SWG\Schema(ref=@NelmioModel(type=Todo::class, groups={"todo:write"}))
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Returns the updated todo",
     *     @SWG\Schema(ref=@NelmioModel(type=Todo::class, groups={"todo:read"}))
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Validation error or invalid request"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Todo not found"
     * )
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $todo = $this->todoRepository->findOneById($id);

        if (!$todo) {
            return $this->json(['message' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }

        $todo->setTitle($data['title'] ?? $todo->getTitle());
        $todo->setDescription($data['description'] ?? $todo->getDescription());
        $todo->setStatus($data['status'] ?? $todo->getStatus());

        // Validate the Todo entity
        $errors = $this->validator->validate($todo);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->todoRepository->save($todo);
        } catch (\Exception $e) {
            $this->logger->error('Failed to update todo: ' . $e->getMessage());
            return $this->json(['message' => 'Failed to update todo'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json($todo, Response::HTTP_OK, [], ['groups' => 'todo:read']);
    }

    /**
     * @Route("/{id}", name="todo_delete", methods={"DELETE"})
     * @SWG\Tag(name="Todos")
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     required=true,
     *     description="The ID of the todo"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Todo deleted successfully"
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Todo not found"
     * )
     */
    public function delete(int $id): JsonResponse
    {
        $todo = $this->todoRepository->findOneById($id);

        if (!$todo) {
            return $this->json(['message' => 'Todo not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $this->todoRepository->delete($todo);
        } catch (\Exception $e) {
            $this->logger->error('Failed to delete todo: ' . $e->getMessage());
            return $this->json(['message' => 'Failed to delete todo'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['message' => 'Todo deleted successfully'], Response::HTTP_OK);
    }
}

