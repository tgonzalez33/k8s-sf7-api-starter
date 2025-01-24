<?php 
namespace App\Tests\Unit;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
// use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Uid\Uuid;

class JwtGenerationTest extends WebTestCase
{
    public function testDecodeJWT(): void
    {
        $uuid = Uuid::v4();
        $user = new User();
        $user->setId($uuid);
        $user->setEmail('example@example.com');

        $payload = [
            'sub' => $user->getId(),
            'username' => $user->getEmail(),
            'iat' => (new \DateTime())->getTimestamp(),
            'exp' => (new \DateTime('+1 hour'))->getTimestamp(),
        ];

        $jwt = JWT::encode($payload, 'your_secret_key', 'HS256');

        $decoded = JWT::decode($jwt, new Key('your_secret_key', 'HS256'));

        $this->assertEquals($uuid, $decoded->sub);
        $this->assertEquals('example@example.com', $decoded->username);
        $this->assertTrue($decoded->iat <= time());
        $this->assertTrue($decoded->exp > time());
    }
}