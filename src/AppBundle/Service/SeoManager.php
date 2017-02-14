<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Entity\Author;
use AppBundle\Entity\PageInterface;
use AppBundle\Entity\Genre;
use AppBundle\Entity\Sequence;
use AppBundle\Entity\Tag;
use AppBundle\Model\SeoData;
use Sonata\SeoBundle\Seo\SeoPage;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;

class SeoManager
{
    /**
     * @var SeoPage
     */
    protected $seoPage;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param SeoPage    $seoPage
     * @param Translator $translator
     */
    public function __construct(SeoPage $seoPage, Translator $translator)
    {
        $this->seoPage    = $seoPage;
        $this->translator = $translator;
    }

    /**
     * @param SeoData $data
     */
    public function setBasicSeoData(SeoData $data)
    {
        $this->seoPage
            ->setTitle($data->getTitle())
            ->addMeta('name', 'description', $data->getMetaDescription())
            ->addMeta('name', 'keywords', $data->getMetaKeywords())
        ;

        if (!$data->isIndexPage()) {
            $this->seoPage->addMeta('name', 'robots', 'noindex');
        }
    }

    public function setHomeSeoData()
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.index_page.title'));
        $seoData->setMetaDescription($this->translator->trans('front.index_page.description'));
        $seoData->setMetaKeywords($this->translator->trans('front.index_page.keywords'));
        $this->setBasicSeoData($seoData);
    }

    public function setSearchSeoData()
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.search_page.title'));
        $seoData->setMetaDescription($this->translator->trans('front.search_page.description'));
        $seoData->setMetaKeywords($this->translator->trans('front.search_page.keywords'));
        $seoData->setIndexPage(false);
        $this->setBasicSeoData($seoData);
    }

    public function setNewBooksSeoData()
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.new_books_page.title'));
        $seoData->setMetaDescription($this->translator->trans('front.new_books_page.description'));
        $seoData->setMetaKeywords($this->translator->trans('front.new_books_page.keywords'));
        $this->setBasicSeoData($seoData);
    }

    public function setPopularBooksSeoData()
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.popular_books_page.title'));
        $seoData->setMetaDescription($this->translator->trans('front.popular_books_page.description'));
        $seoData->setMetaKeywords($this->translator->trans('front.popular_books_page.keywords'));
        $this->setBasicSeoData($seoData);
    }

    public function setTagsSeoData()
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.tags_page.title'));
        $seoData->setMetaDescription($this->translator->trans('front.tags_page.description'));
        $seoData->setMetaKeywords($this->translator->trans('front.tags_page.keywords'));
        $this->setBasicSeoData($seoData);
    }

    /**
     * @param Genre $genre
     */
    public function setGenreSeoData(Genre $genre)
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.genre_page.title', [
            '%genre_title%' => $genre->getTitle(),
        ]));
        $seoData->setMetaDescription($this->translator->trans('front.genre_page.description', [
            '%genre_title%' => $genre->getTitle(),
        ]));
        $seoData->setMetaKeywords($this->translator->trans('front.genre_page.keywords', [
            '%genre_title%' => $genre->getTitle(),
        ]));
        $this->setBasicSeoData($seoData);
    }

    /**
     * @param Author $author
     */
    public function setAuthorSeoData(Author $author)
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.author_page.title', [
            '%author_name%' => $author->getFullName(),
        ]));
        $seoData->setMetaDescription($this->translator->trans('front.author_page.description', [
            '%author_name%' => $author->getFullName(),
        ]));
        $seoData->setMetaKeywords($this->translator->trans('front.author_page.keywords', [
            '%author_name%' => $author->getFullName(),
        ]));
        $this->setBasicSeoData($seoData);
    }

    /**
     * @param array $book
     */
    public function setBookSeoData(array $book)
    {
        $seoData = new SeoData();
        $author  = $book['authors'][0];
        $seoData->setTitle($this->translator->trans('front.book_page.title', [
            '%book_title%'  => $book['title'],
            '%author_name%' => $author['full_name']
        ]));
        $seoData->setMetaDescription($this->translator->trans('front.book_page.description', [
            '%book_title%'  => $book['title'],
            '%author_name%' => $author['full_name']
        ]));
        $seoData->setMetaKeywords($this->translator->trans('front.book_page.keywords', [
            '%book_title%'  => $book['title'],
            '%author_name%' => $author['full_name']
        ]));
        $this->setBasicSeoData($seoData);
    }

    /**
     * @param Tag $tag
     */
    public function setTagSeoData(Tag $tag)
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.tag_page.title', [
            '%tag_title%' => $tag->getTitle(),
        ]));
        $seoData->setMetaDescription($this->translator->trans('front.tag_page.description', [
            '%tag_title%' => $tag->getTitle(),
        ]));
        $seoData->setMetaKeywords($this->translator->trans('front.tag_page.keywords', [
            '%tag_title%' => $tag->getTitle(),
        ]));
        $this->setBasicSeoData($seoData);
    }

    /**
     * @param Sequence $sequence
     */
    public function setSequenceSeoData(Sequence $sequence)
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.sequence_page.title', [
            '%sequence_name%' => $sequence->getName(),
        ]));
        $seoData->setMetaDescription($this->translator->trans('front.sequence_page.description', [
            '%sequence_name%' => $sequence->getName(),
        ]));
        $seoData->setMetaKeywords($this->translator->trans('front.sequence_page.keywords', [
            '%sequence_name%' => $sequence->getName(),
        ]));
        $this->setBasicSeoData($seoData);
    }

    /**
     * @param PageInterface|array $entity
     *
     * @return array
     */
    public function buildBreadcrumbs($entity)
    {
        $breadcrumbs[] = [
            'url'  => '/',
            'name' => $this->translator->trans('front.index_page.breadcrumb_title')
        ];

        if ($entity instanceof Sequence) {
            $name = $entity->getName();
        } elseif ($entity instanceof Genre || $entity instanceof Tag) {
            $name = $entity->getTitle();
        } elseif ($entity instanceof Author) {
            $name = $entity->getShortName();
        } else {
            $name = $entity['title'];
            if ($genre = array_shift($entity['genres'])) {
                $breadcrumbs[] = [
                    'url'  => '/' . $genre['path'],
                    'name' => $genre['title']
                ];
            }

            if ($author = array_shift($entity['authors'])) {
                $breadcrumbs[] = [
                    'url'  => '/' . $author['path'],
                    'name' => $author['short_name']
                ];
            }
        }

        $breadcrumbs[] = [
            'name' => $name
        ];

        return $breadcrumbs;
    }
}
