<?php

namespace AppBundle\Controller;

use AppBundle\Model\Pagination;
use AppBundle\Model\QueryParams;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function profileAction(Request $request)
    {
        if (!$user = $this->getUser()) {
            return new RedirectResponse('/');
        }

        $defaultPerPage  = $this->getParameter('default_per_page');
        $page            = 1;
        $bookPageService = $this->get('book_page_service');
        $bookData        = $bookPageService->getUserBooks($user->getId());
        $bookIds         = array_keys($bookData);

        $queryParams = new QueryParams();
        $queryParams->setFilterId($bookIds);

        $queryService = $this->get('query_service');
        $queryResult  = $queryService->query($queryParams);
        $books        = $queryResult->getResults();
        $pagination   = new Pagination($page, $defaultPerPage);

        $seoManager = $this->get('seo_manager');
        $seoManager->setUserProfileSeoData();

        return $this->render('AppBundle:User:profile.html.twig', [
            'books'       => $books,
            'pagination'  => $pagination->paginate($queryResult->getTotalHits()),
            'page'        => $page,
            'show_author' => true,
        ]);
    }
}
