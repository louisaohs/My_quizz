<?php

namespace App\Controller;

use App\Entity\Reponse;
use App\Form\ReponseType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReponseController extends AbstractController
{
    // #[Route('/reponse/{value}', name: 'reponse')]

    public function getReponses(ManagerRegistry $entityManager, $reponse): Response
    {

        // $reponses = new Reponse();

        // $formulaire = $this->createForm(ReponseType::class, $reponses);

        // $formulaire->handleRequest($request);

        // if ($formulaire->isSubmitted() && $formulaire->isValid()) {
        //     // Traitez les données du formulaire
        //     $em = $entityManager->getManager();
        //     $em->persist($reponses);
        //     $em->flush();
        //     // ...
        // }

        return $this->render('reponse/get_reponse.html.twig', [
            'reponse' => $reponse,
            // $formulaireView = $formulaire->createView();
        ]);
    }
}
