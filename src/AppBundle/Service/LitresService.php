<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * Class LitresService
 * @package AppBundle\Service
 */
class LitresService
{
    CONST DETAILED_DATA_FILE = 'http://www.litres.ru/static/ds/detailed_data.xml.gz';
    CONST BOOKS_ENDPOINT     = 'http://www.litres.ru/pages/catalit_browser/';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return string
     */
    protected function getBooksEndpoint()
    {
        return self::BOOKS_ENDPOINT;
    }

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
    public function getBooksData()
    {
        $endpoint = $this->getBooksEndpoint();

        if ($endpoint) {
            $xml  = new \SimpleXMLElement($endpoint, 0, true);
            $book = new Book;
            foreach ($xml->{'catalit-fb2-books'}->{'fb2-book'} as $data) {
                $genreCollection  = new ArrayCollection();
                $authorCollection = new ArrayCollection();
                $tagCollection    = new ArrayCollection();
                $bookInfo         = $data->{'text_description'}->hidden->{'title-info'};
                $documentInfo     = $data->{'text_description'}->hidden->{'document-info'};
                $publishInfo      = $data->{'text_description'}->hidden->{'publish-info'};
                $authorRepo       = $this->em->getRepository('AppBundle:Author');
                $genreRepo        = $this->em->getRepository('AppBundle:Genre');
                $tagRepo          = $this->em->getRepository('AppBundle:Tag');
                $sequenceRepo     = $this->em->getRepository('AppBundle:Tag');
                $sequence         = $sequenceRepo->findByLitresId($bookInfo->sequences->sequence['id']);
                foreach ($bookInfo->genre as $genre) {
                    $genreCollection->add($genreRepo->findByToken($genre));
                }
                foreach ($bookInfo->author as $author) {
                    $authorCollection->add($authorRepo->findByDocumentID($author->id));
                }
                foreach ($bookInfo->{'art_tags'}->tag as $tag) {
                    $tagCollection->add($tagRepo->findByLitresId($tag['id']));
                }

                $book
                    ->setLitresHubId($data['hub_id'])
                    ->setType($data['type'])
                    ->setCover($data['cover'])
                    ->setCoverPreview($data['cover_preview'])
                    ->setFilename($data['file_id'])
                    ->setPrice($data['price'])
                    ->setRating($data['rating'])
                    ->setRecensesCount($data['recenses'])
                    ->setPrice($data['price'])
                    ->setHasTrial($data['has_trial'])
                    ->setType($data['type'])
                    ->setTitle($bookInfo->{'book-title'})
                    ->setAnnotation($bookInfo->annotation)
                    ->setDate($bookInfo->date['value'])
                    ->setDocumentUrl($documentInfo->{'src-url'})
                    ->setDocumentId($documentInfo->id)
                    ->setPublisher($publishInfo->annotation)
                    ->setYearPublished($publishInfo->annotation)
                    ->setCityPublished($publishInfo->annotation)
                    ->setIsbn($publishInfo->annotation)
                    ->setAuthor($authorCollection)
                    ->setGenre($genreCollection)
                    ->setTag($tagCollection)
                    ->setSequence($sequence)
                ;
            }
        }

    }
}