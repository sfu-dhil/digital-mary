<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Period;
use App\Form\PeriodType;
use App\Repository\PeriodRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/period")
 */
class PeriodController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="period_index", methods={"GET"})
     *
     * @Template()
     */
    public function index(Request $request, PeriodRepository $periodRepository) : array {
        $query = $periodRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'periods' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/new", name="period_new", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $period = new Period();
        $form = $this->createForm(PeriodType::class, $period);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($period);
            $entityManager->flush();
            $this->addFlash('success', 'The new period has been saved.');

            return $this->redirectToRoute('period_show', ['id' => $period->getId()]);
        }

        return [
            'period' => $period,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="period_new_popup", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="period_show", methods={"GET"})
     * @Template()
     *
     * @return array
     */
    public function show(Period $period) {
        return [
            'period' => $period,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="period_edit", methods={"GET","POST"})
     *
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Period $period) {
        $form = $this->createForm(PeriodType::class, $period);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated period has been saved.');

            return $this->redirectToRoute('period_show', ['id' => $period->getId()]);
        }

        return [
            'period' => $period,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="period_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Period $period) {
        if ($this->isCsrfTokenValid('delete' . $period->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($period);
            $entityManager->flush();
            $this->addFlash('success', 'The period has been deleted.');
        }

        return $this->redirectToRoute('period_index');
    }
}
