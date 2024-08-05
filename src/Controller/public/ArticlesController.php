<?php

namespace App\Controller\public;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

class ArticlesController extends AbstractController
{
    #[Route('/articles', name: 'articles')]
    public function Articles(ArticleRepository $articleRepository)
    {
        // récupèrer tous les articles en BDD
        $article = $articleRepository->findAll();


        return $this->render('public/page/List_articles.html.twig', ['articles' => $article]);
    }



    #[Route('/articles/show/{id}', name: 'articlesById')]
    public function ArticlesById (ArticleRepository $articleRepository, int $id): Response
    {

        $article = $articleRepository->find($id);


        return $this->render('public/page/Article_by_id.html.twig', ['article' => $article]);
    }





}
