<?php
/**
 * @author Artem Bochkarev
 */

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class AuthorAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id', null, [], ['value_type' => 'show'])
            ->add('litresHubId')
            ->add('documentId')
            ->add('firstName')
            ->add('lastName')
            ->add('middleName')
            ->add('level')
            ->add('recensesCount')
            ->add('artsCount')
            ->add('photo')
            ->add('description')
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
            ->add('artsCount')
            ->add('lastName')
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
            ->add('fullName')
            ->add('artsCount')
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
            ->add('firstName')
            ->add('lastName')
            ->add('middleName')
            ->add('level')
            ->add('recensesCount')
            ->add('artsCount')
            ->add('photo')
            ->add('description')
        ;
    }
}