<?php

namespace App\Controller;

use App\Entity\Technique;
use App\Form\TechniqueType;
use App\Repository\TechniqueRepository;

use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/technique")
 * @IsGranted("ROLE_USER")
 */
class TechniqueController extends AbstractController implements PaginatorAwareInterface
{
    use PaginatorTrait;

    /**
     * @Route("/", name="technique_index", methods={"GET"})
     * @param Request $request
     * @param TechniqueRepository $techniqueRepository
     *
     * @Template()
     *
     * @return array
     */
    public function index(Request $request, TechniqueRepository $techniqueRepository) : array
    {
        $query = $techniqueRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'techniques' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="technique_search", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function search(Request $request, TechniqueRepository $techniqueRepository) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $techniqueRepository->searchQuery($q);
            $techniques = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), array('wrap-queries'=>true));
        } else {
            $techniques = [];
        }

        return [
            'techniques' => $techniques,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="technique_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, TechniqueRepository $techniqueRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($techniqueRepository->typeaheadSearch($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string)$result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="technique_new", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $technique = new Technique();
        $form = $this->createForm(TechniqueType::class, $technique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
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

    /**
     * @Route("/new_popup", name="technique_new_popup", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request) {
        return $this->new($request);
    }

    /**
     * @Route("/{id}", name="technique_show", methods={"GET"})
     * @Template()
     * @param Technique $technique
     *
     * @return array
     */
    public function show(Technique $technique) {
        return [
            'technique' => $technique,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="technique_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Technique $technique
     *
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Technique $technique) {
        $form = $this->createForm(TechniqueType::class, $technique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated technique has been saved.');

            return $this->redirectToRoute('technique_show', ['id' => $technique->getId()]);
        }

        return [
            'technique' => $technique,
            'form' => $form->createView()
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="technique_delete", methods={"DELETE"})
     * @param Request $request
     * @param Technique $technique
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Technique $technique) {
        if ($this->isCsrfTokenValid('delete' . $technique->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($technique);
            $entityManager->flush();
            $this->addFlash('success', 'The technique has been deleted.');
        }

        return $this->redirectToRoute('technique_index');
    }
}
