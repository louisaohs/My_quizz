<?php

namespace App\Controller\Admin;

use App\Repository\AdminRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(AdminRepository $adminRepo): Response
    {
        $admin = $adminRepo->findAll();

        return $this->render('admin/index.html.twig', [
            'admin' => $admin,
        ]);
    }

    // public function createUser(Request $request, ManagerRegistry $entityManager)
    // {
    //     $admin = new Admin();

    //     $form = $this->createForm(AdminType::class, $admin);

    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {

    //         $em = $entityManager->getManager();
    //         $em->persist($admin);
    //         $em->flush();

    //         // return $this->redirectToRoute('');
    //     }

    //     return $this->render('user_admin/index.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }

    // public function store(AdminRepository $adminRepo): Response
    // {
    //     $admin = $adminRepo->findAll();

    //     return $this->render('user_admin/index.html.twig', [
    //         'admin' => $admin,
    //     ]);
    // }

    // public function show(int $id, AdminRepository $adminRepo): Response
    // {
    //     $admin = $adminRepo->find($id);

    //     if (!$admin) {
    //         throw $this->createNotFoundException('Cet admin n\'existe pas.');
    //     }

    //     return $this->render('user_admin/index.html.twig', [
    //         'question' => $admin,
    //     ]);
    // }

    // public function edit(int $id, Request $request, ManagerRegistry $entityManager, AdminRepository $adminRepo)
    // {
    //     $admin = $adminRepo->find($id);

    //     if (!$admin) {
    //         throw $this->createNotFoundException('Question non trouvée');
    //     }

    //     $form = $this->createForm(AdminType::class, $admin);

    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {

    //         $em = $entityManager->getManager();
    //         $em->persist($admin);
    //         $em->flush();

    //         return $this->redirectToRoute('show', ['id' => $admin->getId()]);
    //     }

    //     return $this->render('user_admin/index.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }

    // public function deleteCategory(Categorie $categories, EntityManagerInterface $entityManager): Response
    // {
    //     $entityManager->remove($categories);
    //     $entityManager->flush();

    //     $categories = $entityManager->getRepository(Categorie::class)->findAll();

    //     return $this->render('user_admin/index.html.twig', [
    //         'categories' => $categories
    //     ]);
    // }

    // public function deleteQuizz(Quizz $quizz, EntityManagerInterface $entityManager)
    // {

    //     $entityManager->remove($quizz);
    //     $entityManager->flush();

    //     // return $this->redirectToRoute('admin_quizz');
    // }
}
