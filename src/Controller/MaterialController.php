<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Material;
use App\Form\MaterialType;
use App\Repository\MaterialRepository;
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

#[Route(path: '/material')]
class MaterialController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'material_index', methods: ['GET'])]
    #[Template]
    public function index(Request $request, MaterialRepository $materialRepository) : array {
        $query = $materialRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'materials' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    #[Route(path: '/search', name: 'material_search', methods: ['GET'])]
    #[Template]
    public function search(Request $request, MaterialRepository $materialRepository) : array {
        $q = $request->query->get('q');
        if ($q) {
            $query = $materialRepository->searchQuery($q);
            $materials = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $materials = [];
        }

        return [
            'materials' => $materials,
            'q' => $q,
        ];
    }

    #[Route(path: '/typeahead', name: 'material_typeahead', methods: ['GET'])]
    public function typeahead(Request $request, MaterialRepository $materialRepository) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];

        foreach ($materialRepository->typeaheadQuery($q)->execute() as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    #[Route(path: '/new', name: 'material_new', methods: ['GET', 'POST'])]
    #[Template]
    #[IsGranted('ROLE_CONTENT_ADMIN')]
    public function new(EntityManagerInterface $entityManager, Request $request) : array|RedirectResponse {
        $material = new Material();
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($material);
            $entityManager->flush();
            $this->addFlash('success', 'The new material has been saved.');

            return $this->redirectToRoute('material_show', ['id' => $material->getId()]);
        }

        return [
            'material' => $material,
            'form' => $form->createView(),
        ];
    }

    #[Route(path: '/{id}', name: 'material_show', methods: ['GET'])]
    #[Template]
    public function show(Material $material) : array {
        return [
            'material' => $material,
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}/edit', name: 'material_edit', methods: ['GET', 'POST'])]
    #[Template]
    public function edit(EntityManagerInterface $entityManager, Request $request, Material $material) : array|RedirectResponse {
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'The updated material has been saved.');

            return $this->redirectToRoute('material_show', ['id' => $material->getId()]);
        }

        return [
            'material' => $material,
            'form' => $form->createView(),
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}', name: 'material_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Material $material) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $material->getId(), $request->request->get('_token'))) {
            $entityManager->remove($material);
            $entityManager->flush();
            $this->addFlash('success', 'The material has been deleted.');
        }

        return $this->redirectToRoute('material_index');
    }
}
