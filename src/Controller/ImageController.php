<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/image")
 */
class ImageController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="image_index", methods={"GET"})
     *
     * @Template
     */
    public function index(Request $request, ImageRepository $imageRepository) : array {
        $query = $imageRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'images' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="image_search", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function search(Request $request, ImageRepository $imageRepository) {
        $q = $request->query->get('q');
        if ($q) {
            $query = $imageRepository->searchQuery($q);
            $images = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $images = [];
        }

        return [
            'images' => $images,
            'q' => $q,
        ];
    }

    /**
     * @Route("/typeahead", name="image_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, ImageRepository $imageRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];

        foreach ($imageRepository->typeaheadQuery($q)->execute() as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/{id}", name="image_show", methods={"GET"})
     * @Template
     *
     * @return array
     */
    public function show(Image $image) {
        return [
            'image' => $image,
        ];
    }

    /**
     * Finds and returns a raw image file.
     *
     * @Route("/{id}/view", name="image_view", methods={"GET"})
     *
     * @return BinaryFileResponse
     */
    public function imageAction(Image $image) {
        if ( ! $image->getPublic() && ! $this->getUser()) {
            throw new AccessDeniedHttpException();
        }

        return new BinaryFileResponse($image->getImageFile());
    }

    /**
     * Finds and returns a raw image file.
     *
     * @Route("/{id}/tn", name="image_thumbnail", methods={"GET"})
     *
     * @return BinaryFileResponse
     */
    public function thumbnailAction(Image $image) {
        if ( ! $image->getPublic() && ! $this->getUser()) {
            throw new AccessDeniedHttpException();
        }

        return new BinaryFileResponse($image->getThumbFile());
    }
}
