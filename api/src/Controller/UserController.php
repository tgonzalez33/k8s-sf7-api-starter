<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService;

class UserController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {

    }

    #[Route('/users', name: 'user_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $result = [];
        $users = $this->entityManager->getRepository(User::class)->findAll();
        foreach($users as $user) {
            $result[] = [
                '_id' => $user->getId(),
                'email' => $user->getEmail()
            ];
        }

        $response = new JsonResponse($result);
        return $response;
    }




    #[Route('/register', name: 'register_user', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required'], 400);
        }

        // Check if user exist
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'User already exists'], 400);
        }

        // Create user
        $user = new User();
        $user->setEmail($data['email']);
        $user->setDateAdd(new \DateTime());
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User with email ' . $data['email'] . ' created successfully'], 201);
    }
}

