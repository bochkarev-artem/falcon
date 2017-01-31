<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class LitresBookManager
{
    CONST ID_LENGTH = 8;

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
        if (!$book['has_trial']) {
            return [];
        }

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

    /**
     * @param array $book
     *
     * @return string
     */
    public function getReadOnlineLink(array $book)
    {
        if (!$book['has_trial']) {
            return '';
        }

        $litresId         = $book['litres_id'];
        $litresDocumentId = $book['document_id'];
        $litres8DigitId   = $this->get8DigitCode($litresId);
        $litres6DigitId   = $this->get6DigitCode($litres8DigitId);

        return "https://www.litres.ru/static/or4/view/or.html?baseurl=/static/trials/$litres6DigitId/$litres8DigitId.&uuid=$litresDocumentId&art=$litresId&trials=1&lfrom=$this->partnerId";
    }

    /**
     * @param string $litresId
     *
     * @return string
     */
    protected function get8DigitCode($litresId)
    {
        $requireToAdd = self::ID_LENGTH - strlen($litresId);

        return $this->prependZeros($litresId, $requireToAdd);
    }

    /**
     * @param string $litres8DigitId
     *
     * @return string
     */
    protected function get6DigitCode($litres8DigitId)
    {
        $codeFragments = str_split($litres8DigitId, 2);
        $codeFragments = array_slice($codeFragments, 0, 3);

        return implode('/', $codeFragments);
    }

    /**
     * @param string  $litresId
     * @param integer $requireToAdd
     *
     * @return string
     */
    protected function prependZeros($litresId, $requireToAdd)
    {
        return str_repeat('0', $requireToAdd) . $litresId;
    }
}
