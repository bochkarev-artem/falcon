<?php

namespace AppBundle\Controller;

use AppBundle\Model\Pagination;
use AppBundle\Model\QueryParams;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class GenreController extends Controller
{
    /**
     * @param integer $id
     * @param integer $page
     *
     * @return Response
     */
    public function showAction($id, $page)
    {
        $defaultPerPage = $this->getParameter('default_per_page');

        $queryParams = new QueryParams();
        $queryParams
            ->setFilterGenres($id)
            ->setPage($page)
            ->setSize($defaultPerPage)
            ->setStart($queryParams->getOffset())
        ;

        $queryService = $this->get('query_service');
        $queryResult  = $queryService->query($queryParams);
        $books        = $queryResult->getResults();

        $genreRepo    = $this->getDoctrine()->getRepository('AppBundle:Genre');
        $genre        = $genreRepo->find($id);

        $pagination   = new Pagination($queryParams->getPage(), $queryParams->getSize());
        $pagination->paginate($queryResult->getTotalHits());

        return $this->render('AppBundle:Genre:show.html.twig', [
            'books'      => $books,
            'genre'      => $genre,
            'pagination' => $pagination->getViewData()
        ]);
    }

    public function listAction()
    {
        $genreRepo = $this->getDoctrine()->getRepository('AppBundle:Genre');
        $genres    = $genreRepo->findAll();

        return $this->render('AppBundle:Genre:list.html.twig', [
            'genres' => $genres,
        ]);
    }
}
