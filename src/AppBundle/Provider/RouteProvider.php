<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Provider;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Sequence;
use AppBundle\Entity\Tag;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Elastica\Document;
use Elastica\Type;
use FOS\ElasticaBundle\Provider\ProviderInterface;

class RouteProvider implements ProviderInterface
{
    /**
     * @var Type
     */
    private $routeType;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var integer
     */
    private $batchSize;

    /**
     * @param Type          $routeType
     * @param EntityManager $em
     * @param integer       $batchSize
     */
    public function __construct(Type $routeType, EntityManager $em, $batchSize)
    {
        $this->routeType = $routeType;
        $this->em        = $em;
        $this->batchSize = $batchSize;
    }

    /**
     * {@inheritdoc}
     */
    public function populate(\Closure $loggerClosure = null, array $options = [])
    {
        $this->updateDocumentsByQuery($this->createQueryBuilder('Book'), $loggerClosure);
        $this->em->clear();

        $this->updateDocumentsByQuery($this->createQueryBuilder('Author'), $loggerClosure);
        $this->em->clear();

        $this->updateDocumentsByQuery($this->createQueryBuilder('Genre'), $loggerClosure);
        $this->em->clear();

        $this->updateDocumentsByQuery($this->createQueryBuilder('Tag'), $loggerClosure);
        $this->em->clear();

        $this->updateDocumentsByQuery($this->createQueryBuilder('Sequence'), $loggerClosure);
        $this->em->clear();
    }

    /**
     * @param string $entity
     *
     * @return QueryBuilder
     */
    private function createQueryBuilder($entity)
    {
        /* @var QueryBuilder $queryBuilder */
        $qb = $this->em->createQueryBuilder();

        return $qb
            ->select('o')
            ->from("AppBundle:$entity", 'o')
        ;
    }

    /**
     * @param $object
     *
     * @return array|bool
     */
    private function prepareDocuments($object)
    {
        $className = (new \ReflectionClass($object))->getShortName();
        $routes    = $this->collectObjectData($object, $className);
        $documents = [];
        foreach ($routes as $routeId => $routeData) {
            array_push($documents, new Document($routeId, $routeData, 'route'));
        }

        return $documents;
    }

    /**
     * @param Book|Author|Genre|Tag|Sequence $object
     * @param string                         $className
     *
     * @return array
     */
    private function collectObjectData($object, $className)
    {
        $type        = strtolower($className);
        $objectId    = $object->getId();
        $routeParams = [
            'defaults' => [
                'id' => $objectId,
            ],
            'requirements' => [],
            'options'      => [],
        ];

        $routes = [];
        $routeId   = $type . ':' . $objectId;
        $routeData = [
            'params' => $routeParams,
            $type    => $objectId,
        ];
        $routeData['params']['defaults']['_controller'] = "AppBundle:$className:show";
        $routeData['path'] = $type . '/' . $object->getSlug();
        $routes[$routeId]  = $routeData;

        return $routes;
    }

    /**
     * @param QueryBuilder  $queryBuilder
     * @param \Closure|null $loggerClosure
     *
     * @return bool
     */
    private function updateDocumentsByQuery(QueryBuilder $queryBuilder, \Closure $loggerClosure = null)
    {
        $nbObjects      = $this->countObjects($queryBuilder);
        $objects        = $this->getQueryIterator($queryBuilder);
        $documentsToAdd = [];
        $processed      = 0;
        $lastCount      = 0;
        $stepStartTime  = microtime(true);

        foreach ($objects as $object) {
            if ($documents = $this->prepareDocuments(array_shift($object))) {
                $documentsToAdd = array_merge($documentsToAdd, $documents);
            }

            $processed++;
            if ($processed % $this->batchSize === 0) {
                if ($loggerClosure) {
                    $stepNbObjects    = $processed - $lastCount;
                    $stepCount        = $processed;
                    $percentComplete  = 100 * $stepCount / $nbObjects;
                    $objectsPerSecond = $stepNbObjects / (microtime(true) - $stepStartTime);
                    $active           = round(memory_get_usage(true) / 1024 / 1024, 1);
                    $peak             = round(memory_get_peak_usage(true) / 1024 / 1024, 1);
                    $loggerClosure(
                        $stepCount,
                        $nbObjects,
                        "\n" . sprintf(
                            '%0.1f%% (%d/%d), %d objects/s %0.1fMb/%0.1fMb',
                            $percentComplete,
                            $stepCount,
                            $nbObjects,
                            $objectsPerSecond,
                            $active,
                            $peak
                        )
                    );
                }

                $this->routeType->addDocuments($documentsToAdd);
                $this->em->clear();

                $documentsToAdd = [];
                $lastCount      = $processed;
                $stepStartTime  = microtime(true);
            }
        }

        if ($documentsToAdd) {
            if ($loggerClosure) {
                $stepNbObjects    = $processed - $lastCount;
                $stepCount        = $processed;
                $percentComplete  = 100 * $stepCount / $nbObjects;
                $objectsPerSecond = $stepNbObjects / (microtime(true) - $stepStartTime);
                $active           = round(memory_get_usage(true) / 1024 / 1024, 1);
                $peak             = round(memory_get_peak_usage(true) / 1024 / 1024, 1);
                $loggerClosure(
                    $stepCount,
                    $nbObjects,
                    "\n" . sprintf(
                        '%0.1f%% (%d/%d), %d objects/s %0.1fMb/%0.1fMb',
                        $percentComplete,
                        $stepCount,
                        $nbObjects,
                        $objectsPerSecond,
                        $active,
                        $peak
                    )
                );
            }

            $this->routeType->addDocuments($documentsToAdd);
            $this->em->clear();
        }

        return true;
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return IterableResult|array
     */
    protected function getQueryIterator(QueryBuilder $queryBuilder)
    {
        try {
            $objects = $queryBuilder->getQuery()->iterate();
        }
        catch (QueryException $e) {
            $aliases  = $queryBuilder->getRootAliases();
            $entities = $queryBuilder->getRootEntities();

            $idQb = clone $queryBuilder;
            $res  = $idQb
                ->select($aliases[0] . '.id')
                ->add('from', new Expr\From($entities[0], $aliases[0], $aliases[0] . '.id'), false)
                ->getQuery()
                ->getResult()
            ;

            $ids = array_keys($res);
            if (!$ids) {
                return [];
            }

            $newQb   = $this->em->createQueryBuilder();
            $objects = $newQb
                ->select($aliases[0])
                ->from($entities[0], $aliases[0], $aliases[0] . '.id')
                ->where($queryBuilder->expr()->in($aliases[0] . '.id', $ids))
                ->getQuery()
                ->iterate()
            ;
        }

        return $objects;
    }

    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return int
     */
    private function countObjects(QueryBuilder $queryBuilder)
    {
        $qb = clone $queryBuilder;

        $aliases = $qb->getRootAliases();
        $qb->select('COUNT(' . $aliases[0] . '.id)');

        return (integer) $qb->getQuery()->getSingleScalarResult();
    }
}
