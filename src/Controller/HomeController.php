<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(RecipeRepository $repository): Response
    {
        return $this->render('home/index.html.twig', [
            'recipes' => $repository->findLatestRecipes(),
        ]);
    }
}
