<?php

namespace App\Controller;

use App\Entity\RemoteImage;
use App\Form\RemoteImageType;
use App\Repository\RemoteImageRepository;

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
 * @Route("/remote_image")
 * @IsGranted("ROLE_USER")
 */
class RemoteImageController extends AbstractController implements PaginatorAwareInterface
{
    use PaginatorTrait;

    /**
     * @Route("/", name="remote_image_index", methods={"GET"})
     * @param Request $request
     * @param RemoteImageRepository $remoteImageRepository
     *
     * @Template()
     *
     * @return array
     */
    public function index(Request $request, RemoteImageRepository $remoteImageRepository) : array
    {
        $query = $remoteImageRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'remote_images' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="remote_image_search", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function search(Request $request, RemoteImageRepository $remoteImageRepository) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $remoteImageRepository->searchQuery($q);
            $remoteImages = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), array('wrap-queries'=>true));
        } else {
            $remoteImages = [];
        }

        return [
            'remote_images' => $remoteImages,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="remote_image_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, RemoteImageRepository $remoteImageRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($remoteImageRepository->typeaheadSearch($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string)$result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="remote_image_new", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request) {
        $remoteImage = new RemoteImage();
        $form = $this->createForm(RemoteImageType::class, $remoteImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($remoteImage);
            $entityManager->flush();
            $this->addFlash('success', 'The new remoteImage has been saved.');

            return $this->redirectToRoute('remote_image_show', ['id' => $remoteImage->getId()]);
        }

        return [
            'remoteImage' => $remoteImage,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/new_popup", name="remote_image_new_popup", methods={"GET","POST"})
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
     * @Route("/{id}", name="remote_image_show", methods={"GET"})
     * @Template()
     * @param RemoteImage $remoteImage
     *
     * @return array
     */
    public function show(RemoteImage $remoteImage) {
        return [
            'remote_image' => $remoteImage,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="remote_image_edit", methods={"GET","POST"})
     * @param Request $request
     * @param RemoteImage $remoteImage
     *
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, RemoteImage $remoteImage) {
        $form = $this->createForm(RemoteImageType::class, $remoteImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated remoteImage has been saved.');

            return $this->redirectToRoute('remote_image_show', ['id' => $remoteImage->getId()]);
        }

        return [
            'remote_image' => $remoteImage,
            'form' => $form->createView()
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="remote_image_delete", methods={"DELETE"})
     * @param Request $request
     * @param RemoteImage $remoteImage
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, RemoteImage $remoteImage) {
        if ($this->isCsrfTokenValid('delete' . $remoteImage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($remoteImage);
            $entityManager->flush();
            $this->addFlash('success', 'The remoteImage has been deleted.');
        }

        return $this->redirectToRoute('remote_image_index');
    }
}
