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
            ->add('litresHubId')
            ->add('documentId')
            ->add('books', 'entity', [
                'class'        => 'AppBundle\Entity\Book',
                'expanded'     => false,
                'multiple'     => true,
                'required'     => false,
                'by_reference' => false,
            ])
            ->add('firstName')
            ->add('lastName')
            ->add('middleName')
            ->add('slug')
            ->add('level')
            ->add('reviewCount')
            ->add('artsCount')
            ->add('photoPath')
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
            ->add('firstName')
            ->add('lastName')
            ->add('slug')
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
            ->add('slug')
            ->add('artsCount')
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
            ->add('documentId')
            ->add('firstName')
            ->add('lastName')
            ->add('middleName')
            ->add('slug')
            ->add('level')
            ->add('reviewCount')
            ->add('artsCount')
            ->add('photoPath')
            ->add('description')
        ;
    }
}