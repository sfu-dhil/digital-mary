<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Technique;
use App\Form\TechniqueType;
use App\Repository\TechniqueRepository;
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

#[Route(path: '/technique')]
class TechniqueController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'technique_index', methods: ['GET'])]
    #[Template]
    public function index(Request $request, TechniqueRepository $techniqueRepository) : array {
        $query = $techniqueRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'techniques' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    #[Route(path: '/search', name: 'technique_search', methods: ['GET'])]
    #[Template]
    public function search(Request $request, TechniqueRepository $techniqueRepository) : array {
        $q = $request->query->get('q');
        if ($q) {
            $query = $techniqueRepository->searchQuery($q);
            $techniques = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $techniques = [];
        }

        return [
            'techniques' => $techniques,
            'q' => $q,
        ];
    }

    #[Route(path: '/typeahead', name: 'technique_typeahead', methods: ['GET'])]
    public function typeahead(Request $request, TechniqueRepository $techniqueRepository) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];

        foreach ($techniqueRepository->typeaheadQuery($q)->execute() as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    #[Route(path: '/new', name: 'technique_new', methods: ['GET', 'POST'])]
    #[Template]
    #[IsGranted('ROLE_CONTENT_ADMIN')]
    public function new(EntityManagerInterface $entityManager, Request $request) : array|RedirectResponse {
        $technique = new Technique();
        $form = $this->createForm(TechniqueType::class, $technique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($technique);
            $entityManager->flush();
            $this->addFlash('success', 'The new technique has been saved.');

            return $this->redirectToRoute('technique_show', ['id' => $technique->getId()]);
        }

        return [
            'technique' => $technique,
            'form' => $form->createView(),
        ];
    }

    #[Route(path: '/{id}', name: 'technique_show', methods: ['GET'])]
    #[Template]
    public function show(Request $request, Technique $technique) : array {
        $items = $this->paginator->paginate($technique->getItems(), $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);

        return [
            'technique' => $technique,
            'items' => $items,
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}/edit', name: 'technique_edit', methods: ['GET', 'POST'])]
    #[Template]
    public function edit(EntityManagerInterface $entityManager, Request $request, Technique $technique) : array|RedirectResponse {
        $form = $this->createForm(TechniqueType::class, $technique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'The updated technique has been saved.');

            return $this->redirectToRoute('technique_show', ['id' => $technique->getId()]);
        }

        return [
            'technique' => $technique,
            'form' => $form->createView(),
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}', name: 'technique_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Technique $technique) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $technique->getId(), $request->request->get('_token'))) {
            $entityManager->remove($technique);
            $entityManager->flush();
            $this->addFlash('success', 'The technique has been deleted.');
        }

        return $this->redirectToRoute('technique_index');
    }
}
