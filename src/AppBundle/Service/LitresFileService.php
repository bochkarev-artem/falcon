<?php
/**
 * @author Artem Bochkarev
 */
namespace AppBundle\Service;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Sequence;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Monolog\Logger;
use Psr\Log\LogLevel;

/**
 * Class LitresService // TODO need to combine with LitresService
 * @package AppBundle\Service
 */
class LitresFileService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $authorRepo;

    /**
     * @var EntityRepository
     */
    private $genreRepo;

    /**
     * @var EntityRepository
     */
    private $sequenceRepo;

    /**
     * @var EntityRepository
     */
    private $bookRepo;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var int $batchSize
     */
    private $batchSize = 100;

    /**
     * @var bool $debug
     */
    private $debug;

    /**
     * @var string $rootDir
     */
    private $xmlFile;

    /**
     * @var array $locales
     */
    private $locales;

    /**
     * @param EntityManager $em
     * @param Logger        $logger
     * @param string        $rootDir
     * @param array         $locales
     */
    public function __construct(EntityManager $em, Logger $logger, $rootDir, $locales)
    {
        $this->em           = $em;
        $this->logger       = $logger;
        $this->xmlFile      = $rootDir . '/../web/detailed_data.xml';
        $this->authorRepo   = $this->em->getRepository('AppBundle:Author');
        $this->genreRepo    = $this->em->getRepository('AppBundle:Genre');
        $this->sequenceRepo = $this->em->getRepository('AppBundle:Sequence');
        $this->bookRepo     = $this->em->getRepository('AppBundle:Book');
        $this->locales      = $locales;
    }

    /**
     * @param string  $param
     * @param string  $debug
     *
     * @return bool
     */
    public function getData($param, $debug)
    {
        $this->debug = $debug == 'y' ? true : false;

        switch ($param) {
            case 'books':
                return $this->getBooksData();
                break;
            default:
                return false;
        }
    }

    /**
     * @param \SimpleXMLElement $sequence
     *
     * @return Sequence|null
     */
    public function getSequence($sequence)
    {
        $sequenceId   = (integer) $sequence['id'];
        $sequenceName = (string) $sequence['name'];
        $sequence     = $this->sequenceRepo->findOneBy(['litresName' => $sequenceName]);

        if (!$sequence && $sequenceId) {
            $sequence = new Sequence();
            $sequence->setLitresId($sequenceId);
            $sequence->setName($sequenceName);
            if (preg_match("/[у|е|ы|а|о|э|я|и|ю]/", $sequenceName)) {
                $sequence->setLang('ru');
            } else {
                $sequence->setLang('en');
            }

            $this->em->persist($sequence);
            $this->em->flush();
        }

        return $sequence;
    }

    /**
     * @param string $authorId
     * @param \SimpleXMLElement $subject
     *
     * @return Author|false
     */
    public function getAuthor($authorId, $subject)
    {
        $author = $this->authorRepo->findOneBy(['documentId' => $authorId]);
        if (!$author) {
            $fName = (string) $subject->{'first-name'};
            $mName = (string) $subject->{'middle-name'};
            $lName = (string) $subject->{'last-name'};

            if (!($fName || $mName || $lName)) { // no real name
                return false;
            }

            if (strlen($fName) > 250 || strlen($lName) > 250) {
                return false;
            }

            $author = new Author;
            $author
                ->setDocumentId((string) $authorId)
                ->setFirstName($fName)
                ->setMiddleName($mName)
                ->setLastName($lName)
            ;

            if (preg_match("/[у|е|ы|а|о|э|я|и|ю]/", $fName . $lName)) {
                $author->setLang('ru');
            } else {
                $author->setLang('en');
            }

            $this->em->persist($author);
            $this->em->flush();
        }

        return $author;
    }

    /**
     * @param string $genreToken
     *
     * @return Genre|null
     */
    public function getGenre($genreToken)
    {
        $genre = $this->genreRepo->findOneBy(['token' => $genreToken]);

        return $genre;
    }

    /**
     * @throws \ErrorException
     * @return bool
     */
    public function getBooksData()
    {
        $skipped = 0;
        $step    = 0;
        $xmlReader = new \XMLReader();
        $xmlReader->open($this->xmlFile);
        $first = true;
        while ($xmlReader->read() && $xmlReader->name !== 'art');
        while ($xmlReader->name === 'art') {
            if ($first) {
                $first = false;
                for ($i = 0; $i < 737900; $i++) {
                    $this->goToNextNode($xmlReader);
                }
            }
            $step++;
            $node = $xmlReader->readOuterXML();
            $data = $this->getXml($node);
            if (!$data) {
                echo "skip $step no book data\n";
                $this->goToNextNode($xmlReader);
                continue;
            }
            $hubId = (string)$data['int_id'];
            if ($book = $this->bookRepo->findOneBy(['litresHubId' => $hubId])) {
                $skipped++;
                $this->goToNextNode($xmlReader);
                continue;
            }

            $annotation = '';
            $book = new Book;
            $hidden = $data->{'text_description'}->hidden;
            if (!$hidden) {
                $skipped++;
                echo "skip $step no desc\n";
                $this->goToNextNode($xmlReader);
                continue;
            }
            $titleInfo = $hidden->{'title-info'};
            $lang = (string)$titleInfo->lang;
            if (strlen($lang) != 0 && !in_array($lang, $this->locales)) {
                $skipped++;
                echo "skip $step wrong lang $lang\n";
                $this->goToNextNode($xmlReader);
                continue;
            }
            $documentInfo = $hidden->{'document-info'};
            $publishInfo = $hidden->{'publish-info'};

            $author = null;
            foreach ($titleInfo->author as $author) {
                $authorId = $author->id;
                $author = $this->getAuthor($authorId, $author);
                if ($author) {
                    $book->addAuthor($author);
                } else {
                    if ($this->logger && $this->debug) {
                        $this->logger->log(
                            LogLevel::DEBUG,
                            sprintf('Author %s doesnt have name', $authorId)
                        );
                    }

                    $skipped++;
                    echo "skip $step no author\n";
                    $this->goToNextNode($xmlReader);

                    continue 2;
                }
            }

            if ($author === null) {
                $skipped++;
                echo "skip $step no author\n";
                $this->goToNextNode($xmlReader);
                continue;
            }

            $genres = [];
            foreach ($titleInfo->genre as $token) {
                $token = (string)$token;
                $genres[$token] = $token; // To exclude duplicated
            }

            foreach ($genres as $token) {
                $genre = $this->getGenre($token);
                if ($genre) {
                    $book->addGenre($genre);
                } else {
                    if ($this->logger && $this->debug) {
                        $this->logger->log(
                            LogLevel::DEBUG,
                            sprintf('Genre %s not found', $genre)
                        );
                    }

                    $skipped++;
                    echo "skip $step no genre\n";
                    $this->goToNextNode($xmlReader);

                    continue 2;
                }
            }

            if ($data->{'sequence'}) {
                foreach ($data->{'sequence'} as $sequence) {
                    $sequenceNumber = (integer)$sequence['number'];
                    $sequence = $this->getSequence($sequence);
                    if ($sequence) {
                        $book->setSequence($sequence);
                        $book->setSequenceNumber($sequenceNumber);
                        break;
                    }
                }
            }

            if ($titleInfo->annotation) {
                $annotation = strip_tags($titleInfo->annotation->asXML(), '<p><br>');
            }

            /** @var Author $mainAuthor */
            $mainAuthor = $book->getAuthors()->first();
            if (!($mainAuthor instanceof Author)) {
                echo "skip $step no author\n";
                $this->goToNextNode($xmlReader);
                continue;
            }

            $cover = $this->getCover((string)$data['file_id']);
            $title = substr((string)$titleInfo->{'book-title'}, 0, 254);
            if (preg_match("/[у|е|ы|а|о|э|я|и|ю]/", $title)) {
                $bookLocale = 'ru';
            } else {
                $bookLocale = 'en';
            }

            $sequence = $book->getSequence();
            if ($sequence && $sequence->getLang() == 'ru' || $mainAuthor->getLang() == 'ru' || $bookLocale == 'ru') {
                $book->setLang('ru');
            } else {
                $book->setLang('en');
            }

            $book
                ->setLitresHubId($hubId)
                ->setCover($cover)
                ->setPrice((string)$data['price'])
                ->setTitle(mb_convert_encoding($title, 'utf-8'))
                ->setAnnotation($annotation)
                ->setDocumentId((string)$documentInfo->id)
                ->setPublisher((string)$publishInfo->publisher)
                ->setCityPublished((string)$publishInfo->city)
                ->setIsbn((string)$publishInfo->isbn)
                ->setMainAuthorSlug($mainAuthor->getSlug())
            ;

            $yearPublished = (string)$publishInfo->year;
            if (strlen($yearPublished) < 5) {
                $book->setYearPublished($yearPublished);
            }
            $date = (string)$titleInfo->date['value'];
            if ($date && $date != '0000-00-00') {
                $date = new \DateTime($date);
                $book->setDate($date);
            }

            $this->em->persist($book);
            if ($step % $this->batchSize === 0) {
                $this->em->flush();
                $this->em->clear();
                if ($this->debug) {
                    echo ">>> books processed ($step), skipped ($skipped)\n";
                }
            }

            $this->goToNextNode($xmlReader);
        }

        $this->em->flush();
        $this->em->clear();

        return true;
    }

    /**
     * @param string $fileId
     *
     * @return string
     */
    private function getCover($fileId)
    {
        $litres8DigitId = $this->get8DigitCode($fileId);
        $litres6DigitId = $this->get6DigitCode($litres8DigitId);

        return sprintf("http://robot.litres.ru/static/bookimages/%s/%s.bin.dir/%s.cover.jpg", $litres6DigitId, $litres8DigitId, $litres8DigitId);
    }

    /**
     * @param \XMLReader $xmlReader
     */
    private function goToNextNode($xmlReader)
    {
        $xmlReader->next('art');
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
     * @param string $litresId
     *
     * @return string
     */
    protected function get8DigitCode($litresId)
    {
        $requireToAdd = 8 - strlen($litresId);

        return $this->prependZeros($litresId, $requireToAdd);
    }

    /**
     * @param string $xml
     *
     * @return \SimpleXMLElement
     */
    private function getXml($xml)
    {
        return simplexml_load_string($xml);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function mbUcfirstOnly($string)
    {
        $string = mb_strtolower($string);

        return $this->mbUcfirst($string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function mbUcfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_substr($string, 1);
    }
}
