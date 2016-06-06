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
            ->add('title')
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
            ->add('sequences', 'entity', [
                'class'    => 'AppBundle\Entity\Sequence',
                'expanded' => false,
                'multiple' => true,
                'required' => false,
            ])
            ->add('price')
            ->add('cover')
            ->add('coverPreview')
            ->add('filename')
            ->add('type', 'choice', [
                'choices'            => $this->getBookTypeChoices(),
                'translation_domain' => 'AdminBundle',
            ])
            ->add('hasTrial')
            ->add('reader')
            ->add('annotation')
            ->add('date')
            ->add('lang')
            ->add('documentId')
            ->add('publisher')
            ->add('cityPublished')
            ->add('yearPublished')
            ->add('isbn')
            ->add('rating')
            ->add('recensesCount')
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
            ->add('sequences', null, [], 'entity', [
                'class' => 'AppBundle\Entity\Sequence',
            ])
            ->add('litresHubId')
            ->add('documentId')
            ->add('type')
            ->add('title')
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
            ->add('type', null, [
                    'template' => 'AdminBundle:Book:list_book_custom.html.twig',
                    'widget'   => 'type',
                ]
            )
            ->add('title')
            ->add('publisher')
            ->add('rating')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show'   => array(),
                    'edit'   => array(),
                    'delete' => array(),
                )
            ))
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
            ->add('price')
            ->add('cover')
            ->add('coverPreview')
            ->add('filename')
            ->add('type', null, [
                    'template' => 'AdminBundle:Book:show_book_custom.html.twig',
                    'widget'   => 'type',
                ]
            )
            ->add('hasTrial')
            ->add('reader')
            ->add('genres')
            ->add('authors')
            ->add('tags')
            ->add('sequences')
            ->add('title')
            ->add('annotation')
            ->add('date')
            ->add('lang')
            ->add('documentId')
            ->add('publisher')
            ->add('cityPublished')
            ->add('yearPublished')
            ->add('isbn')
            ->add('rating')
            ->add('recensesCount')
        ;
    }

    /**
     * @return array
     */
    protected function getBookTypeChoices()
    {
        return [
            'choice.book0'  => Book::ELECTRONIC_BOOK,
            'choice.book1'  => Book::AUDIO_BOOK,
            'choice.book2'  => Book::MULTIMEDIA_BOOK,
            'choice.book3'  => Book::READER,
            'choice.book4'  => Book::PDF_BOOK,
            'choice.book5'  => Book::PRINT_ON_DEMAND_BOOK,
            'choice.book6'  => Book::DB,
            'choice.book7'  => Book::VIDEO,
            'choice.book8'  => Book::GAME,
            'choice.book9'  => Book::SOFT,
            'choice.book11' => Book::ADOBE_DRM,
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
}