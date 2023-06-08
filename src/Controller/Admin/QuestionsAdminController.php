<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Entity\Question;
use App\Form\CategorieType;
use App\Form\QuestionType;
use App\Repository\CategorieRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionsAdminController extends AbstractController
{
    #[Route('/admin/questions', name: 'questions_admin')]
    public function index(QuestionRepository $questionsRepo): Response
    {
        return $this->render('admin/questions_index.html.twig', [
            'questions' => $questionsRepo->findAll()
        ]);
    }

    #[Route('/admin/questions/ajout', name: 'questions_ajout')]
    public function ajoutQuestions(Request $request, ManagerRegistry $entityManager)
    {
        $ajoutQuestions = new Question();

        $form = $this->createForm(QuestionType::class, $ajoutQuestions);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $entityManager->getManager();
            $em->persist($ajoutQuestions);
            $em->flush();

            return $this->redirectToRoute('questions_admin');
        }

        return $this->render('admin/questions_ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function editQuestions(int $id, Request $request, ManagerRegistry $entityManager, QuestionRepository $repository)
    {
        $questions = $repository->find($id);

        if (!$questions) {
            throw $this->createNotFoundException('Question non-trouvée');
        }

        $form = $this->createForm(QuestionType::class, $questions);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $entityManager->getManager();
            $em->persist($questions);
            $em->flush();

            return $this->redirectToRoute('questions_admin', ['id' => $questions->getId()]);
        }

        return $this->render('admin/questions_edit.html.twig', [
            'form' => $form->createView(),
            'questions' => $questions
        ]);
    }

    public function deleteQuestions(int $id, EntityManagerInterface $entityManager, QuestionRepository $repository)
    {
        $questions = $repository->find($id);

        if (!$questions) {
            throw $this->createNotFoundException('Question non-trouvée');
        }

        $entityManager->remove($questions);
        $entityManager->flush();

        return $this->redirectToRoute('questions_admin');
    }
}
