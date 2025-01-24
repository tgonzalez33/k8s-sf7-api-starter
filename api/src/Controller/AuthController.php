<?php 
namespace App\Controller;

use App\Entity\User;
use Firebase\JWT\JWT;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Firebase\JWT\Key;

class AuthController extends AbstractController
{
    private string $jwtSecret;

    public function __construct(string $jwtSecret)
    {
        $this->jwtSecret = $jwtSecret;
    }

    #[Route('/auth/login', name: 'auth_login', methods: ['POST'])]
    public function login(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Invalid credentials'], 400);
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }

        $payload = [
            'sub' => $user->getId(),
            'username' => $user->getEmail(),
            'iat' =>  (new \DateTime())->getTimestamp(),
            'exp' => (new \DateTime('+1 hour'))->getTimestamp(),
        ];

        $jwt = JWT::encode($payload, $this->jwtSecret, 'HS256');
        return new JsonResponse(['token' => $jwt]);
    }
}
