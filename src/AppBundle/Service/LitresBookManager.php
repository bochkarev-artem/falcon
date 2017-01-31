<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class LitresBookManager
{
    /**
     * @var string
     */
    protected $partnerId;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param Translator $translator
     * @param string     $partnerId
     */
    public function __construct(Translator $translator, $partnerId)
    {
        $this->translator = $translator;
        $this->partnerId  = $partnerId;
    }

    /**
     * @param array $book
     *
     * @return array
     */
    public function getDownloadLinks(array $book)
    {
        $links   = [];
        $formats = [
            'fb2',
            'epub',
            'rtf',
            'a4.pdf',
        ];

        $i = 0;
        foreach ($formats as $format) {
            $links[$i]['url'] = $this->translator->trans('front.litres_download_url', [
                '%litres_hub_id%' => $book['litres_id'],
                '%format%'        => $format,
                '%partner_id%'    => $this->partnerId
            ]);
            $links[$i]['text'] = $this->translator->trans('front.litres_download_link_text_' . $format);
            $i++;
        }

        return $links;
    }
}
