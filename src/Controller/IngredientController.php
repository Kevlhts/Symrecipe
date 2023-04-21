<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    /**
     *  Controller : display all ingredients
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    //repository = récupération de donnée
        // Injection de dépendance : on injecte un service dans les paramètre de la fonction
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/ingredient/index.html.twig', [
            //passer une variable ingredient à la vue:
            'ingredients' => $ingredients
        ]);
    }

    /**
     * Controller : Create an ingredient
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/nouveau', 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(
        Request                $request,
        EntityManagerInterface $manager
    ): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        // process le formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $ingredient = $form->getData();
            $manager->persist($ingredient);// comme commit
            $manager->flush();// comme push

            $this->addFlash(
                'success', //match avec bootstrap 'success"
                'Ton ingrédient est bien enregistré'
            );


            return $this->redirectToRoute('ingredient.index');
        }
        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView() // creation du formulaire dans la vue
        ]);

    }

    /**
     * Controller: edit ingredient
     * @param Ingredient $ingredient
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/edition/{id}/', 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(
        Ingredient             $ingredient,
        Request                $request,
        EntityManagerInterface $manager
    ): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec succès ! '
            );

            return $this->redirectToRoute('ingredient.index');
        }
            return $this->render('pages/ingredient/edit.html.twig', [
                'form' => $form->createView()
            ]);
        }

        #[Route('/ingredient/suppression/{id}/','ingredient.delete', methods: ['GET'])]
        public function delete(
            Ingredient $ingredient,
            EntityManagerInterface $entityManager,
        ) : Response {

        if(!$ingredient) {
            $this->addFlash(
                'success',
                'l\'ingredient en question n\'a pas été trouvé ! '
            );
        }

        $entityManager->remove($ingredient);
        $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient a été supprimé avec succès ! '
            );

        return $this->redirectToRoute('ingredient.index');
        }

}
