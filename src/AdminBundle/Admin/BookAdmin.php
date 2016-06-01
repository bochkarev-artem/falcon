<?php
/**
 * @author Artem Bochkarev
 */

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class BookAdmin extends Admin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id', null, [], ['value_type' => 'show'])
            ->add('litresHubId')
            ->add('title')
            ->add('authors', 'sonata_type_model', [
                'class'    => 'AppBundle\Entity\Author',
                'expanded' => false,
                'multiple' => true
            ])
            ->add('genres', 'sonata_type_model', [
                'class'    => 'AppBundle\Entity\Genre',
                'expanded' => false,
                'multiple' => true
            ])
            ->add('tags', 'sonata_type_model', [
                'class'    => 'AppBundle\Entity\Tag',
                'expanded' => false,
                'multiple' => true
            ])
            ->add('sequences', 'sonata_type_model', [
                'class'    => 'AppBundle\Entity\Sequence',
                'expanded' => false,
                'multiple' => true
            ])
            ->add('price')
            ->add('cover')
            ->add('coverPreview')
            ->add('filename')
            ->add('type')
            ->add('hasTrial')
            ->add('reader')
            ->add('annotation')
            ->add('date')
            ->add('lang')
            ->add('documentUrl')
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
            ->add('documentUrl')
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
            ->add('type')
            ->add('title')
            ->add('documentUrl')
            ->add('rating')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
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
            ->add('type')
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
            ->add('documentUrl')
            ->add('documentId')
            ->add('publisher')
            ->add('cityPublished')
            ->add('yearPublished')
            ->add('isbn')
            ->add('rating')
            ->add('recensesCount')
        ;
    }
}