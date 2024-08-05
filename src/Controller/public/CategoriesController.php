<?php

namespace App\Controller\public;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

class CategoriesController extends AbstractController
{

    #[Route('/categories', name: 'ListCategories')]
    public function ListCategories (CategoryRepository $categoryRepository)
    {

        $categories = $categoryRepository->findAll();


        return $this->render('public/page/ListCategories.html.twig', [ 'categories' => $categories]);
    }

    #[Route('/categories/show/{id}', name: 'ArticlesInCategories')]
    public function ArticlesInCategories (CategoryRepository $categoryRepository, ArticleRepository $articleRepository, $id)
    {
        $category = $categoryRepository->find($id);
        $articles = $articleRepository->findBy(['category' => $category]);

        return $this->render('public/page/CategoryById.html.twig', [ 'category' => $category , 'articles' => $articles ]);
    }

}