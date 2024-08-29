<?php

namespace App\Controller\admin;

use App\Entity\Card;
use App\Form\CardType;
use App\Repository\CardRepository;
use App\Service\UniqueFilenameGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminCardController extends AbstractController
{



    #[Route('/admin/cards', name: 'adminListCards')]
    public function listCards (CardRepository $cardRepository)
    {
        // récupèrer tous les cartes dans la BDD
        $card = $cardRepository->findAll();


        return $this->render('admin/page/admin_list_cards.html.twig', ['cards' => $card]);
    }

    #[Route('/admin/deleteCards', name: 'deleteCard')]
    public function deleteCards (CardRepository $cardRepository)
    {
        // récupèrer tous les cartes dans la BDD
        $card = $cardRepository->findAll();


        return $this->render('admin/page/admin_list_cards.html.twig');
    }
    #[Route('/cards/update/{id}', 'admin_update_card.html.twig')]
    public function updateCards(int $id, Request $request, EntityManagerInterface $entityManager, CardRepository $cardRepository,ParameterBagInterface $params, SluggerInterface $slugger, UniqueFilenameGenerator $uniqueFilenameGenerator)
    {
        $card = $cardRepository->find($id);

        $cardForm = $this->createForm(CardType::class, $card);

        $cardForm->handleRequest($request);

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

            $this->addFlash('success', 'carte modifié');
        }

        $articleCreateFormView = $cardForm->createView();

        return $this->render('admin/page/admin_update_card.html.twig', ['cardForm' => $cardForm->createView()]);

    }
}