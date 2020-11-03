<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Item;
use App\Entity\RemoteImage;
use App\Form\ImageType;
use App\Form\ItemType;
use App\Form\RemoteImageType;
use App\Repository\ItemRepository;
use App\Services\FileUploader;
use DateTime;
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

/**
 * @Route("/item")
 */
class ItemController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="item_index", methods={"GET"})
     *
     * @Template()
     */
    public function index(Request $request, ItemRepository $itemRepository) : array {
        $query = $itemRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'items' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/search", name="item_search", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function search(Request $request, ItemRepository $itemRepository) {
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

    /**
     * @Route("/typeahead", name="item_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request, ItemRepository $itemRepository) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $data = [];
        foreach ($itemRepository->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/new", name="item_new", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new(Request $request, UserInterface $user) {
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item->addRevision(new DateTime(), $user->getFullname());
            $entityManager = $this->getDoctrine()->getManager();
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

    /**
     * @Route("/new_popup", name="item_new_popup", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function new_popup(Request $request, UserInterface $user) {
        return $this->new($request, $user);
    }

    /**
     * @Route("/{id}", name="item_show", methods={"GET"})
     * @Template()
     *
     * @return array
     */
    public function show(Item $item) {
        return [
            'item' => $item,
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit", name="item_edit", methods={"GET","POST"})
     *
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function edit(Request $request, Item $item, UserInterface $user) {
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item->addRevision(new DateTime(), $user->getFullname());
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated item has been saved.');

            return $this->redirectToRoute('item_show', ['id' => $item->getId()]);
        }

        return [
            'item' => $item,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}", name="item_delete", methods={"DELETE"})
     *
     * @return RedirectResponse
     */
    public function delete(Request $request, Item $item) {
        if ($this->isCsrfTokenValid('delete' . $item->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($item);
            $entityManager->flush();
            $this->addFlash('success', 'The item has been deleted.');
        }

        return $this->redirectToRoute('item_index');
    }

    /**
     * Add an image to an item.
     *
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/add_image", name="item_add_image", methods={"GET","POST"})
     * @Template()
     */
    public function addImage(Request $request, Item $item, EntityManagerInterface $em) {
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

    /**
     * Edit an image.
     *
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit_image/{image_id}", name="item_edit_image", methods={"GET","POST"})
     * @ParamConverter("image", options={"id" = "image_id"})
     * @Template()
     */
    public function editImage(Request $request, Item $item, Image $image, FileUploader $fileUploader, EntityManagerInterface $em) {
        $form = $this->createForm(ImageType::class, $image);
        $form->remove('imageFile');
        $form->add('newImageFile', FileType::class, [
            'label' => 'Replacement Image',
            'required' => false,
            'attr' => [
                'help_block' => "Select a file to upload which is less than {$fileUploader->getMaxUploadSize(false)} in size.",
                'data-maxsize' => $fileUploader->getMaxUploadSize(),
            ],
            'mapped' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (($upload = $form->get('newImageFile')->getData())) {
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

    /**
     * Edit an image.
     *
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/delete_image/{image_id}", name="item_delete_image", methods={"DELETE"})
     * @ParamConverter("image", options={"id" = "image_id"})
     * @Template()
     */
    public function deleteImage(Request $request, Item $item, Image $image, EntityManagerInterface $em) {
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();
            $this->addFlash('success', 'The remoteImage has been deleted.');
        } else {
            $this->addFlash('warning', 'Invalid security token.');
        }

        return $this->redirectToRoute('item_show', ['id' => $item->getId()]);
    }

    /**
     * Finds and returns a raw image file.
     *
     * @Route("/{id}/image/{image_id}", name="item_image_view", methods={"GET"})
     * @ParamConverter("image", options={"id" = "image_id"})
     *
     * @return BinaryFileResponse
     */
    public function view(Image $image) {
        if ( ! $image->getPublic() && ! $this->getUser()) {
            throw new AccessDeniedHttpException();
        }

        return new BinaryFileResponse($image->getImageFile());
    }

    /**
     * Finds and returns a raw image file.
     *
     * @Route("/{id}/tn/{image_id}", name="item_image_thumbnail", methods={"GET"})
     * @ParamConverter("image", options={"id" = "image_id"})
     *
     * @return BinaryFileResponse
     */
    public function thumbnail(Image $image) {
        if ( ! $image->getPublic() && ! $this->getUser()) {
            throw new AccessDeniedHttpException();
        }

        return new BinaryFileResponse($image->getThumbFile());
    }

    /**
     * @Route("/{id}/add_remote_image", name="item_add_remote_image", methods={"GET","POST"})
     * @Template()
     * @IsGranted("ROLE_CONTENT_ADMIN")
     *
     * @return array|RedirectResponse
     */
    public function newRemoteImage(Request $request, Item $item, EntityManagerInterface $em) {
        $remoteImage = new RemoteImage();
        $remoteImage->setItem($item);
        $form = $this->createForm(RemoteImageType::class, $remoteImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
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

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/edit_remote_image/{remote_image_id}", name="item_edit_remote_image", methods={"GET","POST"})
     * @ParamConverter("remoteImage", options={"id" = "remote_image_id"})
     *
     * @Template()
     *
     * @return array|RedirectResponse
     */
    public function editRemoteImage(Request $request, Item $item, RemoteImage $remoteImage) {
        $form = $this->createForm(RemoteImageType::class, $remoteImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'The updated remoteImage has been saved.');

            return $this->redirectToRoute('item_show', ['id' => $item->getId()]);
        }

        return [
            'remote_image' => $remoteImage,
            'form' => $form->createView(),
        ];
    }

    /**
     * @IsGranted("ROLE_CONTENT_ADMIN")
     * @Route("/{id}/delete_remote_image/{remote_image_id}", name="item_delete_remote_image", methods={"DELETE"})
     * @ParamConverter("remoteImage", options={"id" = "remote_image_id"})
     *
     * @return RedirectResponse
     */
    public function deleteRemoteImage(Request $request, Item $item, RemoteImage $remoteImage) {
        if ($this->isCsrfTokenValid('delete' . $remoteImage->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($remoteImage);
            $entityManager->flush();
            $this->addFlash('success', 'The remoteImage has been deleted.');
        } else {
            $this->addFlash('warning', 'Invalid security token.');
        }

        return $this->redirectToRoute('item_show', ['id' => $item->getId()]);
    }
}