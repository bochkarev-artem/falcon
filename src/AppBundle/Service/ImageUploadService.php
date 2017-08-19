<?php
/**
 * @author Artem Bochkarev
 */
namespace AppBundle\Service;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use League\Flysystem\Filesystem;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Class ImageUploadService
 * @package AppBundle\Service
 */
class ImageUploadService
{
    /**
     * @var string
     */
    private $s3Filesystem;

    /**
     * @var string
     */
    private $bookMapping;

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    /**
     * @param Filesystem     $filesystem
     * @param UploaderHelper $uploaderHelper
     * @param string         $bookMapping
     */
    public function __construct(
        Filesystem $filesystem,
        UploaderHelper $uploaderHelper,
        $bookMapping
    ) {
        $this->s3Filesystem   = $filesystem;
        $this->uploaderHelper = $uploaderHelper;
        $this->bookMapping    = $bookMapping;
    }

    /**
     * @param Book $book
     *
     * @return boolean
     */
    public function updateBookCover(Book $book)
    {
        if (!$coverUrl = $book->getCover()) {
            return false;
        }

        $path = "$this->bookMapping/" . basename($coverUrl);
        if (!$this->s3Filesystem->has($path)) {
            $fileContent = @file_get_contents($coverUrl);
            if (false === $fileContent) {
                return false;
            }
            $bookId = $book->getId();
            echo ">>> $bookId book updated with photo\n";

            $this->s3Filesystem->write($path, $fileContent);
        }

        $book->setCoverPath($path);

        return true;
    }
}