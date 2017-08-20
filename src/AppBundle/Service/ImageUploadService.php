<?php
/**
 * @author Artem Bochkarev
 */
namespace AppBundle\Service;

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
    private $bookMapping;

    /**
     * @param Filesystem $filesystem
     * @param string     $bookMapping
     */
    public function __construct(
        Filesystem $filesystem,
        $bookMapping
    ) {
        $this->s3Filesystem = $filesystem;
        $this->bookMapping  = $bookMapping;
    }

    /**
     * @param Book $book
     */
    public function updateBookCover(Book $book)
    {
        if (!$coverUrl = $book->getCover()) {
            return;
        }

        $path = "$this->bookMapping/" . basename($coverUrl);
        if (!$this->s3Filesystem->has($path)) {
            $fileContent = @file_get_contents($coverUrl);
            if (false === $fileContent) {
                return;
            }

            $this->s3Filesystem->write($path, $fileContent);
        }

        $book->setCoverPath($path);
    }
}
