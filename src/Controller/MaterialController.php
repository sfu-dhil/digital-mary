<?php

namespace App\Controller;

use App\Entity\Material;
use App\Form\MaterialType;
use App\Repository\MaterialRepository;

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
 * @Route("/material")
 * @IsGranted("ROLE_USER")
 */
class MaterialController extends AbstractController implements PaginatorAwareInterface
{
    use PaginatorTrait;

    /**
     * @Route("/", name="material_index", methods={"GET"})
     * @param Request $request
     * @param MaterialRepository $materialRepository
     *
     * @Template()
     *
     * @return array
     */
    public function index(Request $request, MaterialRepository $materialRepository) : array
    {
        $query = $materialRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'materials' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="material_search", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function search(Request $request, MaterialRepository $materialRepository) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $materialRepository->searchQuery($q);
            $materials = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), array('wrap-queries'=>true));
        } else {
            $materials = [];
        }

        return [
            'materials' => $materials,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="material_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, MaterialRepository $materialRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($materialRepository->typeaheadSearch($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string)$result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="material_new", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $material = new Material();
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($material);
            $entityManager->flush();
            $this->addFlash('success', 'The new material has been saved.');

            return $this->redirectToRoute('material_show', ['id' => $material->getId()]);
        }

        return [
            'material' => $material,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="material_new_popup", methods={"GET","POST"})
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
     * @Route("/{id}", name="material_show", methods={"GET"})
     * @Template()
     * @param Material $material
     *
     * @return array
     */
    public function show(Material $material) {
        return [
            'material' => $material,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="material_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Material $material
     *
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Material $material) {
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated material has been saved.');

            return $this->redirectToRoute('material_show', ['id' => $material->getId()]);
        }

        return [
            'material' => $material,
            'form' => $form->createView()
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="material_delete", methods={"DELETE"})
     * @param Request $request
     * @param Material $material
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Material $material) {
        if ($this->isCsrfTokenValid('delete' . $material->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($material);
            $entityManager->flush();
            $this->addFlash('success', 'The material has been deleted.');
        }

        return $this->redirectToRoute('material_index');
    }
}
