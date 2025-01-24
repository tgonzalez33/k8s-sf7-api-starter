<?php
namespace App\Security;

use App\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class JwtAuthenticator extends AbstractAuthenticator
{
    private string $jwtSecret;
    /**
     * @var UserProviderInterface<User>
    */
    private UserProviderInterface $userProvider;

    /**
     * @param UserProviderInterface<User> $userProvider
     */
    public function __construct(string $jwtSecret, UserProviderInterface $userProvider)
    {
        $this->jwtSecret = $jwtSecret;
        $this->userProvider = $userProvider;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new AuthenticationException('Missing or invalid Authorization header');
        }

        $jwt = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = JWT::decode($jwt, new Key($this->jwtSecret, 'HS256'));
        } catch (\Exception $e) {
            throw new AuthenticationException('Invalid JWT token');
        }

        return new SelfValidatingPassport(
            new UserBadge($decoded->username ?? '', function ($userIdentifier) use ($decoded) {
                // Load user with his email
                return $this->userProvider->loadUserByIdentifier($decoded->username);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?JsonResponse
    {
        // Let Symfony do his request job
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        return new JsonResponse(['error' => $exception->getMessage()], 401);
    }
}
