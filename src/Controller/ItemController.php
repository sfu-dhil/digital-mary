<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Item;
use App\Entity\RemoteImage;
use App\Form\ImageType;
use App\Form\ItemType;
use App\Form\RemoteImageType;
use App\Repository\ItemRepository;
use App\Services\FileUploader;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route(path: '/item')]
class ItemController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    #[Route(path: '/', name: 'item_index', methods: ['GET'])]
    #[Template]
    public function index(Request $request, ItemRepository $itemRepository) : array {
        $query = $itemRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'items' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    #[Route(path: '/search', name: 'item_search', methods: ['GET'])]
    #[Template]
    public function search(Request $request, ItemRepository $itemRepository) : array {
        $q = $request->query->get('q');
        if ($q) {
            $query = $itemRepository->searchQuery($q);
            $items = $this->paginator->paginate($query, $request->query->getInt('page', 1), $this->getParameter('page_size'), ['wrap-queries' => true]);
        } else {
            $items = [];
        }

        return [
            'items' => $items,
            'q' => $q,
        ];
    }

    #[Route(path: '/typeahead', name: 'item_typeahead', methods: ['GET'])]
    public function typeahead(Request $request, ItemRepository $itemRepository) : JsonResponse {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];

        foreach ($itemRepository->typeaheadQuery($q)->execute() as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    #[Route(path: '/new', name: 'item_new', methods: ['GET', 'POST'])]
    #[Template]
    #[IsGranted('ROLE_CONTENT_ADMIN')]
    public function new(EntityManagerInterface $entityManager, Request $request, UserInterface $user) : array|RedirectResponse {
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($item->getContributions() as $contribution) {
                $contribution->setItem($item);
                if ( ! $entityManager->contains($contribution)) {
                    $entityManager->persist($contribution);
                }
            }
            $item->addRevision(new DateTimeImmutable(), $user->getFullname());
            $entityManager->persist($item);
            $entityManager->flush();
            $this->addFlash('success', 'The new item has been saved.');

            return $this->redirectToRoute('item_show', ['id' => $item->getId()]);
        }

        return [
            'item' => $item,
            'form' => $form->createView(),
        ];
    }

    #[Route(path: '/{id}', name: 'item_show', methods: ['GET'])]
    #[Template]
    public function show(Item $item) : array {
        return [
            'item' => $item,
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}/edit', name: 'item_edit', methods: ['GET', 'POST'])]
    #[Template]
    public function edit(EntityManagerInterface $entityManager, Request $request, Item $item, UserInterface $user) : array|RedirectResponse {
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($item->getContributions() as $contribution) {
                $contribution->setItem($item);
                if ( ! $entityManager->contains($contribution)) {
                    $entityManager->persist($contribution);
                }
            }
            $item->addRevision(new DateTimeImmutable(), $user->getFullname());
            $entityManager->flush();
            $this->addFlash('success', 'The updated item has been saved.');

            return $this->redirectToRoute('item_show', ['id' => $item->getId()]);
        }

        return [
            'item' => $item,
            'form' => $form->createView(),
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}', name: 'item_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Item $item) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $entityManager->remove($item);
            $entityManager->flush();
            $this->addFlash('success', 'The item has been deleted.');
        }

        return $this->redirectToRoute('item_index');
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}/add_image', name: 'item_add_image', methods: ['GET', 'POST'])]
    #[Template]
    public function addImage(Request $request, Item $item, EntityManagerInterface $em) : array|RedirectResponse {
        $image = new Image();
        $image->setItem($item);
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($image);
            $em->flush();
            $this->addFlash('success', 'The image has been added to the item.');

            return $this->redirectToRoute('item_show', ['id' => $item->getId()]);
        }

        return [
            'item' => $item,
            'form' => $form->createView(),
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}/edit_image/{image_id}', name: 'item_edit_image', methods: ['GET', 'POST'])]
    #[ParamConverter('image', options: ['id' => 'image_id'])]
    #[Template]
    public function editImage(Request $request, Item $item, Image $image, FileUploader $fileUploader, EntityManagerInterface $em) : array|RedirectResponse {
        $form = $this->createForm(ImageType::class, $image);
        $form->remove('imageFile');
        $form->add('newImageFile', FileType::class, [
            'label' => 'Replacement Image',
            'required' => false,
            'help' => "Select a file to upload which is less than {$fileUploader->getMaxUploadSize(false)} in size.",
            'attr' => [
                'data-maxsize' => $fileUploader->getMaxUploadSize(),
            ],
            'mapped' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($upload = $form->get('newImageFile')->getData()) {
                $image->setImageFile($upload);
                $image->preUpdate(); // The doctrine event won't fire properly without this.
            }
            $em->flush();
            $this->addFlash('success', 'The image has been updated.');

            return $this->redirectToRoute('item_show', ['id' => $item->getId()]);
        }

        return [
            'item' => $item,
            'image' => $image,
            'form' => $form->createView(),
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}/delete_image/{image_id}', name: 'item_delete_image', methods: ['DELETE'])]
    #[ParamConverter('image', options: ['id' => 'image_id'])]
    public function deleteImage(EntityManagerInterface $entityManager, Request $request, Item $item, Image $image, EntityManagerInterface $em) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {
            $entityManager->remove($image);
            $entityManager->flush();
            $this->addFlash('success', 'The Image has been deleted.');
        } else {
            $this->addFlash('warning', 'Invalid security token.');
        }

        return $this->redirectToRoute('item_show', ['id' => $item->getId()]);
    }

    /**
     * Finds and returns a raw image file.
     */
    #[Route(path: '/{id}/image/{image_id}', name: 'item_image_view', methods: ['GET'])]
    #[ParamConverter('image', options: ['id' => 'image_id'])]
    public function view(Image $image) : BinaryFileResponse {
        if ( ! $image->getPublic() && ! $this->getUser()) {
            throw new AccessDeniedHttpException();
        }

        return new BinaryFileResponse($image->getImageFile());
    }

    /**
     * Finds and returns a raw image file.
     */
    #[Route(path: '/{id}/tn/{image_id}', name: 'item_image_thumbnail', methods: ['GET'])]
    #[ParamConverter('image', options: ['id' => 'image_id'])]
    public function thumbnail(Image $image) : BinaryFileResponse {
        if ( ! $image->getPublic() && ! $this->getUser()) {
            throw new AccessDeniedHttpException();
        }

        return new BinaryFileResponse($image->getThumbFile());
    }

    #[Route(path: '/{id}/add_remote_image', name: 'item_add_remote_image', methods: ['GET', 'POST'])]
    #[Template]
    #[IsGranted('ROLE_CONTENT_ADMIN')]
    public function newRemoteImage(EntityManagerInterface $entityManager, Request $request, Item $item, EntityManagerInterface $em) : array|RedirectResponse {
        $remoteImage = new RemoteImage();
        $remoteImage->setItem($item);
        $form = $this->createForm(RemoteImageType::class, $remoteImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($remoteImage);
            $entityManager->flush();
            $this->addFlash('success', 'The new remoteImage has been saved.');

            return $this->redirectToRoute('item_show', ['id' => $item->getId()]);
        }

        return [
            'remote_image' => $remoteImage,
            'form' => $form->createView(),
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}/edit_remote_image/{remote_image_id}', name: 'item_edit_remote_image', methods: ['GET', 'POST'])]
    #[ParamConverter('remoteImage', options: ['id' => 'remote_image_id'])]
    #[Template]
    public function editRemoteImage(EntityManagerInterface $entityManager, Request $request, Item $item, RemoteImage $remoteImage) : array|RedirectResponse {
        $form = $this->createForm(RemoteImageType::class, $remoteImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'The updated remoteImage has been saved.');

            return $this->redirectToRoute('item_show', ['id' => $item->getId()]);
        }

        return [
            'remote_image' => $remoteImage,
            'form' => $form->createView(),
        ];
    }

    #[IsGranted('ROLE_CONTENT_ADMIN')]
    #[Route(path: '/{id}/delete_remote_image/{remote_image_id}', name: 'item_delete_remote_image', methods: ['DELETE'])]
    #[ParamConverter('remoteImage', options: ['id' => 'remote_image_id'])]
    public function deleteRemoteImage(EntityManagerInterface $entityManager, Request $request, Item $item, RemoteImage $remoteImage) : RedirectResponse {
        if ($this->isCsrfTokenValid('delete' . $remoteImage->getId(), $request->request->get('_token'))) {
            $entityManager->remove($remoteImage);
            $entityManager->flush();
            $this->addFlash('success', 'The remoteImage has been deleted.');
        } else {
            $this->addFlash('warning', 'Invalid security token.');
        }

        return $this->redirectToRoute('item_show', ['id' => $item->getId()]);
    }
}
