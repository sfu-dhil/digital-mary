<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\RemoteImage;
use App\Repository\RemoteImageRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/remote_image")
 */
class RemoteImageController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="remote_image_index", methods={"GET"})
     *
     * @Template()
     */
    public function index(Request $request, RemoteImageRepository $remoteImageRepository) : array {
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
            $remoteImages = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
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
        foreach ($remoteImageRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/{id}", name="remote_image_show", methods={"GET"})
     * @Template()
     *
     * @return array
     */
    public function show(RemoteImage $remoteImage) {
        return [
            'remote_image' => $remoteImage,
        ];
    }
}
