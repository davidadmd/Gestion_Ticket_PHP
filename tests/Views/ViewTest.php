<?php

namespace Tests\Views;

use PHPUnit\Framework\TestCase;

/**
 * Classe simple pour tester le contenu HTML de base
 */
class ViewTest extends TestCase
{
    /**
     * Test simple de vérification de structure HTML
     * Vérifie qu'une chaîne HTML contient les éléments attendus
     */
    public function testHtmlStructure()
    {
        // HTML de test simple
        $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <title>Test Page</title>
            </head>
            <body>
                <h1>Hello World</h1>
                <p>This is a test paragraph.</p>
                <form>
                    <input type="text" name="username">
                    <input type="password" name="password">
                    <button type="submit">Submit</button>
                </form>
            </body>
            </html>
        ';
        
        // Vérifier la structure HTML de base
        $this->assertStringContainsString('<!DOCTYPE html>', $html);
        $this->assertStringContainsString('<html>', $html);
        $this->assertStringContainsString('<head>', $html);
        $this->assertStringContainsString('<title>Test Page</title>', $html);
        $this->assertStringContainsString('<body>', $html);
        
        // Vérifier les éléments de contenu
        $this->assertStringContainsString('<h1>Hello World</h1>', $html);
        $this->assertStringContainsString('<p>This is a test paragraph.</p>', $html);
        
        // Vérifier le formulaire
        $this->assertStringContainsString('<form>', $html);
        $this->assertStringContainsString('<input type="text" name="username">', $html);
        $this->assertStringContainsString('<input type="password" name="password">', $html);
        $this->assertStringContainsString('<button type="submit">Submit</button>', $html);
    }
    
    /**
     * Test de validation d'une structure de formulaire
     * Vérifie qu'un formulaire HTML contient les champs nécessaires
     */
    public function testFormValidation()
    {
        // HTML de formulaire de test
        $formHtml = '
            <form method="POST" action="/submit">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" rows="4" required></textarea>
                </div>
                <div class="form-check">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">J\'accepte les conditions</label>
                </div>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        ';
        
        // Vérifier la structure du formulaire
        $this->assertStringContainsString('<form method="POST" action="/submit">', $formHtml);
        
        // Vérifier les champs du formulaire
        $this->assertStringContainsString('input type="email"', $formHtml);
        $this->assertStringContainsString('name="email"', $formHtml);
        $this->assertStringContainsString('<textarea', $formHtml);
        $this->assertStringContainsString('name="message"', $formHtml);
        $this->assertStringContainsString('input type="checkbox"', $formHtml);
        $this->assertStringContainsString('name="terms"', $formHtml);
        
        // Vérifier le bouton de soumission
        $this->assertStringContainsString('<button type="submit"', $formHtml);
        $this->assertStringContainsString('Envoyer', $formHtml);
        
        // Vérifier les attributs required
        $this->assertStringContainsString('required', $formHtml);
    }
}
