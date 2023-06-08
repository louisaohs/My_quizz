<?php

namespace App\Controller;

use App\Entity\Categorie;
use Doctrine\ORM\Query;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Form\CategorieType;
use App\Form\QuestionType;
use App\Form\ReponseType;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\EntityManager;
use App\Repository\QuestionRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\Cast\String_;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Question\Question as QuestionQuestion;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class CategorieController extends AbstractController
{
    #[Route('/categories', name: 'categories')]
    public function index(CategorieRepository $repository): Response
    {
        // nos catégories :
        $categories = $repository->findAll();

        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categorie/{id}', name: 'categorie')]
    public function getCategories(EntityManagerInterface $manager, Request $request, $id)
    {
        $session = $request->getSession();
        $counter = $session->has('counter') ? (int) $session->get('counter') : 0;

        //Score de l'utlisateur (bon ou mauvais)
        $goodScore = $session->get('goodScore');
        $badScore = $session->get('badScore');

        // paramètre passer dans la vue
        $scope = [];

        // vu passer en 1er paramètre
        $vue = "";

        $questionRepository = $manager->getRepository(Question::class);
        $reponseRepository = $manager->getRepository(Reponse::class);
        $categorieRepository = $manager->getRepository(Categorie::class);

        // récupere l'id de la categorie passer dans les paramètres d'uri
        $questions = $questionRepository->findBy(['id_categorie' => $id]);

        // récupere les informations de la catégorie
        $categories = $categorieRepository->findBy(['id' => $id]);

        $entityReponse = new Reponse();

        $form = $this->createFormBuilder($entityReponse)
            ->add('id_question', HiddenType::class)
            ->add('reponse', null, [
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('reponse_expected', HiddenType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $manager->flush();

            //récupere la réponse
            $checkReponseIsGood = $reponseRepository->findBy(['reponse' => $entityReponse->getReponse()]);

            if ($session->get('counter') !== count($questions) - 1) {
                if (count($checkReponseIsGood) !== 0 && $checkReponseIsGood[0]->getReponseExpected() === 1) {
                    dump('bonne réponse');
                    $counter++;

                    $goodScore++;

                    $session->set('goodScore', $goodScore);
                } else {
                    dump('mauvaise réponse');
                    $counter++;

                    $badScore++;

                    $session->set('badScore', $badScore);
                }
                $session->set('counter', $counter);

                //récupère l'id de la question
                $idDeLaQuestion = $questions[$counter]->getId();

                //récuperes les réponses de la question actuel
                $reponses = $reponseRepository->findBy(['id_question' => $idDeLaQuestion]);

                $vue = 'categorie/get_categorie.html.twig';
                $scope = [
                    'questions' => $questions,
                    'reponses' => $reponses,
                    'formCategorie' => $form->createView(),
                    'questionIncrémenter' => $counter,
                ];
            } else {

                $vue = 'reponse/index.html.twig';
                $scope = [
                    'questions' => $questions,
                    'score' => [
                        'goodAnswer' => $goodScore,
                        'badAnwser' => $badScore
                    ],
                    'categorie' => $categories
                ];
                $session->clear();
            }
        } else {

            // récupère l'id de la question
            $idDeLaQuestion = $questions[0]->getId();

            // récupere les réponses de la question actuel
            $reponses = $reponseRepository->findBy(['id_question' => $idDeLaQuestion]);

            $vue = 'categorie/get_categorie.html.twig';
            $scope = [
                'questions' => $questions,
                'reponses' => $reponses,
                'formCategorie' => $form->createView(),
                'questionIncrémenter' => $counter,
            ];
        }
        return $this->render($vue, $scope);
    }
}
