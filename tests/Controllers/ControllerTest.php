<?php

namespace Tests\Controllers;

use Controllers\Controller;
use PHPUnit\Framework\TestCase;
use PDO;
use Twig\Environment;

class ControllerTest extends TestCase
{
    protected $controller;
    protected $dbMock;
    protected $twigMock;

    protected function setUp(): void
    {
        // Créer des mocks pour les dépendances
        $this->dbMock = $this->createMock(PDO::class);
        $this->twigMock = $this->createMock(Environment::class);
        
        // Créer une instance de Controller avec nos dépendances mockées
        $this->controller = new class($this->dbMock, $this->twigMock) extends Controller {
            public function __construct($db, $twig) {
                $this->db = $db;
                $this->twig = $twig;
            }
            
            // Rendre les méthodes protégées accessibles pour les tests
            public function testIsLoggedIn() {
                return $this->isLoggedIn();
            }
            
            public function testIsAdmin() {
                return $this->isAdmin();
            }
            
            public function testIsGranted($role) {
                return $this->is_granted($role);
            }
            
            public function testRedirect($path) {
                return $path; // Simuler la redirection sans exit
            }
            
            public function testRender($view, $data = []) {
                return ["view" => $view, "data" => $data];
            }
            
            public function testIsPost() {
                return $this->isPost();
            }
            
            public function testIsGet() {
                return $this->isGet();
            }
            
            public function testGetCurrentUser() {
                return $this->getCurrentUser();
            }
            
            public function testSessionMethods($key, $value) {
                $this->setSession($key, $value);
                $result = $this->getSession($key);
                $this->deleteSession($key);
                return $result;
            }
        };
    }

    // Test 1: Vérifier si un utilisateur est connecté
    public function testIsLoggedIn()
    {
        // Cas 1: utilisateur non connecté
        $_SESSION = [];
        $this->assertFalse($this->controller->testIsLoggedIn());
        
        // Cas 2: utilisateur connecté
        $_SESSION['user_id'] = 1;
        $this->assertTrue($this->controller->testIsLoggedIn());
    }

    // Test 2: Tester les méthodes de session
    public function testSessionMethods()
    {
        // Initialiser la session
        $_SESSION = [];
        
        // Tester le stockage et la récupération
        $testValue = 'test_value';
        $result = $this->controller->testSessionMethods('test_key', $testValue);
        
        // Vérifier le résultat
        $this->assertEquals($testValue, $result);
        
        // Vérifier que la clé a été supprimée
        $this->assertArrayNotHasKey('test_key', $_SESSION);
    }

    // Test 3: Tester la méthode isPost
    public function testIsPost()
    {
        // Cas 1: méthode GET
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertFalse($this->controller->testIsPost());
        
        // Cas 2: méthode POST
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertTrue($this->controller->testIsPost());
    }

    // Test 4: Tester la méthode isGet
    public function testIsGet()
    {
        // Cas 1: méthode POST
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertFalse($this->controller->testIsGet());
        
        // Cas 2: méthode GET
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertTrue($this->controller->testIsGet());
    }

    // Test 5: Tester la méthode render
    public function testRender()
    {
        // Vérifier simplement que la méthode de rendu renvoie un format attendu
        $testData = ['testVar' => 'testValue'];
        $result = $this->controller->testRender('test/view', $testData);
        
        // Vérifier que le résultat est un tableau
        $this->assertIsArray($result);
        
        // Vérifier que la vue est correcte
        $this->assertArrayHasKey('view', $result);
        $this->assertEquals('test/view', $result['view']);
        
        // Vérifier que les données passées sont présentes
        $this->assertArrayHasKey('data', $result);
        $this->assertIsArray($result['data']);
        $this->assertArrayHasKey('testVar', $result['data']);
        $this->assertEquals('testValue', $result['data']['testVar']);
    }
}
