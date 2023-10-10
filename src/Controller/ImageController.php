<?php

declare(strict_types=1);

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

#[Route(path: '/image')]
class ImageController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'image_index', methods: ['GET'])]
    #[Template]
    public function index(Request $request, ImageRepository $imageRepository) : array {
        $query = $imageRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'images' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    #[Route(path: '/search', name: 'image_search', methods: ['GET'])]
    #[Template]
    public function search(Request $request, ImageRepository $imageRepository) : array {
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

    #[Route(path: '/typeahead', name: 'image_typeahead', methods: ['GET'])]
    public function typeahead(Request $request, ImageRepository $imageRepository) : JsonResponse {
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

    #[Route(path: '/{id}', name: 'image_show', methods: ['GET'])]
    #[Template]
    public function show(Image $image) : array {
        return [
            'image' => $image,
        ];
    }

    #[Route(path: '/{id}/view', name: 'image_view', methods: ['GET'])]
    public function image(Image $image) : BinaryFileResponse {
        if ( ! $image->getPublic() && ! $this->getUser()) {
            throw new AccessDeniedHttpException();
        }

        return new BinaryFileResponse($image->getImageFile());
    }

    #[Route(path: '/{id}/tn', name: 'image_thumbnail', methods: ['GET'])]
    public function thumbnail(Image $image) : BinaryFileResponse {
        if ( ! $image->getPublic() && ! $this->getUser()) {
            throw new AccessDeniedHttpException();
        }

        return new BinaryFileResponse($image->getThumbFile());
    }
}
