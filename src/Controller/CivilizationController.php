<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Civilization;
use App\Form\CivilizationType;
use App\Repository\CivilizationRepository;
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
 * @Route("/civilization")
 */
class CivilizationController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="civilization_index", methods={"GET"})
     *
     * @Template()
     */
    public function index(Request $request, CivilizationRepository $civilizationRepository) : array {
        $query = $civilizationRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'civilizations' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="civilization_search", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function search(Request $request, CivilizationRepository $civilizationRepository) {
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

    /**
     * @Route("/typeahead", name="civilization_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, CivilizationRepository $civilizationRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($civilizationRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="civilization_new", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $civilization = new Civilization();
        $form = $this->createForm(CivilizationType::class, $civilization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
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

    /**
     * @Route("/new_popup", name="civilization_new_popup", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="civilization_show", methods={"GET"})
     * @Template()
     *
     * @return array
     */
    public function show(Request $request, Civilization $civilization) {
        $items = $this->paginator->paginate($civilization->getItems(), $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);

        return [
            'civilization' => $civilization,
            'items' => $items,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="civilization_edit", methods={"GET","POST"})
     *
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Civilization $civilization) {
        $form = $this->createForm(CivilizationType::class, $civilization);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated civilization has been saved.');

            return $this->redirectToRoute('civilization_show', ['id' => $civilization->getId()]);
        }

        return [
            'civilization' => $civilization,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="civilization_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Civilization $civilization) {
        if ($this->isCsrfTokenValid('delete' . $civilization->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($civilization);
            $entityManager->flush();
            $this->addFlash('success', 'The civilization has been deleted.');
        }

        return $this->redirectToRoute('civilization_index');
    }
}
