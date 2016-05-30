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

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
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