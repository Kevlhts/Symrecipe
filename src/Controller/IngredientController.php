<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
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
    #[Route('/ingredient', name: 'ingredient', methods: ['GET'])]
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
}
