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
        $file = $this->getFile(self::DETAILED_DATA_FILE);

        if ($file) {
            $xml = new \SimpleXMLElement($file);
            $book = new Book();
            foreach ($xml->{'litres-updates'} as $art) {
                foreach ($art->attributes() as $name => $attribute) {
                    $data[$name] = $attribute;
                }
                if (!empty($data)) {
                    $book->setLitresIntId($data['int_id']);
                    $book->setPrice($data['price']);
                    $book->setCover($data['cover']);
                    $book->setIsSale($data['on_sale']);
                    $book->setFileIntId($data['file_id']);
                    $book->setType($data['type']);
                    $book->setIsShowPreview($data['show_preview']);
                    $book->setIsAllowRead($data['allow_read']);
                }
            }
            gzclose($file);
        }

    }
}