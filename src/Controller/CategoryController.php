<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/category')]
class CategoryController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'category_index', methods: ['GET'])]
    #[Template]
    public function index(Request $request, CategoryRepository $categoryRepository) : array {
        $query = $categoryRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'categories' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    #[Route(path: '/search', name: 'category_search', methods: ['GET'])]
    #[Template]
    public function search(Request $request, CategoryRepository $categoryRepository) : array {
        $q = $request->query->get('q');
        if ($q) {
            $query = $categoryRepository->searchQuery($q);
            $categories = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $categories = [];
        }

        return [
            'categories' => $categories,
            'q' => $q,
        ];
    }

    #[Route(path: '/typeahead', name: 'category_typeahead', methods: ['GET'])]
    public function typeahead(Request $request, CategoryRepository $categoryRepository) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];

        foreach ($categoryRepository->typeaheadQuery($q)->execute() as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    #[Route(path: '/new', name: 'category_new', methods: ['GET', 'POST'])]
    #[Template]
    #[IsGranted('ROLE_CONTENT_ADMIN')]
    public function new(EntityManagerInterface $entityManager, Request $request) : array|RedirectResponse {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'The new category has been saved.');

            return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
        }

        return [
            'category' => $category,
            'form' => $form->createView(),
        ];
    }

    #[Route(path: '/{id}', name: 'category_show', methods: ['GET'])]
    #[Template]
    public function show(Request $request, Category $category) : array {
        $items = $this->paginator->paginate($category->getItems(), $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);

        return [
            'category' => $category,
            'items' => $items,
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}/edit', name: 'category_edit', methods: ['GET', 'POST'])]
    #[Template]
    public function edit(EntityManagerInterface $entityManager, Request $request, Category $category) : array|RedirectResponse {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'The updated category has been saved.');

            return $this->redirectToRoute('category_show', ['id' => $category->getId()]);
        }

        return [
            'category' => $category,
            'form' => $form->createView(),
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}', name: 'category_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Category $category) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
            $this->addFlash('success', 'The category has been deleted.');
        }

        return $this->redirectToRoute('category_index');
    }
}
