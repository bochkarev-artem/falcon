<?php
/**
 * @author Artem Bochkarev
 */

namespace AdminBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class BookAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('litresHubId')
            ->add('documentId')
            ->add('title')
            ->add('slug')
            ->add('sequence')
            ->add('price')
            ->add('cover')
            ->add('authors', 'sonata_type_model_autocomplete', [
                'property' => 'lastName',
                'multiple' => true,
                'required' => false,
                'callback' => function(AuthorAdmin $admin, $property, $value) {
                    /** @var QueryBuilder $qb */
                    $qb      = $admin->getDatagrid()->getQuery();
                    $aliases = $qb->getRootAliases();
                    $qb
                        ->where($qb->expr()->like($aliases[0] . '.' . $property, ':fname'))
                        ->orWhere($qb->expr()->like($aliases[0] . '.firstName', ':mname'))
                        ->orWhere($qb->expr()->like($aliases[0] . '.middleName', ':lname'))
                        ->setParameter('fname', "%$value%")
                        ->setParameter('mname', "%$value%")
                        ->setParameter('lname', "%$value%")
                    ;
                },
            ])
            ->add('tags', 'sonata_type_model_autocomplete', [
                'property' => 'title',
                'required' => false,
                'multiple' => true,
            ])
            ->add('genres', 'sonata_type_model_autocomplete', [
                'property' => 'titleRu',
                'required' => false,
                'multiple' => true,
            ])
            ->add('sequence', 'sonata_type_model_autocomplete', [
                'property' => 'name',
                'required' => false,
            ])
            ->add('coverPath')
            ->add('hasTrial')
            ->add('featuredHome')
            ->add('featuredMenu')
            ->add('annotation', null, [
                'attr' => ['rows' => '7'],
            ])
            ->add('lang')
            ->add('publisher')
            ->add('cityPublished')
            ->add('yearPublished')
            ->add('isbn')
            ->add('sequenceNumber')
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('slug')
            ->add('lang')
            ->add('authors', 'doctrine_orm_callback', [
                    'callback' => function($queryBuilder, $alias, $field, $value) {
                        /* @var \Doctrine\ORM\QueryBuilder $queryBuilder */
                        if (!isset($value['value']) || !$value['value']) {
                            return false;
                        }

                        $queryBuilder->join($alias . '.authors', 'authors');
                        $filterExpr = $queryBuilder->expr()->orX(
                            $queryBuilder->expr()->like('authors.firstName', ':name'),
                            $queryBuilder->expr()->like('authors.lastName', ':name'),
                            $queryBuilder->expr()->like('authors.middleName', ':name')
                        );

                        if (preg_match('/^\s*[\d]+\s*$/', $value['value'])) {
                            $filterExpr = $queryBuilder->expr()->orX(
                                $filterExpr,
                                $queryBuilder->expr()->eq('authors.id', ':author_id')
                            );
                            $queryBuilder->setParameter('author_id', trim($value['value']));
                        }

                        $queryBuilder
                            ->andWhere($filterExpr)
                            ->setParameter('name', '%' . $value['value'] . '%')
                        ;

                        return true;
                    },
                ]
            )
            ->add('genres', 'doctrine_orm_callback', [
                    'callback' => function($queryBuilder, $alias, $field, $value) {
                        /* @var \Doctrine\ORM\QueryBuilder $queryBuilder */
                        if (!isset($value['value']) || !$value['value']) {
                            return false;
                        }

                        $queryBuilder->join($alias . '.genres', 'genres');
                        $filterExpr = $queryBuilder->expr()->like('genres.title', ':title');

                        if (preg_match('/^\s*[\d]+\s*$/', $value['value'])) {
                            $filterExpr = $queryBuilder->expr()->orX(
                                $filterExpr,
                                $queryBuilder->expr()->eq('genres.id', ':genre_id')
                            );
                            $queryBuilder->setParameter('genre_id', trim($value['value']));
                        }

                        $queryBuilder
                            ->andWhere($filterExpr)
                            ->setParameter('title', '%' . $value['value'] . '%')
                        ;

                        return true;
                    },
                ]
            )

            ->add('tags', 'doctrine_orm_callback', [
                    'callback' => function($queryBuilder, $alias, $field, $value) {
                        /* @var \Doctrine\ORM\QueryBuilder $queryBuilder */
                        if (!isset($value['value']) || !$value['value']) {
                            return false;
                        }

                        $queryBuilder->join($alias . '.tags', 'tags');
                        $filterExpr = $queryBuilder->expr()->like('tags.title', ':title');

                        if (preg_match('/^\s*[\d]+\s*$/', $value['value'])) {
                            $filterExpr = $queryBuilder->expr()->orX(
                                $filterExpr,
                                $queryBuilder->expr()->eq('tags.id', ':tag_id')
                            );
                            $queryBuilder->setParameter('tag_id', trim($value['value']));
                        }

                        $queryBuilder
                            ->andWhere($filterExpr)
                            ->setParameter('title', '%' . $value['value'] . '%')
                        ;

                        return true;
                    },
                ]
            )
            ->add('sequence', 'doctrine_orm_callback', [
                    'callback' => function($queryBuilder, $alias, $field, $value) {
                        /* @var \Doctrine\ORM\QueryBuilder $queryBuilder */
                        if (!isset($value['value']) || !$value['value']) {
                            return false;
                        }

                        $queryBuilder->join($alias . '.sequence', 'sequence');
                        $filterExpr = $queryBuilder->expr()->like('sequence.name', ':name');

                        if (preg_match('/^\s*[\d]+\s*$/', $value['value'])) {
                            $filterExpr = $queryBuilder->expr()->orX(
                                $filterExpr,
                                $queryBuilder->expr()->eq('sequence.id', ':sequence_id')
                            );
                            $queryBuilder->setParameter('sequence_id', trim($value['value']));
                        }

                        $queryBuilder
                            ->andWhere($filterExpr)
                            ->setParameter('name', '%' . $value['value'] . '%')
                        ;

                        return true;
                    },
                ]
            )
            ->add('litresHubId')
            ->add('documentId')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('litresHubId')
            ->add('documentId')
            ->add('title')
            ->add('lang')
            ->add('featuredHome', null, ['editable' => true])
            ->add('path')
            ->add('authors')
            ->add('genres')
            ->add('tags')
            ->add('updatedOn')
            ->add('createdOn')
            ->add('_action', 'actions', [
                'actions' => [
                    'show'   => [],
                    'edit'   => [],
                    'delete' => [],
                ]
            ])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('litresHubId')
            ->add('documentId')
            ->add('title')
            ->add('slug')
            ->add('price')
            ->add('cover')
            ->add('coverPath')
            ->add('hasTrial')
            ->add('genres')
            ->add('authors')
            ->add('tags')
            ->add('sequence')
            ->add('annotation')
            ->add('date')
            ->add('lang')
            ->add('publisher')
            ->add('cityPublished')
            ->add('yearPublished')
            ->add('isbn')
            ->add('sequenceNumber')
            ->add('updatedOn')
            ->add('createdOn')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);

        $rootAliases = $query->getRootAliases();
        $rootAlias   = array_shift($rootAliases);

        if ($context == 'list') {
            $query
                ->addSelect('a')
                ->addSelect('g')
                ->addSelect('t')
                ->leftJoin($rootAlias . '.authors', 'a')
                ->leftJoin($rootAlias . '.genres', 'g')
                ->leftJoin($rootAlias . '.tags', 't')
            ;
        }

        return $query;
    }
}
