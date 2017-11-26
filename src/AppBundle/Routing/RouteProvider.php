<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Routing;

use AppBundle\Service\LocaleService;
use Elastica\Query\BoolQuery;
use Elastica\Query\Term;
use Elastica\Type;
use Symfony\Cmf\Component\Routing\RouteProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteProvider implements RouteProviderInterface
{
    /**
     * @var Type
     */
    private $repository;

    /**
     * @var LocaleService
     */
    private $localeService;

    /**
     * @param Type $repository
     * @param LocaleService $localeService
     */
    public function __construct(Type $repository, LocaleService $localeService)
    {
        $this->repository    = $repository;
        $this->localeService = $localeService;
    }

    /**
     * {@inheritDoc}
     */
    public function getRouteCollectionForRequest(Request $request)
    {
        $url  = ltrim(rawurldecode($request->getPathInfo()), '/');
        $page = preg_replace('/.+\/([1-9]+[0-9]*)$/', '$1', $url);
        if ($page === $url) {
            $pageUrl = '';
            $page    = 1;
        } else {
            $pageUrl = '/' . $page;
        }
        $searchUrl = rtrim(preg_replace('#^(.*?)(\/\d+)?$#iu', '$1', $url), '/');
        $boolQuery = new BoolQuery();
        $pathQuery = new Term();
        $pathName  = 'path_' . $this->localeService->getLocale();
        $pathQuery->setTerm($pathName, rawurldecode($searchUrl));
        $boolQuery->addMust($pathQuery);

        $results = $this->repository->search($boolQuery)->getResults();

        $collection = new RouteCollection();
        if ($results) {
            $routeData = $results[0]->getData();
            if (array_key_exists('book', $routeData)) {
                $pageUrl = '';
            }
            if ($url != $searchUrl . $pageUrl) {
                $routeData = [
                    'params' => [
                        'defaults'     => [
                            '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
                            'path'        => '/' . $searchUrl,
                            'permanent'   => true,
                        ],
                        'requirements' => [],
                        'options'      => [],
                    ],
                ];
            } else {
                $routeData['params']['defaults']['page'] = $page;
            }

            $internalParams = [
                '_path'   => $url,
                '_params' => array_keys($routeData['params']['defaults']),
                '_locale' => $this->localeService->getLocale(),
            ];

            $collection->add(
                'dynamic_route',
                new Route(
                    $url,
                    array_merge($routeData['params']['defaults'], $internalParams),
                    $routeData['params']['requirements'],
                    $routeData['params']['options']
                )
            );
        }

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getRouteByName($name, $parameters = [])
    {
        throw new RouteNotFoundException;
    }

    /**
     * {@inheritDoc}
     */
    public function getRoutesByNames($names, $parameters = [])
    {
        return [];
    }
}
