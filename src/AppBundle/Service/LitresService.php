<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Entity\Book;

/**
 * Class LitresService
 * @package AppBundle\Service
 */
class LitresService
{
    CONST DETAILED_DATA_FILE = 'http://www.litres.ru/static/ds/detailed_data.xml.gz';
    CONST BOOKS_ENDPOINT     = 'http://www.litres.ru/pages/catalit_browser/';

    /**
     * @param $file
     * @return resource|bool
     */
    public function getFile($file)
    {
        $filename = 'detailed_data.xml.gz';
        if (file_put_contents($filename, fopen($file, 'r'))) {
            return gzopen($filename, 'r');
        }

        return false;
    }

    /**
     *
     */
    public function getDetailedData()
    {
        $endpoint = self::BOOKS_ENDPOINT;

        if ($endpoint) {
            $xml = new \SimpleXMLElement($endpoint);
            $book = new Book();
            foreach ($xml->{'litres-updates'} as $art) {
                foreach ($art->attributes() as $name => $attribute) {
                    $data[$name] = $attribute;
                }
                if (!empty($data)) {
                    $book->setLitresHubId($data['hub_id'])
                        ->setPrice($data['price'])
                        ->setCover($data['cover'])
                        ->setHasTrial($data['on_sale'])
                        ->setFilename($data['file_id'])
                        ->setType($data['type'])
                    ;
                }
            }
        }

    }
}