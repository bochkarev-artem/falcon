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
     * @var string
     */
    private $authorMapping;

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    /**
     * @param Filesystem     $filesystem
     * @param UploaderHelper $uploaderHelper
     * @param string         $bookMapping
     * @param string         $authorMapping
     */
    public function __construct(
        Filesystem $filesystem,
        UploaderHelper $uploaderHelper,
        $bookMapping,
        $authorMapping
    ) {
        $this->s3Filesystem   = $filesystem;
        $this->uploaderHelper = $uploaderHelper;
        $this->bookMapping    = $bookMapping;
        $this->authorMapping  = $authorMapping;
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

        $fileName = basename($coverUrl);
        $path     = "$this->bookMapping/" . basename($fileName);

        if (!$this->s3Filesystem->has($path)) {
            $fileContent = @file_get_contents($coverUrl);
            if (false === $fileContent) {
                return false;
            }
            $bookId = $book->getId();
            echo ">>> $bookId book updated with photo\n";

            $this->s3Filesystem->write($path, $fileContent);
            $book->setCoverName($fileName);
            $book->setCoverPath($path);
        }

        return true;
    }

    /**
     * @param Author $author
     *
     * @return boolean
     */
    public function updateAuthorPhoto(Author $author)
    {
        if (!$photoUrl = $author->getPhoto()) {
            return false;
        }

        $fileName = basename($photoUrl);
        $path     = "$this->authorMapping/" . $fileName;

        if (!$this->s3Filesystem->has($path)) {
            $fileContent = @file_get_contents($photoUrl);
            if (false === $fileContent) {
                return false;
            }
            $authorId = $author->getId();
            echo ">>> $authorId author updated with photo\n";

            $this->s3Filesystem->write($path, $fileContent);
            $author->setPhotoName($fileName);
            $author->setPhotoPath($path);
        }

        return true;
    }
}