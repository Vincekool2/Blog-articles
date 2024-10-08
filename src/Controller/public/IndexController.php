<?php

declare(strict_types=1);

// on créé un namespace
// c'est à dire un chemin pour identifier la classe
// actuelle
namespace App\Controller\public;

// on appelle le namespace des classes qu'on utilise
// pour que symfony fasse le require de ces classes
use App\Repository\ArticleRepository;
use App\Repository\CardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// on étend la classe AbstractController
// qui permet d'utiliser des fonctions
// utilitaires pour les controllers (twig etc)
class IndexController extends AbstractController
{



    #[Route('/', name: 'home')]
    public function index (ArticleRepository $articleRepository, CardRepository $cardRepository)
    {


        return $this->render('public/page/Home.html.twig'   );

    }
}