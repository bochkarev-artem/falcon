<?php
/**
 * @author Artem Bochkarev
 */
namespace AppBundle\Model\Pagerfanta;

use Pagerfanta\View\DefaultView;
use Pagerfanta\View\Template\TemplateInterface;
use Symfony\Component\Translation\Translator;

class PaginationView extends DefaultView
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * PaginationView constructor.
     * @param Translator $translator
     * @param null|TemplateInterface $template
     */
    public function __construct(Translator $translator, TemplateInterface $template = null)
    {
        $this->translator = $translator;

        parent::__construct($template);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pagination_view';
    }

    protected function createDefaultTemplate()
    {
        return new PaginationTemplate($this->translator);
    }

    protected function getDefaultProximity()
    {
        return 2;
    }
}
