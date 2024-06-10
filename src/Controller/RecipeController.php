<?php

namespace App\Controller;

use App\Entity\Recipe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/recipes', name: 'recipe.')]
class RecipeController extends AbstractController
{
    #[Route('/{id}', name: 'show', requirements: ['id' => '[0-9]+'])]
    public function show(Recipe $recipe): Response
    {
        return $this->render('recipes/show.html.twig', [
            'recipe' => $recipe
        ]);
    }
}
