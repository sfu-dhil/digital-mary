<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\CircaDate;
use App\Repository\CircaDateRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/circa_date")
 * @IsGranted("ROLE_ADMIN")
 */
class CircaDateController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * @Route("/", name="circa_date_index", methods={"GET"})
     *
     * @Template()
     */
    public function index(Request $request, CircaDateRepository $circaDateRepository) : array {
        $query = $circaDateRepository->indexQuery();
        $pageSize = $this->getParameter('page_size');
        $page = $request->query->getint('page', 1);

        return [
            'circa_dates' => $this->paginator->paginate($query, $page, $pageSize),
        ];
    }

    /**
     * @Route("/{id}", name="circa_date_show", methods={"GET"})
     * @Template()
     *
     * @return array
     */
    public function show(CircaDate $circaDate) {
        return [
            'circa_date' => $circaDate,
        ];
    }
}
