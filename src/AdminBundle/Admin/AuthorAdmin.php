<?php
/**
 * @author Artem Bochkarev
 */

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class AuthorAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('documentId')
            ->add('firstName')
            ->add('lastName')
            ->add('middleName')
            ->add('lang')
            ->add('books', 'sonata_type_model_autocomplete', [
                'property' => 'title',
                'required' => false,
                'multiple' => true,
            ])
            ->add('slug')
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('documentId')
            ->add('firstName')
            ->add('lastName')
            ->add('slug')
            ->add('lang')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('documentId')
            ->add('fullName')
            ->add('slug')
            ->add('lang')
            ->add('updatedOn')
            ->add('createdOn')
            ->add('_action', 'actions', [
                'actions' => [
                    'show'   => [],
                    'edit'   => [],
                    'delete' => [],
                ],
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
            ->add('firstName')
            ->add('lastName')
            ->add('middleName')
            ->add('slug')
            ->add('lang')
            ->add('updatedOn')
            ->add('createdOn')
        ;
    }
}
