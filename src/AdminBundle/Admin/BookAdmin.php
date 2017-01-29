<?php
/**
 * @author Artem Bochkarev
 */

namespace AdminBundle\Admin;

use AppBundle\Entity\Book;
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
            ->add('authors', 'entity', [
                'class'    => 'AppBundle\Entity\Author',
                'expanded' => false,
                'multiple' => true,
                'required' => false,
            ])
            ->add('genres', 'sonata_type_model', [
                'class'    => 'AppBundle\Entity\Genre',
                'expanded' => false,
                'multiple' => true,
                'required' => false,
            ])
            ->add('tags', 'sonata_type_model', [
                'class'    => 'AppBundle\Entity\Tag',
                'expanded' => false,
                'multiple' => true,
                'required' => false,
            ])
            ->add('sequence')
            ->add('price')
            ->add('cover')
            ->add('coverPath')
            ->add('filename')
            ->add('bookType', 'choice', [
                'choices'            => $this->getBookTypeChoices(),
                'translation_domain' => 'AdminBundle',
            ])
            ->add('hasTrial')
            ->add('featuredHome')
            ->add('featuredMenu')
            ->add('reader')
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
            ->add('bookType')
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
            ->add('bookType', null, [
                    'template' => 'AdminBundle:Book:list_book_custom.html.twig',
                    'widget'   => 'type',
                ]
            )
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
            ->add('filename')
            ->add('bookType', null, [
                    'template' => 'AdminBundle:Book:show_book_custom.html.twig',
                    'widget'   => 'type',
                ]
            )
            ->add('hasTrial')
            ->add('reader')
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
     * @return array
     */
    protected function getBookTypeChoices()
    {
        return [
            'choice.book0'  => Book::TYPE_ELECTRONIC,
            'choice.book1'  => Book::TYPE_AUDIO,
            'choice.book2'  => Book::TYPE_MULTIMEDIA,
            'choice.book3'  => Book::TYPE_READER,
            'choice.book4'  => Book::TYPE_PDF,
            'choice.book5'  => Book::TYPE_PRINT_ON_DEMAND,
            'choice.book6'  => Book::TYPE_DB,
            'choice.book7'  => Book::TYPE_VIDEO,
            'choice.book8'  => Book::TYPE_GAME,
            'choice.book9'  => Book::TYPE_SOFT,
            'choice.book11' => Book::TYPE_ADOBE_DRM,
        ];
    }

    /**
     * @param integer $value
     *
     * @return string
     */
    public function getBookTypeLabel($value)
    {
        return $this->trans(array_search($value, self::getBookTypeChoices()));
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);

        if ($context == 'list') {
            $query
                ->addSelect('a')
                ->addSelect('g')
                ->addSelect('t')
                ->leftJoin($query->getRootAlias() . '.authors', 'a')
                ->leftJoin($query->getRootAlias() . '.genres', 'g')
                ->leftJoin($query->getRootAlias() . '.tags', 't')
            ;
        }

        return $query;
    }
}