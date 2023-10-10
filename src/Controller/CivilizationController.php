<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Civilization;
use App\Form\CivilizationType;
use App\Repository\CivilizationRepository;
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

#[Route(path: '/civilization')]
class CivilizationController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'civilization_index', methods: ['GET'])]
    #[Template]
    public function index(Request $request, CivilizationRepository $civilizationRepository) : array {
        $query = $civilizationRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'civilizations' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    #[Route(path: '/search', name: 'civilization_search', methods: ['GET'])]
    #[Template]
    public function search(Request $request, CivilizationRepository $civilizationRepository) : array {
        $q = $request->query->get('q');
        if ($q) {
            $query = $civilizationRepository->searchQuery($q);
            $civilizations = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $civilizations = [];
        }

        return [
            'civilizations' => $civilizations,
            'q' => $q,
        ];
    }

    #[Route(path: '/typeahead', name: 'civilization_typeahead', methods: ['GET'])]
    public function typeahead(Request $request, CivilizationRepository $civilizationRepository) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];

        foreach ($civilizationRepository->typeaheadQuery($q)->execute() as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    #[Route(path: '/new', name: 'civilization_new', methods: ['GET', 'POST'])]
    #[Template]
    #[IsGranted('ROLE_CONTENT_ADMIN')]
    public function new(EntityManagerInterface $entityManager, Request $request) : array|RedirectResponse {
        $civilization = new Civilization();
        $form = $this->createForm(CivilizationType::class, $civilization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($civilization);
            $entityManager->flush();
            $this->addFlash('success', 'The new civilization has been saved.');

            return $this->redirectToRoute('civilization_show', ['id' => $civilization->getId()]);
        }

        return [
            'civilization' => $civilization,
            'form' => $form->createView(),
        ];
    }

    #[Route(path: '/{id}', name: 'civilization_show', methods: ['GET'])]
    #[Template]
    public function show(Request $request, Civilization $civilization) : array {
        $items = $this->paginator->paginate($civilization->getItems(), $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);

        return [
            'civilization' => $civilization,
            'items' => $items,
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}/edit', name: 'civilization_edit', methods: ['GET', 'POST'])]
    #[Template]
    public function edit(EntityManagerInterface $entityManager, Request $request, Civilization $civilization) : array|RedirectResponse {
        $form = $this->createForm(CivilizationType::class, $civilization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'The updated civilization has been saved.');

            return $this->redirectToRoute('civilization_show', ['id' => $civilization->getId()]);
        }

        return [
            'civilization' => $civilization,
            'form' => $form->createView(),
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}', name: 'civilization_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Civilization $civilization) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $civilization->getId(), $request->request->get('_token'))) {
            $entityManager->remove($civilization);
            $entityManager->flush();
            $this->addFlash('success', 'The civilization has been deleted.');
        }

        return $this->redirectToRoute('civilization_index');
    }
}
