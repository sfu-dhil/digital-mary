<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ItemRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Show the home page.
     */
    #[Route(path: '/', name: 'homepage', methods: ['GET'])]
    #[Template]
    public function index(ItemRepository $itemRepository) : array {
        return [
            'items' => $itemRepository->getFeaturedItems(),
        ];
    }
}
