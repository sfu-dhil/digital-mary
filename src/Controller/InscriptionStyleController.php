<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\InscriptionStyle;
use App\Form\InscriptionStyleType;
use App\Repository\InscriptionStyleRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/inscription_style")
 */
class InscriptionStyleController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="inscription_style_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, InscriptionStyleRepository $inscriptionStyleRepository) : array {
        $query = $inscriptionStyleRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'inscription_styles' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="inscription_style_search", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function search(Request $request, InscriptionStyleRepository $inscriptionStyleRepository) {
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

    /**
     * @Route("/typeahead", name="inscription_style_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, InscriptionStyleRepository $inscriptionStyleRepository) {
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

    /**
     * @Route("/new", name="inscription_style_new", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $inscriptionStyle = new InscriptionStyle();
        $form = $this->createForm(InscriptionStyleType::class, $inscriptionStyle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
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

    /**
     * @Route("/new_popup", name="inscription_style_new_popup", methods={"GET", "POST"})
     * @Template
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="inscription_style_show", methods={"GET"})
     * @Template
     *
     * @return array
     */
    public function show(InscriptionStyle $inscriptionStyle) {
        return [
            'inscription_style' => $inscriptionStyle,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="inscription_style_edit", methods={"GET", "POST"})
     *
     * @Template
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, InscriptionStyle $inscriptionStyle) {
        $form = $this->createForm(InscriptionStyleType::class, $inscriptionStyle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated inscriptionStyle has been saved.');

            return $this->redirectToRoute('inscription_style_show', ['id' => $inscriptionStyle->getId()]);
        }

        return [
            'inscription_style' => $inscriptionStyle,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="inscription_style_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, InscriptionStyle $inscriptionStyle) {
        if ($this->isCsrfTokenValid('delete' . $inscriptionStyle->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($inscriptionStyle);
            $entityManager->flush();
            $this->addFlash('success', 'The inscriptionStyle has been deleted.');
        }

        return $this->redirectToRoute('inscription_style_index');
    }
}
