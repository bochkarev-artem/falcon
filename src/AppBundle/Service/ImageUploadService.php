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
     * @var string
     */
    private $localFilesystem;

    /**
     * @param FilesystemMap $filesystemMap
     */
    public function __construct(FilesystemMap $filesystemMap)
    {
        $this->s3Filesystem    = $filesystemMap->get('s3_upload_fs');
        $this->localFilesystem = $filesystemMap->get('local_upload_fs');
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
        if (!$book->getCoverPreviewName()) {
            $fileName = $book->getLitresHubId() . '.jpeg';
            $this->localFilesystem->write($fileName, $fileContent);
            $book->setCoverPreviewName($fileName);
        }

        if (!$previewOnly) {
            $coverUrl    = $book->getCover();
            $fileContent = @file_get_contents($coverUrl);
            if (false === $fileContent) {
                return false;
            }
            if (!$book->getCoverName()) {
                $fileName = $book->getLitresHubId() . '.jpeg';
                $this->s3Filesystem->write($fileName, $fileContent);
                $book->setCoverName($fileName);
            }
        }

        return true;
    }
}