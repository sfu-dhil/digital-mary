<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\CircaDate;
use App\Form\CircaDateType;
use App\Repository\CircaDateRepository;
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
 * @Route("/circa_date")
 * @IsGranted("ROLE_USER")
 */
class CircaDateController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="circa_date_index", methods={"GET"})
     *
     * @Template()
     */
    public function index(Request $request, CircaDateRepository $circaDateRepository) : array {
        $query = $circaDateRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'circa_dates' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="circa_date_search", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function search(Request $request, CircaDateRepository $circaDateRepository) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $circaDateRepository->searchQuery($q);
            $circaDates = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $circaDates = [];
        }

        return [
            'circa_dates' => $circaDates,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="circa_date_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, CircaDateRepository $circaDateRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($circaDateRepository->typeaheadSearch($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="circa_date_new", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $circaDate = new CircaDate();
        $form = $this->createForm(CircaDateType::class, $circaDate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($circaDate);
            $entityManager->flush();
            $this->addFlash('success', 'The new circaDate has been saved.');

            return $this->redirectToRoute('circa_date_show', ['id' => $circaDate->getId()]);
        }

        return [
            'circaDate' => $circaDate,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="circa_date_new_popup", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="circa_date_show", methods={"GET"})
     * @Template()
     *
     * @return array
     */
    public function show(CircaDate $circaDate) {
        return [
            'circa_date' => $circaDate,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="circa_date_edit", methods={"GET","POST"})
     *
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, CircaDate $circaDate) {
        $form = $this->createForm(CircaDateType::class, $circaDate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated circaDate has been saved.');

            return $this->redirectToRoute('circa_date_show', ['id' => $circaDate->getId()]);
        }

        return [
            'circa_date' => $circaDate,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="circa_date_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, CircaDate $circaDate) {
        if ($this->isCsrfTokenValid('delete' . $circaDate->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($circaDate);
            $entityManager->flush();
            $this->addFlash('success', 'The circaDate has been deleted.');
        }

        return $this->redirectToRoute('circa_date_index');
    }
}
