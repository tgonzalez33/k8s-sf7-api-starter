<?php

namespace App\Tests\Unit;

use App\Tests\Utils\DatabasePurger;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

class UserCreationTest extends WebTestCase
{
    protected KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        //parent::setUp();

        // Crée un client HTTP pour démarrer le kernel
        $this->client = static::createClient();
        // Récupérer l'EntityManager via le container après avoir démarré le kernel
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        
        if (!$entityManager instanceof EntityManagerInterface) {
            throw new \LogicException('EntityManager not found or invalid.');
        }

        // Purger la base de données
         /** @var EntityManagerInterface $entityManager */
        $this->entityManager = $entityManager;
        $purger = new DatabasePurger($this->entityManager);
        $purger->purge();
    }

    public function testCreateUser(): void
    {
        // Les données pour créer un nouvel utilisateur
        $userData = [
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ];

        $content = json_encode($userData);
        if ($content === false) {
            throw new \RuntimeException('Failed to encode JSON.');
        }

        // Effectuer une requête POST pour créer un utilisateur
        $this->client->request(
            'POST',
            '/register',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $content
        );

        // Vérifier que le statut HTTP est 201 (Created)
        $this->assertResponseStatusCodeSame(201);

        // Vérifier que la réponse contient les informations attendues
        $responseContent = $this->client->getResponse()->getContent();
        if ($responseContent === false) {
            throw new \RuntimeException('Response content is empty or invalid.');
        }

        $this->assertJson($responseContent);
        $responseData = json_decode($responseContent, true);

        $this->assertEquals('User with email testuser@example.com created successfully', $responseData['message']);
    }
}
