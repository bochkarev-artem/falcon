<?php
/**
 * @author Artem Bochkarev
 */

namespace AdminBundle\Admin;

use AppBundle\Entity\Ads;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class AdsAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('active')
            ->add('code', null, [
                'attr' => ['rows' => '7'],
            ])
            ->add('priority')
            ->add('position', 'choice', [
                'choices'            => $this->getPositionChoices(),
                'translation_domain' => 'AdminBundle',
            ])
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('position')
            ->add('active')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('active')
            ->add('priority')
            ->add('position', null, [
                    'template' => 'AdminBundle:Ads:list_ads_custom.html.twig',
                    'widget'   => 'position',
                ]
            )
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
            ->add('name')
            ->add('active')
            ->add('position', null, [
                    'template' => 'AdminBundle:Ads:show_ads_custom.html.twig',
                    'widget'   => 'position',
                ]
            )
            ->add('updatedOn')
            ->add('createdOn')
        ;
    }


    /**
     * @return array
     */
    protected function getPositionChoices()
    {
        return [
            'choice.ads_position.index'        => Ads::POSITION_INDEX,
            'choice.ads_position.book_top'     => Ads::POSITION_BOOK_TOP,
            'choice.ads_position.book_bottom'  => Ads::POSITION_BOOK_BOTTOM,
            'choice.ads_position.catalog_side' => Ads::POSITION_CATALOG_SIDE,
            'choice.ads_position.catalog_top'  => Ads::POSITION_CATALOG_TOP,
            'choice.ads_position.book_mobile'  => Ads::POSITION_BOOK_MOBILE,
        ];
    }

    /**
     * @param integer $value
     *
     * @return string
     */
    public function getPositionLabel($value)
    {
        return $this->trans(array_search($value, self::getPositionChoices()));
    }
}
