<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Period;
use App\Form\PeriodType;
use App\Repository\PeriodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/period')]
class PeriodController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'period_index', methods: ['GET'])]
    #[Template]
    public function index(Request $request, PeriodRepository $periodRepository) : array {
        $query = $periodRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'periods' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    #[Route(path: '/new', name: 'period_new', methods: ['GET', 'POST'])]
    #[Template]
    #[IsGranted('ROLE_CONTENT_ADMIN')]
    public function new(EntityManagerInterface $entityManager, Request $request) : array|RedirectResponse {
        $period = new Period();
        $form = $this->createForm(PeriodType::class, $period);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

    #[Route(path: '/{id}', name: 'period_show', methods: ['GET'])]
    #[Template]
    public function show(Period $period) : array {
        return [
            'period' => $period,
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}/edit', name: 'period_edit', methods: ['GET', 'POST'])]
    #[Template]
    public function edit(EntityManagerInterface $entityManager, Request $request, Period $period) : array|RedirectResponse {
        $form = $this->createForm(PeriodType::class, $period);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'The updated period has been saved.');

            return $this->redirectToRoute('period_show', ['id' => $period->getId()]);
        }

        return [
            'period' => $period,
            'form' => $form->createView(),
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}', name: 'period_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Period $period) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $period->getId(), $request->request->get('_token'))) {
            $entityManager->remove($period);
            $entityManager->flush();
            $this->addFlash('success', 'The period has been deleted.');
        }

        return $this->redirectToRoute('period_index');
    }
}
