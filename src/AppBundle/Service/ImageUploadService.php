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
     * @var UploaderHelper
     */
    private $uploaderHelper;

    /**
     * @param Filesystem     $filesystem
     * @param UploaderHelper $uploaderHelper
     * @param string         $bookFullMapping
     * @param string         $bookPreviewMapping
     * @param string         $authorMapping
     */
    public function __construct(
        Filesystem $filesystem,
        UploaderHelper $uploaderHelper,
        $bookFullMapping,
        $bookPreviewMapping,
        $authorMapping
    )
    {
        $this->s3Filesystem   = $filesystem;
        $this->uploaderHelper = $uploaderHelper;
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
        if (!$book->getCoverPreviewUrl() && !$this->s3Filesystem->has($path)) {
            $this->s3Filesystem->write($path, $fileContent);
            $book->setCoverPreviewName($fileName);
            $url = $this->uploaderHelper->asset($book, 'coverPreviewFile');
            $book->setCoverPreviewUrl($url);
        }

        if (!$previewOnly) {
            $coverUrl    = $book->getCover();
            $fileContent = @file_get_contents($coverUrl);
            if (false === $fileContent) {
                return false;
            }

            $fileName = basename($coverUrl);
            $path     = "$this->fullMapping/" . basename($fileName);
            if (!$book->getCoverUrl() && !$this->s3Filesystem->has($path)) {
                $this->s3Filesystem->write($path, $fileContent);
                $book->setCoverName($fileName);
                $url = $this->uploaderHelper->asset($book, 'coverFile');
                $book->setCoverUrl($url);
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
        if (!$author->getPhotoUrl() && !$this->s3Filesystem->has($path)) {
            $this->s3Filesystem->write($path, $fileContent);
            $author->setPhotoName($fileName);
            $url = $this->uploaderHelper->asset($author, 'photoFile');
            $author->setPhotoUrl($url);
        }

        return true;
    }
}