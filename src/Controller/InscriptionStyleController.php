<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\InscriptionStyle;
use App\Form\InscriptionStyleType;
use App\Repository\InscriptionStyleRepository;
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

#[Route(path: '/inscription_style')]
class InscriptionStyleController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'inscription_style_index', methods: ['GET'])]
    #[Template]
    public function index(Request $request, InscriptionStyleRepository $inscriptionStyleRepository) : array {
        $query = $inscriptionStyleRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'inscription_styles' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    #[Route(path: '/search', name: 'inscription_style_search', methods: ['GET'])]
    #[Template]
    public function search(Request $request, InscriptionStyleRepository $inscriptionStyleRepository) : array {
        $q = $request->query->get('q');
        if ($q) {
            $query = $inscriptionStyleRepository->searchQuery($q);
            $inscriptionStyles = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $inscriptionStyles = [];
        }

        return [
            'inscription_styles' => $inscriptionStyles,
            'q' => $q,
        ];
    }

    #[Route(path: '/typeahead', name: 'inscription_style_typeahead', methods: ['GET'])]
    public function typeahead(Request $request, InscriptionStyleRepository $inscriptionStyleRepository) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];

        foreach ($inscriptionStyleRepository->typeaheadQuery($q)->execute() as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    #[Route(path: '/new', name: 'inscription_style_new', methods: ['GET', 'POST'])]
    #[Template]
    #[IsGranted('ROLE_CONTENT_ADMIN')]
    public function new(EntityManagerInterface $entityManager, Request $request) : array|RedirectResponse {
        $inscriptionStyle = new InscriptionStyle();
        $form = $this->createForm(InscriptionStyleType::class, $inscriptionStyle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($inscriptionStyle);
            $entityManager->flush();
            $this->addFlash('success', 'The new inscriptionStyle has been saved.');

            return $this->redirectToRoute('inscription_style_show', ['id' => $inscriptionStyle->getId()]);
        }

        return [
            'inscription_style' => $inscriptionStyle,
            'form' => $form->createView(),
        ];
    }

    #[Route(path: '/{id}', name: 'inscription_style_show', methods: ['GET'])]
    #[Template]
    public function show(InscriptionStyle $inscriptionStyle) : array {
        return [
            'inscription_style' => $inscriptionStyle,
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}/edit', name: 'inscription_style_edit', methods: ['GET', 'POST'])]
    #[Template]
    public function edit(EntityManagerInterface $entityManager, Request $request, InscriptionStyle $inscriptionStyle) : array|RedirectResponse {
        $form = $this->createForm(InscriptionStyleType::class, $inscriptionStyle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'The updated inscriptionStyle has been saved.');

            return $this->redirectToRoute('inscription_style_show', ['id' => $inscriptionStyle->getId()]);
        }

        return [
            'inscription_style' => $inscriptionStyle,
            'form' => $form->createView(),
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}', name: 'inscription_style_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, InscriptionStyle $inscriptionStyle) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $inscriptionStyle->getId(), $request->request->get('_token'))) {
            $entityManager->remove($inscriptionStyle);
            $entityManager->flush();
            $this->addFlash('success', 'The inscriptionStyle has been deleted.');
        }

        return $this->redirectToRoute('inscription_style_index');
    }
}
