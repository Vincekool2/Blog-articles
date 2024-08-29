<?php

declare(strict_types=1);

namespace App\Controller\public;

use App\Controller\FileException;
use App\Entity\Card;
use App\Form\CardType;
use App\Repository\CardRepository;
use App\Service\UniqueFilenameGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class CardController extends AbstractController
{




    #[Route('/insert', name: 'insert_card', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CardRepository $cardRepository ,ParameterBagInterface $params, SluggerInterface $slugger, UniqueFilenameGenerator $uniqueFilenameGenerator)
    {
        $card = new Card();

            // J'instancie le gabarit du formulaire et le lie à l'entité
            $cardForm = $this->createForm(CardType::class, $card);

            // Lien entre le formulaire et la requête
            $cardForm->handleRequest($request);


            // Verifie si le formulaire a été envoyé et que ces données sont correctes
            if ($cardForm->isSubmitted() && $cardForm->isValid()) {

                $imageFile = $cardForm->get('imagecard')->getData();

                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);

                    $extension = $imageFile->guessExtension();

                    // j'ai créé une classe "de service"
                    // qui genere un nom unique pour un fichier
                    $newFilename = $uniqueFilenameGenerator->generateUniqueFilename($safeFilename, $extension);

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
                    $card->setImagecard($newFilename);


                }


                $entityManager->persist($card);
                $entityManager->flush();

                $this->addFlash('success', 'article enregistré');

                return $this->redirectToRoute('insert_card');
            }

            return $this->render('public/page/insert_card.html.twig', ['cardForm' => $cardForm->createView()
            ]);

    }

    #[Route('/', name: 'card_search')]
    public function searchCard(Request $request, CardRepository $cardRepository ): Response
    {
        $searchCard = [];
        if ($request->request->has('title')) {
            $titleCard = $request->request->get('title');
            $searchCard = $cardRepository->findOneLike($titleCard);

            if (count($searchCard) === 0) {
                $html = $this->renderView('admin/404.html.twig');
                return new Response($html, 404);
            }

        }
        return $this->render('public/page/home.html.twig', ['card' => $searchCard]);

    }





}