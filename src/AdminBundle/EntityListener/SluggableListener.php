<?php
/**
 * @author Artem Bochkarev
 */
namespace AdminBundle\EntityListener;

use Gedmo\Sluggable\SluggableListener as GedmoListener;

class SluggableListener extends GedmoListener
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTransliterator(['AdminBundle\Utils\Transliterator', 'transliterate']);
    }
}
