<?php

namespace App\Controller\admin;


use App\Entity\Article;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AdminArticlesController extends AbstractController
{
    #[Route('/admin/articles', name: 'AdminArticles')]
    public function Articles(ArticleRepository $articleRepository)
    {
        // récupèrer tous les articles en BDD
        $article = $articleRepository->findAll();


        return $this->render('admin/page/AdminList_articles.html.twig', ['articles' => $article]);
    }



    #[Route('/admin/articles/show/{id}', name: 'AdminArticlesById')]
    public function ArticlesById (ArticleRepository $articleRepository, int $id): Response
    {

        $article = $articleRepository->find($id);


        return $this->render('admin/page/AdminArticle_by_id.html.twig', ['article' => $article]);
    }



    #[Route('/admin/articles/delete/{id}', name: 'delete_article')]
    public function deleteArticle(int $id, ArticleRepository $articleRepository, EntityManagerInterface $entityManager)
    {
        $article = $articleRepository->find($id);

        if (!$article) {
            $html = $this->renderView('admin/404.html.twig');
            return new Response($html, 404);
        }

        try {
            $entityManager->remove($article);
            $entityManager->flush();


            $this->addFlash('success', 'Article bien supprimé !');

        } catch(\Exception $exception){
            return $this->$this->   renderView('admin/error.html.twig', ['errorMessage' => $exception->getMessage()]);
        }

        return $this->redirectToRoute('AdminArticles');
    }

        #[Route('/admin/articles/insert', 'admin_insert_article')]
    public function insertArticle (Request $request, EntityManagerInterface $entityManager ,SluggerInterface $slugger, ParameterBagInterface $params)
        {
            $article = new Article();

            $articleCreateForm = $this->createForm(ArticleType::class, $article);

            $articleCreateForm->handleRequest($request);


            if ($articleCreateForm->isSubmitted() && $articleCreateForm->isValid()) {

                $imageFile = $articleCreateForm->get('image')->getData();

                // si il y a bien un fichier envoyé
                if ($imageFile) {
                    // je récupère son nom
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // je nettoie le nom (sort les caractères spéciaux etc)
                    $safeFilename = $slugger->slug($originalFilename);
                    // je rajoute un identifiant unique au nom
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                    try {
                        // je récupère le chemin de la racine du projet
                        $rootPath = $params->get('kernel.project_dir');
                        // je déplace le fichier dans le dossier /public/upload en partant de la racine
                        // du projet, et je renomme le fichier avec le nouveau nom (slugifié et identifiant unique)
                        $imageFile->move($rootPath . '/public/uploads', $newFilename);
                    } catch (FileException $e) {
                        dd($e->getMessage());
                    }
                    // je stocke dans la propriété image
                    // de l'entité article le nom du fichier
                    $article->setImage($newFilename);
                }

                    $entityManager->persist($article);
                    $entityManager->flush();

                    $this->addFlash('success', 'article enregistré');

                }

                $articleCreateFormView = $articleCreateForm->createView();

                return $this->render('admin/page/insert_article.html.twig', ['articleForm' => $articleCreateFormView]);
            }

        #[Route('/admin/articles/update/{id}', 'admin_update_article')]
    public function updateArticle (Request $request, EntityManagerInterface $entityManager, ArticleRepository $articleRepository, int $id,)
    {
        $article = $articleRepository->find($id);

        $articleCreateForm = $this->createForm(ArticleType::class, $article);

        $articleCreateForm->handleRequest($request);

        if ($articleCreateForm->isSubmitted() && $articleCreateForm->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();


            $this->addFlash('success', 'article modifié');
            return $this->redirectToRoute('AdminArticles');


        }

        $articleCreateFormView = $articleCreateForm->createView();

        return $this->render('admin/page/ArticleUpdate.html.twig', [
            'articleForm' => $articleCreateFormView
        ]);


    }


}
