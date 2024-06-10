<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/categories', name: 'admin.category.')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $repository): Response
    {
        return $this->render('/admin/category/index.html.twig', [
            'categories' => $repository->findAll()
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '[0-9]+'], methods: ['GET', 'POST'])]
    public function edit(Category $category, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Category updated successfully');
            return $this->redirectToRoute('admin.category.index', ['id' => $category->getId()]);
        }

        return $this->render('/admin/category/edit.html.twig', [
            'category' => $category,
            'form'=> $form
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category created successfully');
            return $this->redirectToRoute('admin.category.index');
        }

        return $this->render('/admin/category/create.html.twig', [
            'form'=> $form
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(Category $category, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'Category deleted successfully');
        return $this->redirectToRoute('admin.category.index');
    }
}
