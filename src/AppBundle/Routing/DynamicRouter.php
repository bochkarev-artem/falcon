<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Routing;

use Elastica\Query\BoolQuery;
use Elastica\Query\Term;
use Elastica\Type;
use Symfony\Cmf\Component\Routing\DynamicRouter as BaseDynamicRouter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class DynamicRouter extends BaseDynamicRouter
{
    /**
     * @var Type
     */
    private $repository;

    /**
     * @param RequestContext                              $context
     * @param RequestMatcherInterface|UrlMatcherInterface $matcher
     * @param UrlGeneratorInterface                       $generator
     * @param EventDispatcherInterface                    $eventDispatcher
     * @param                                             $repository
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        RequestContext $context,
        $matcher,
        UrlGeneratorInterface $generator,
        EventDispatcherInterface $eventDispatcher = null,
        $repository
    ) {
        $this->context = $context;
        if (!$matcher instanceof RequestMatcherInterface && !$matcher instanceof UrlMatcherInterface) {
            throw new \InvalidArgumentException('Invalid $matcher');
        }

        $this->matcher         = $matcher;
        $this->generator       = $generator;
        $this->eventDispatcher = $eventDispatcher;
        $this->repository      = $repository;

        $this->generator->setContext($context);
    }

    /**
     * {@inheritDoc}
     */
    public function generate($name, $parameters = [], $absolute = false)
    {
        if ($name == 'dynamic_route') {
            $parameters['_path'] = $this->getPath($parameters);

            $route = new Route('/{_path}', [], ['_path' => '.*']);

            // remove default parameters extracted by DynamicRouter (in RouteRepository)
            // otherwise they will be added to query string
            if (isset($parameters['_params']) && is_array($parameters['_params'])) {
                foreach ($parameters['_params'] as $key) {
                    unset($parameters[$key]);
                }
                unset($parameters['_params']);
            }
            $collection = new RouteCollection();
            $collection->add('dynamic_route', $route);
            $generator = new UrlGenerator($collection, $this->context);

            return $generator->generate($name, $parameters, $absolute);
        }
        else {
            return parent::generate($name, $parameters, $absolute);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($name)
    {
        if ($name == 'dynamic_route') {
            return true;
        }
        else {
            return parent::supports($name);
        }
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    private function getPath($parameters)
    {
        $searchUrl = preg_replace('#^(.*?)(?:/\d+)?(?:\.html)?$#iu', '$1', $parameters['_path']);
        $boolQuery = new BoolQuery();
        $pathQuery = new Term();
        $pathQuery->setTerm('path', rawurldecode($searchUrl));
        $boolQuery->addMust($pathQuery);

        $results = $this->repository->search($boolQuery)->getResults();
        $path    = $parameters['_path'];
        if ($results) {
            $routeData = $results[0]->getData();
            $path      = $routeData['path'];
        }

        return $path;
    }
}
