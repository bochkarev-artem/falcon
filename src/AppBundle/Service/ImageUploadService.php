<?php
/**
 * @author Artem Bochkarev
 */
namespace AppBundle\Service;

use AppBundle\Entity\Author;
use AppBundle\Entity\Book;
use League\Flysystem\Filesystem;

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
    private $fullMapping;

    /**
     * @var string
     */
    private $previewMapping;

    /**
     * @var string
     */
    private $authorMapping;

    /**
     * @param Filesystem $filesystem
     * @param string $bookFullMapping;
     * @param string $bookPreviewMapping;
     * @param string $authorMapping
     */
    public function __construct(Filesystem $filesystem, $bookFullMapping, $bookPreviewMapping, $authorMapping)
    {
        $this->s3Filesystem   = $filesystem;
        $this->fullMapping    = $bookFullMapping;
        $this->previewMapping = $bookPreviewMapping;
        $this->authorMapping  = $authorMapping;
    }

    /**
     * @param Book    $book
     * @param boolean $previewOnly
     *
     * @return boolean
     */
    public function updateBookCover(Book $book, $previewOnly)
    {
        $coverPreviewUrl = $book->getCoverPreview();
        $fileContent     = @file_get_contents($coverPreviewUrl);
        if (false === $fileContent) {
            return false;
        }

        $fileName = basename($coverPreviewUrl);
        $path     = "$this->previewMapping/" . $fileName;
        if (!$book->getCoverPreviewName() && !$this->s3Filesystem->has($path)) {
            $this->s3Filesystem->write($path, $fileContent);
            $book->setCoverPreviewName($fileName);
        }

        if (!$previewOnly) {
            $coverUrl    = $book->getCover();
            $fileContent = @file_get_contents($coverUrl);
            if (false === $fileContent) {
                return false;
            }

            $fileName = basename($coverUrl);
            $path     = "$this->fullMapping/" . $fileName;
            if (!$book->getCoverName() && !$this->s3Filesystem->has($path)) {
                $fileName = basename($coverUrl);
                $this->s3Filesystem->write($path, $fileContent);
                $book->setCoverName($fileName);
            }
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
        $photoUrl    = $author->getPhoto();
        $fileContent = @file_get_contents($photoUrl);
        if (false === $fileContent) {
            return false;
        }

        $fileName = basename($photoUrl);
        $path     = "$this->authorMapping/" . $fileName;
        if (!$author->getPhotoName() && !$this->s3Filesystem->has($path)) {
            $this->s3Filesystem->write($path, $fileContent);
            $author->setPhotoName($fileName);
        }

        return true;
    }
}