<?php

namespace App\Controller;

use App\Entity\Epigraphy;
use App\Form\EpigraphyType;
use App\Repository\EpigraphyRepository;

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
 * @Route("/epigraphy")
 * @IsGranted("ROLE_USER")
 */
class EpigraphyController extends AbstractController implements PaginatorAwareInterface
{
    use PaginatorTrait;

    /**
     * @Route("/", name="epigraphy_index", methods={"GET"})
     * @param Request $request
     * @param EpigraphyRepository $epigraphyRepository
     *
     * @Template()
     *
     * @return array
     */
    public function index(Request $request, EpigraphyRepository $epigraphyRepository) : array
    {
        $query = $epigraphyRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'epigraphies' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="epigraphy_search", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function search(Request $request, EpigraphyRepository $epigraphyRepository) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $epigraphyRepository->searchQuery($q);
            $epigraphies = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), array('wrap-queries'=>true));
        } else {
            $epigraphies = [];
        }

        return [
            'epigraphies' => $epigraphies,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="epigraphy_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, EpigraphyRepository $epigraphyRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($epigraphyRepository->typeaheadSearch($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string)$result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="epigraphy_new", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $epigraphy = new Epigraphy();
        $form = $this->createForm(EpigraphyType::class, $epigraphy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($epigraphy);
            $entityManager->flush();
            $this->addFlash('success', 'The new epigraphy has been saved.');

            return $this->redirectToRoute('epigraphy_show', ['id' => $epigraphy->getId()]);
        }

        return [
            'epigraphy' => $epigraphy,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="epigraphy_new_popup", methods={"GET","POST"})
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
     * @Route("/{id}", name="epigraphy_show", methods={"GET"})
     * @Template()
     * @param Epigraphy $epigraphy
     *
     * @return array
     */
    public function show(Epigraphy $epigraphy) {
        return [
            'epigraphy' => $epigraphy,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="epigraphy_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Epigraphy $epigraphy
     *
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Epigraphy $epigraphy) {
        $form = $this->createForm(EpigraphyType::class, $epigraphy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated epigraphy has been saved.');

            return $this->redirectToRoute('epigraphy_show', ['id' => $epigraphy->getId()]);
        }

        return [
            'epigraphy' => $epigraphy,
            'form' => $form->createView()
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="epigraphy_delete", methods={"DELETE"})
     * @param Request $request
     * @param Epigraphy $epigraphy
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Epigraphy $epigraphy) {
        if ($this->isCsrfTokenValid('delete' . $epigraphy->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($epigraphy);
            $entityManager->flush();
            $this->addFlash('success', 'The epigraphy has been deleted.');
        }

        return $this->redirectToRoute('epigraphy_index');
    }
}
