<?php
/**
 * @author Artem Bochkarev
 */
namespace AppBundle\Service;

use AppBundle\Entity\Book;
use Knp\Bundle\GaufretteBundle\FilesystemMap;

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
     * @param FilesystemMap $filesystemMap
     */
    public function __construct(FilesystemMap $filesystemMap)
    {
        $this->s3Filesystem = $filesystemMap->get('s3_upload_fs');
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

        $coverPreviewName = $book->getCoverPreviewName();
        if (!$coverPreviewName || !$this->s3Filesystem->has($coverPreviewName)) {
            $fileName = basename($coverPreviewUrl);
            $this->s3Filesystem->write($fileName, $fileContent);
            $book->setCoverPreviewName($fileName);
        }

        if (!$previewOnly) {
            $coverUrl    = $book->getCover();
            $fileContent = @file_get_contents($coverUrl);
            if (false === $fileContent) {
                return false;
            }

            $coverName = $book->getCoverName();
            if (!$coverName || !$this->s3Filesystem->has($coverName)) {
                $fileName = basename($coverUrl);
                $this->s3Filesystem->write($fileName, $fileContent);
                $book->setCoverName($fileName);
            }
        }

        return true;
    }
}