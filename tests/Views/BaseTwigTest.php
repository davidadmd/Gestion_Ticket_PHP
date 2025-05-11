<?php

namespace Tests\Views;

use PHPUnit\Framework\TestCase;

/**
 * Test simple pour le template base.twig
 */
class BaseTwigTest extends TestCase
{
    private $templateContent;
    
    protected function setUp(): void
    {
        parent::setUp();
        // Lire le contenu du template au lieu d'essayer de le rendre avec Twig
        $this->templateContent = file_get_contents(__DIR__ . '/../../views/layout/base.twig');
    }
    
    /**
     * Test la structure de base du template
     */
    public function testBaseTwigStructure()
    {
        // Vérifier les éléments HTML de base
        $this->assertStringContainsString('<!DOCTYPE html>', $this->templateContent);
        $this->assertStringContainsString('<html lang="fr">', $this->templateContent);
        $this->assertStringContainsString('<head>', $this->templateContent);
        $this->assertStringContainsString('<body>', $this->templateContent);
        $this->assertStringContainsString('</html>', $this->templateContent);
        
        // Vérifier le titre
        $this->assertStringContainsString('<title>{% block title %}Gestion des Tickets{% endblock %}</title>', $this->templateContent);
        
        // Vérifier les liens CSS
        $this->assertStringContainsString('bootstrap', $this->templateContent);
        $this->assertStringContainsString('font-awesome', $this->templateContent);
        
        // Vérifier les éléments de navigation
        $this->assertStringContainsString('<nav class="navbar', $this->templateContent);
        $this->assertStringContainsString('Gestion des Tickets', $this->templateContent);
        
        // Vérifier les blocs Twig principaux
        $this->assertStringContainsString('{% block content %}{% endblock %}', $this->templateContent);
        $this->assertStringContainsString('{% block javascripts %}{% endblock %}', $this->templateContent);
        
        // Vérifier la partie conditionnelle pour utilisateurs connectés
        $this->assertStringContainsString('{% if session.user_id is defined %}', $this->templateContent);
        $this->assertStringContainsString('Déconnexion', $this->templateContent);
    }
    
    /**
     * Test les conditions pour différents rôles d'utilisateurs
     */
    public function testUserRoleConditions()
    {
        // Vérifier les conditions pour administrateurs
        $this->assertStringContainsString('{% if session.user_id is defined and is_granted(\'admin\') %}', $this->templateContent);
        $this->assertStringContainsString('Administration', $this->templateContent);
        
        // Vérifier les conditions pour rôles non-utilisateurs (techniciens, admins)
        $this->assertStringContainsString('{% if session.role != \'0\' %}', $this->templateContent);
        
        // Vérifier les conditions pour techniciens
        $this->assertStringContainsString('{% if session.role == \'1\' %}', $this->templateContent);
        $this->assertStringContainsString('Tickets à traiter', $this->templateContent);
    }
}
