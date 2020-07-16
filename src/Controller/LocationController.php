<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Location;
use App\Form\LocationType;
use App\Repository\LocationRepository;
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
 * @Route("/location")
 * @IsGranted("ROLE_USER")
 */
class LocationController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="location_index", methods={"GET"})
     *
     * @Template()
     */
    public function index(Request $request, LocationRepository $locationRepository) : array {
        $query = $locationRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'locations' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="location_search", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function search(Request $request, LocationRepository $locationRepository) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $locationRepository->searchQuery($q);
            $locations = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $locations = [];
        }

        return [
            'locations' => $locations,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="location_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, LocationRepository $locationRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($locationRepository->typeaheadSearch($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="location_new", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($location);
            $entityManager->flush();
            $this->addFlash('success', 'The new location has been saved.');

            return $this->redirectToRoute('location_show', ['id' => $location->getId()]);
        }

        return [
            'location' => $location,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="location_new_popup", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="location_show", methods={"GET"})
     * @Template()
     *
     * @return array
     */
    public function show(Request $request, Location $location) {
        $itemsFound = $this->paginator->paginate($location->getItemsFound(), $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        $itemsProvenanced = $this->paginator->paginate($location->getItemsProvenanced(), $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        return [
            'location' => $location,
            'itemsFound' => $itemsFound,
            'itemsProvenanced' => $itemsProvenanced
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="location_edit", methods={"GET","POST"})
     *
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Location $location) {
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated location has been saved.');

            return $this->redirectToRoute('location_show', ['id' => $location->getId()]);
        }

        return [
            'location' => $location,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="location_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Location $location) {
        if ($this->isCsrfTokenValid('delete' . $location->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($location);
            $entityManager->flush();
            $this->addFlash('success', 'The location has been deleted.');
        }

        return $this->redirectToRoute('location_index');
    }
}
