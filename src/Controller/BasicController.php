<?php

namespace App\Controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class BasicController
{
    private $loader;
    protected $twig;

    public function __construct()
    {
//        try {
//            // Chargement du répertoire des templates
//            $this->loader = new FilesystemLoader(ROOT.'/templates');
//
//            // Initialisation de Twig
//            $this->twig = new Environment($this->loader, [
//                'cache' => false,  // Désactive le cache pour le développement
//            ]);
//        } catch (\Exception $e) {
//            // Affiche l'erreur si Twig échoue à se charger
//            dump($e->getMessage());
//        }
        $this->loader = new FilesystemLoader(ROOT.'/templates');
        $this->twig = new Environment($this->loader, [
            'cache' => false,
        ]);

        $this->twig->addFunction(new TwigFunction('asset', function ($path) {
            return "/" . ltrim($path, '/');
        }));
    }
}
