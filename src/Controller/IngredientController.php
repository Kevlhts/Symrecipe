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
     * This function display all ingredients
     */
    #[Route('/ingredient', name: 'ingredient_route', methods: ['GET'])]
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
            'ingredients'=>$ingredients
        ]);
    }
    #[Route('/ingredient/nouveau','ingredient.new', methods: ['GET','POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager
    ) : Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class,$ingredient);

        // process le formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()){

            $ingredient = $form->getData();
            $manager->persist($ingredient);// comme commit
            $manager->flush();// comme push

            $this->addFlash(
                'success', //match avec bootstrap 'succes"
                'Ton ingrédient est bien enregistré'
            );


            $this->redirectToRoute('ingredient_route');
        }
        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView() // creation du formulaire dans la vue
        ]);
    }
}
