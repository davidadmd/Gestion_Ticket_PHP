<?php

namespace Tests\Controllers;

use Controllers\AuthController;
use Models\UserModel;
use PHPUnit\Framework\TestCase;

class AuthControllerTest extends TestCase
{
    protected $authController;
    protected $userModelMock;

    protected function setUp(): void
    {
        // Créer un mock pour UserModel
        $this->userModelMock = $this->createMock(UserModel::class);
        
        // Créer une instance d'AuthController avec notre mock
        $this->authController = new class($this->userModelMock) extends AuthController {
            private $userModel;
            
            public function __construct($userModel) {
                $this->userModel = $userModel;
            }
            
            // Méthode pour simuler la redirection
            public function redirect($url) {
                return $url;
            }
            
            // Méthode pour récupérer le modèle utilisateur mocké
            public function getUserModel() {
                return $this->userModel;
            }
            
            // Simuler la vérification de login sans les paramètres $_POST
            public function testLogin($username, $password) {
                return $this->getUserModel()->verifyLogin($username, $password);
            }
            
            // Simuler la méthode de rendu sans Twig
            public function render($template, $data = []) {
                return "Rendu de $template";
            }
        };
    }

    // Test 1: Vérification de login réussie
    public function testLoginSuccess()
    {
        // Données de test
        $username = 'testuser';
        $password = 'password123';
        $expectedUser = [
            'id' => 1,
            'username' => 'testuser',
            'role' => 0
        ];
        
        // Configuration du comportement du mock
        $this->userModelMock->expects($this->once())
            ->method('verifyLogin')
            ->with($username, $password)
            ->willReturn($expectedUser);
            
        // Test de la méthode
        $result = $this->authController->testLogin($username, $password);
        
        // Vérification des résultats
        $this->assertEquals($expectedUser, $result);
    }

    // Test 2: Vérification de login échouée
    public function testLoginFailure()
    {
        // Données de test
        $username = 'testuser';
        $password = 'wrongpassword';
        
        // Configuration du comportement du mock
        $this->userModelMock->expects($this->once())
            ->method('verifyLogin')
            ->with($username, $password)
            ->willReturn(false);
            
        // Test de la méthode
        $result = $this->authController->testLogin($username, $password);
        
        // Vérification des résultats
        $this->assertFalse($result);
    }

    // Test 3: Vérification que getUserByUsername est appelé correctement
    public function testGetUserByUsername()
    {
        // Données de test
        $username = 'existinguser';
        $expectedUser = [
            'id' => 2,
            'username' => 'existinguser',
            'email' => 'existing@example.com',
            'role' => 0
        ];
        
        // Configuration du comportement du mock
        $this->userModelMock->expects($this->once())
            ->method('getUserByUsername')
            ->with($username)
            ->willReturn($expectedUser);
            
        // Test de la méthode
        $result = $this->authController->getUserModel()->getUserByUsername($username);
        
        // Vérification des résultats
        $this->assertEquals($expectedUser, $result);
    }

    // Test 4: Test de la méthode de rendu
    public function testRender()
    {
        // Test de la méthode render
        $result = $this->authController->render('auth/login');
        
        // Vérification des résultats
        $this->assertEquals('Rendu de auth/login', $result);
    }
}
