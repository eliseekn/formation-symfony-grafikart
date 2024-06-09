<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    #[Route('/recipes', name: 'recipe.index')]
    public function index(RecipeRepository $repository): Response
    {
        return $this->render('recipes/index.html.twig', [
            'recipes' => $repository->findAll(),
        ]);
    }

    #[Route('/recipes/{id}', name: 'recipe.show', requirements: ['id' => '[0-9]+'])]
    public function show(Recipe $recipe): Response
    {
        return $this->render('recipes/show.html.twig', [
            'recipe' => $recipe
        ]);
    }

    #[Route('/recipes/{id}/edit', name: 'recipe.edit', requirements: ['id' => '[0-9]+'], methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Recipe updated successfully');
            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId()]);
        }

        return $this->render('recipes/edit.html.twig', [
            'recipe' => $recipe,
            'form'=> $form
        ]);
    }

    #[Route('/recipes/create', name: 'recipe.create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recipe);
            $entityManager->flush();

            $this->addFlash('success', 'Recipe created successfully');
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('recipes/create.html.twig', [
            'form'=> $form
        ]);
    }

    #[Route('/recipes/{id}/delete', name: 'recipe.delete', methods: ['DELETE'])]
    public function delete(Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($recipe);
        $entityManager->flush();

        $this->addFlash('success', 'Recipe deleted successfully');
        return $this->redirectToRoute('recipe.index');
    }
}
