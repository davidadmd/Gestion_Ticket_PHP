<?php

namespace Controllers;

use PDO;

class HomeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        echo $this->render('home', [
            'title' => "Page d'accueil",
            'active_page' => 'home'
        ]);
    }
}
