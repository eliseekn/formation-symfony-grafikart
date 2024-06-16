<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/recipes', name: 'admin.recipe.')]
class RecipeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(RecipeRepository $repository): Response
    {
        return $this->render('/admin/recipes/index.html.twig', [
            'recipes' => $repository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '[0-9]+'])]
    public function show(Recipe $recipe): Response
    {
        return $this->render('/admin/recipes/show.html.twig', [
            'recipe' => $recipe
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '[0-9]+'], methods: ['GET', 'POST'])]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            unlink($this->getParameter('kernel.project_dir') . '/public/images/' . $recipe->getImage());
            $recipe->setImage($this->uploadFile($form->get('file')->getData()));
            $entityManager->flush();

            $this->addFlash('success', 'Recipe updated successfully');
            return $this->redirectToRoute('admin.recipe.show', ['id' => $recipe->getId()]);
        }

        return $this->render('/admin/recipes/edit.html.twig', [
            'recipe' => $recipe,
            'form'=> $form
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setImage($this->uploadFile($form->get('file')->getData()));

            $entityManager->persist($recipe);
            $entityManager->flush();

            $this->addFlash('success', 'Recipe created successfully');
            return $this->redirectToRoute('admin.recipe.index');
        }

        return $this->render('/admin/recipes/create.html.twig', [
            'form'=> $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(Recipe $recipe, EntityManagerInterface $entityManager): Response
    {
        unlink($this->getParameter('kernel.project_dir') . '/public/images/' . $recipe->getImage());
        $entityManager->remove($recipe);
        $entityManager->flush();

        $this->addFlash('success', 'Recipe deleted successfully');
        return $this->redirectToRoute('admin.recipe.index');
    }

    private function  uploadFile(UploadedFile $file): string
    {
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $file->guessExtension();
        $file->move($this->getParameter('kernel.project_dir') . '/public/images', $filename);
        return $filename;
    }
}
