<?php

namespace App\Controller\admin;

use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

class AdminCategoriesController extends AbstractController
{

    #[Route('/AdminCategories', name: 'AdminListCategories')]
    public function ListCategories (CategoryRepository $categoryRepository)
    {

        $categories = $categoryRepository->findAll();


        return $this->render('admin/page/AdminListCategories.html.twig', [ 'categories' => $categories]);
    }

    #[Route('/AdminCategories/show/{id}', name: 'AdminArticlesInCategories')]
    public function ArticlesInCategories (CategoryRepository $categoryRepository, ArticleRepository $articleRepository, $id)
    {
        $category = $categoryRepository->find($id);
        $articles = $articleRepository->findBy(['category' => $category]);

        return $this->render('admin/page/AdminCategoryById.html.twig', [ 'category' => $category , 'articles' => $articles ]);
    }

}