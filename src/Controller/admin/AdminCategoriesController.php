<?php

namespace App\Controller\admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Généré automatiquement par le make crud
#[Route('/admin/categories')]
class AdminCategoriesController extends AbstractController
{
    #[Route('/', name: 'admin_list_categories', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/page/AdminListCategories.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }
    #[Route('/AdminCategories/show/{id}', name: 'AdminArticlesInCategories')]
    public function ArticlesInCategories (CategoryRepository $categoryRepository, ArticleRepository $articleRepository, $id)
    {
        $category = $categoryRepository->find($id);
        $articles = $articleRepository->findBy(['category' => $category]);

        return $this->render('admin/page/AdminCategoryById.html.twig', [ 'category' => $category , 'articles' => $articles ]);
    }

    #[Route('/insert', name: 'admin_insert_category', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('admin_list_categories', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/page/insert_category.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_update_category', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        // Quand on demande à Symfony d'instancier une entité en parametre
        // d'un controleur et qu'on a un id en parametre de la route
        // symfonty va automatiquement essayer de récupérer un enregistrement
        // dans la table reliée, correspondant à l'id (equivalent au categoryRepository->find($id)
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_list_categories', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('admin/page/CategoryUpdate.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_delete_category')]
    public function deleteCategory(int $id, EntityManagerInterface $entityManager , categoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            $html = $this->renderView('admin/404.html.twig');
            return new Response($html, 404);
        }

        try {
            $entityManager->remove($category);
            $entityManager->flush();


            $this->addFlash('success', 'category has been deleted !');

        } catch(\Exception $exception){
            return $this->$this->   renderView('admin/error.html.twig', ['errorMessage' => $exception->getMessage()]);
        }


        return $this->redirectToRoute('admin_list_categories', [], Response::HTTP_SEE_OTHER);
    }
}