<?php

namespace App\Controller\BackEnd;

use App\Entity\Category;
use App\Form\Type\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/categories")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("", name="category_index", methods={"GET"})
     */
    public function index(Request $request, CategoryRepository $categoryRepository): Response
    {
        $criteria = [];
        $page = $request->get('page', 1);
        $size = $request->get('size', 20);
        $sort = $request->get('sort', ['name' => 'asc']);
        $pager = $categoryRepository->search($criteria, $sort)->setMaxPerPage($size)->setCurrentPage($page);

        return $this->render('backend/category/index.html.twig', [
            'pager' => $pager,
        ]);
    }

    /**
     * @Route("/create", name="category_create", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $category->getParent()) {
                $category->setLevel($category->getParent()->getLevel() + 1);
            }
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Create category successfully.');
            if ($url = $request->get('redirect_url')) {
                return $this->redirect($url);
            }

            return $this->redirectToRoute('category_create');
        }

        return $this->render('backend/category/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="category_edit", methods={"GET", "PUT"})
     */
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Edit category successfully.');

            if ($url = $request->get('redirect_url')) {
                return $this->redirect($url);
            }
        }

        return $this->render('backend/category/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="category_delete", methods={"DELETE"})
     */
    public function delete(Category $category, EntityManagerInterface $entityManager): Response
    {
        $category->setDeleted(true);
        $entityManager->flush();
        $this->addFlash('success', 'Delete category successfully.');

        return $this->redirectToRoute('category_index');
    }
}
