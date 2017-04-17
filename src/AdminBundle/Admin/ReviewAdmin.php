<?php
/**
 * @author Artem Bochkarev
 */

namespace AdminBundle\Admin;

use AppBundle\Entity\BookReview;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class ReviewAdmin extends AbstractAdmin
{
    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('book', 'sonata_type_model_autocomplete', [
                'property' => 'title',
                'required' => true,
            ])
            ->add('user', 'sonata_type_model_autocomplete', [
                'property' => 'username',
                'required' => true,
            ])
            ->add('text', null, [
                'attr' => ['rows' => '7'],

            ])
            ->add('status', 'choice', [
                'choices'            => $this->getReviewStatusChoices(),
                'translation_domain' => 'AdminBundle',
            ])
            ->add('rejectReason')
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('status')
            ->add('user', 'doctrine_orm_callback', [
                    'callback' => function($queryBuilder, $alias, $field, $value) {
                        /* @var \Doctrine\ORM\QueryBuilder $queryBuilder */
                        if (!isset($value['value']) || !$value['value']) {
                            return false;
                        }

                        $queryBuilder->join($alias . '.user', 'u');
                        $filterExpr = $queryBuilder->expr()->orX(
                            $queryBuilder->expr()->like('u.firstName', ':name'),
                            $queryBuilder->expr()->like('u.lastName', ':name'),
                            $queryBuilder->expr()->like('u.username', ':name')
                        );

                        if (preg_match('/^\s*[\d]+\s*$/', $value['value'])) {
                            $filterExpr = $queryBuilder->expr()->orX(
                                $filterExpr,
                                $queryBuilder->expr()->eq('u.id', ':user_id')
                            );
                            $queryBuilder->setParameter('user_id', trim($value['value']));
                        }

                        $queryBuilder
                            ->andWhere($filterExpr)
                            ->setParameter('name', '%' . $value['value'] . '%')
                        ;

                        return true;
                    },
                ]
            )
            ->add('book', 'doctrine_orm_callback', [
                    'callback' => function($queryBuilder, $alias, $field, $value) {
                        /* @var \Doctrine\ORM\QueryBuilder $queryBuilder */
                        if (!isset($value['value']) || !$value['value']) {
                            return false;
                        }

                        $queryBuilder->join($alias . '.book', 'b');
                        $filterExpr = $queryBuilder->expr()->like('b.title', ':title');

                        if (preg_match('/^\s*[\d]+\s*$/', $value['value'])) {
                            $filterExpr = $queryBuilder->expr()->orX(
                                $filterExpr,
                                $queryBuilder->expr()->eq('b.id', ':book_id')
                            );
                            $queryBuilder->setParameter('book_id', trim($value['value']));
                        }

                        $queryBuilder
                            ->andWhere($filterExpr)
                            ->setParameter('title', '%' . $value['value'] . '%')
                        ;

                        return true;
                    },
                ]
            )
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('book')
            ->add('user')
            ->add('status', null, [
                    'template' => 'AdminBundle:BookReview:list_review_custom.html.twig',
                    'widget'   => 'status',
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
            ->add('book')
            ->add('user')
            ->add('text')
            ->add('status', null, [
                    'template' => 'AdminBundle:BookReview:show_review_custom.html.twig',
                    'widget'   => 'status',
                ]
            )
            ->add('rejectReason')
            ->add('updatedOn')
            ->add('createdOn')
        ;
    }


    /**
     * @return array
     */
    protected function getReviewStatusChoices()
    {
        return [
            'choice.review_status.pending'  => BookReview::STATUS_PENDING,
            'choice.review_status.approved' => BookReview::STATUS_APPROVED,
            'choice.review_status.rejected' => BookReview::STATUS_REJECTED,
        ];
    }

    /**
     * @param integer $value
     *
     * @return string
     */
    public function getReviewStatusLabel($value)
    {
        return $this->trans(array_search($value, self::getReviewStatusChoices()));
    }
}
