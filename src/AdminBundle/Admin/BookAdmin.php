<?php
/**
 * @author Artem Bochkarev
 */

namespace AdminBundle\Admin;

use AppBundle\Entity\Book;
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
                'property' => 'title',
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
            ->add('annotation')
            ->add('lang')
            ->add('publisher')
            ->add('cityPublished')
            ->add('yearPublished')
            ->add('isbn')
            ->add('rating')
            ->add('reviewCount')
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
            ->add('authors', null, [], 'entity', [
                'class' => 'AppBundle\Entity\Author',
            ])
            ->add('genres', null, [], 'entity', [
                'class' => 'AppBundle\Entity\Genre',
            ])
            ->add('tags', null, [], 'entity', [
                'class' => 'AppBundle\Entity\Tag',
            ])
            ->add('sequence', null, [], 'entity', [
                'class' => 'AppBundle\Entity\Sequence',
            ])
            ->add('litresHubId')
            ->add('documentId')
            ->add('title')
            ->add('slug')
            ->add('rating')
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
            ->add('path')
            ->add('authors')
            ->add('genres')
            ->add('tags')
            ->add('rating')
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
            ->add('rating')
            ->add('reviewCount')
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