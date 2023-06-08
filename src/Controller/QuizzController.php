<?php

namespace App\Controller;

use App\Entity\Quizz;
use App\Form\QuizzType;
use App\Repository\QuizzRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuizzController extends AbstractController
{
    #[Route('/question', name: 'app_question')]
    public function index(QuizzRepository $repository): Response
    {
        $questions = $repository->findAll();

        return $this->render('question/index.html.twig', [
            'questions' => $questions,
        ]);
    }
    // ------------------------------------------------------
    public function create(Request $request,  ManagerRegistry $entityManager): Response
    {
        $questions = new Quizz();

        $form = $this->createForm(QuizzType::class, $questions);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $entityManager->getManager();
            $em->persist($questions);
            $em->flush();

            return $this->redirectToRoute('app_question');
        }

        return $this->render('question/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    // ------------------------------------------------------
    public function store(QuizzRepository $repository): Response
    {
        $questions = $repository->findAll();

        return $this->render('question/index.html.twig', [
            'questions' => $questions,
        ]);
    }
    // ------------------------------------------------------
    public function show(int $id, QuizzRepository $repository): Response
    {
        $question = $repository->find($id);

        if (!$question) {
            throw $this->createNotFoundException('Question non trouvée');
        }

        return $this->render('question/show.html.twig', [
            'question' => $question,
        ]);
    }
    // ------------------------------------------------------
    public function edit(int $id, Request $request, ManagerRegistry $entityManager, QuizzRepository $repository)
    {
        $question = $repository->find($id);

        if (!$question) {
            throw $this->createNotFoundException('Question non trouvée');
        }

        $form = $this->createForm(QuizzType::class, $question);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $entityManager->getManager();
            $em->persist($question);
            $em->flush();

            return $this->redirectToRoute('show', ['id' => $question->getId()]);
        }

        return $this->render('question/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    // ------------------------------------------------------
    public function update()
    {
    }
    // ------------------------------------------------------
    public function delete(int $id, EntityManagerInterface $entityManager, QuizzRepository $repository)
    {
        $question = $repository->find($id);

        if (!$question) {
            throw $this->createNotFoundException('Question non trouvée');
        }

        $entityManager->remove($question);
        $entityManager->flush();

        return $this->redirectToRoute('app_question');
    }
}
