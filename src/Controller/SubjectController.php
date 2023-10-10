<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Subject;
use App\Form\SubjectType;
use App\Repository\SubjectRepository;
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

#[Route(path: '/subject')]
class SubjectController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'subject_index', methods: ['GET'])]
    #[Template]
    public function index(Request $request, SubjectRepository $subjectRepository) : array {
        $query = $subjectRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getInt('page', 1);

        return [
            'subjects' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    #[Route(path: '/search', name: 'subject_search', methods: ['GET'])]
    #[Template]
    public function search(Request $request, SubjectRepository $subjectRepository) : array {
        $q = $request->query->get('q');
        if ($q) {
            $query = $subjectRepository->searchQuery($q);
            $subjects = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $subjects = [];
        }

        return [
            'subjects' => $subjects,
            'q' => $q,
        ];
    }

    #[Route(path: '/typeahead', name: 'subject_typeahead', methods: ['GET'])]
    public function typeahead(Request $request, SubjectRepository $subjectRepository) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];

        foreach ($subjectRepository->typeaheadQuery($q)->execute() as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    #[Route(path: '/new', name: 'subject_new', methods: ['GET', 'POST'])]
    #[Template]
    #[IsGranted('ROLE_CONTENT_ADMIN')]
    public function new(EntityManagerInterface $entityManager, Request $request) : array|RedirectResponse {
        $subject = new Subject();
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($subject);
            $entityManager->flush();
            $this->addFlash('success', 'The new subject has been saved.');

            return $this->redirectToRoute('subject_show', ['id' => $subject->getId()]);
        }

        return [
            'subject' => $subject,
            'form' => $form->createView(),
        ];
    }

    #[Route(path: '/{id}', name: 'subject_show', methods: ['GET'])]
    #[Template]
    public function show(Request $request, Subject $subject) : array {
        $items = $this->paginator->paginate($subject->getItems(), $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);

        return [
            'subject' => $subject,
            'items' => $items,
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}/edit', name: 'subject_edit', methods: ['GET', 'POST'])]
    #[Template]
    public function edit(EntityManagerInterface $entityManager, Request $request, Subject $subject) : array|RedirectResponse {
        $form = $this->createForm(SubjectType::class, $subject);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'The updated subject has been saved.');

            return $this->redirectToRoute('subject_show', ['id' => $subject->getId()]);
        }

        return [
            'subject' => $subject,
            'form' => $form->createView(),
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}', name: 'subject_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Subject $subject) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $subject->getId(), $request->request->get('_token'))) {
            $entityManager->remove($subject);
            $entityManager->flush();
            $this->addFlash('success', 'The subject has been deleted.');
        }

        return $this->redirectToRoute('subject_index');
    }
}
