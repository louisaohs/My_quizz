<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesAdminController extends AbstractController
{
    #[Route('/admin/categories', name: 'categories_admin')]
    public function index(CategorieRepository $categoriesRepo): Response
    {
        return $this->render('admin/categories_index.html.twig', [
            'categories' => $categoriesRepo->findAll()
        ]);
    }

    #[Route('/admin/categories/ajout', name: 'categories_ajout')]
    public function ajoutCategories(Request $request, ManagerRegistry $entityManager)
    {
        $ajoutCategories = new Categorie();

        $form = $this->createForm(CategorieType::class, $ajoutCategories);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $entityManager->getManager();
            $em->persist($ajoutCategories);
            $em->flush();

            return $this->redirectToRoute('categories_admin');
        }

        return $this->render('admin/categories_ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function editCategories(int $id, Request $request, ManagerRegistry $entityManager, CategorieRepository $repository)
    {
        $categories = $repository->find($id);

        if (!$categories) {
            throw $this->createNotFoundException('Question non trouvée');
        }

        $form = $this->createForm(CategorieType::class, $categories);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $entityManager->getManager();
            $em->persist($categories);
            $em->flush();

            return $this->redirectToRoute('categories_admin', ['id' => $categories->getId()]);
        }

        return $this->render('admin/categories_edit.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories
        ]);
    }

    public function deleteCategories(int $id, EntityManagerInterface $entityManager, CategorieRepository $repository)
    {
        $categories = $repository->find($id);

        if (!$categories) {
            throw $this->createNotFoundException('Catégorie non-trouvée');
        }

        $entityManager->remove($categories);
        $entityManager->flush();

        return $this->redirectToRoute('categories_admin');
    }
}
