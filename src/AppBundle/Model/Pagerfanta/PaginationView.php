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
     * @param TemplateInterface|null $template
     */
    public function __construct(Translator $translator, TemplateInterface $template = null)
    {
        $this->translator = $translator;

        parent::__construct($template);
    }

    protected function createDefaultTemplate()
    {
        return new PaginationTemplate($this->translator);
    }

    protected function getDefaultProximity()
    {
        return 2;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'pagination_view';
    }
}
