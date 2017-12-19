<?php
/**
 * @author Artem Bochkarev
 */

namespace AppBundle\Service;

use AppBundle\Entity\Author;
use AppBundle\Entity\Genre;
use AppBundle\Entity\PageInterface;
use AppBundle\Entity\Sequence;
use AppBundle\Entity\Tag;
use AppBundle\Model\SeoData;
use Sonata\SeoBundle\Seo\SeoPage;
use Symfony\Component\Translation\TranslatorInterface;

class SeoManager
{
    /**
     * @var SeoPage
     */
    protected $seoPage;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param SeoPage $seoPage
     * @param TranslatorInterface $translator
     */
    public function __construct(
        SeoPage $seoPage,
        TranslatorInterface $translator
    ) {
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

    public function setUserProfileRatingsSeoData()
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.user_profile_ratings.title'));
        $this->setBasicSeoData($seoData);
    }

    public function setUserProfileReviewsSeoData()
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.user_profile_reviews.title'));
        $this->setBasicSeoData($seoData);
    }

    public function setUserProfileStatsSeoData()
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.user_profile_stats.title'));
        $this->setBasicSeoData($seoData);
    }

    public function setUserProfileSeoData()
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.user_profile.title'));
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

    /**
     * @param int $page
     */
    public function setNewBooksSeoData($page)
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.new_books_page.title'));
        $seoData->setMetaDescription($this->translator->trans('front.new_books_page.description'));
        $seoData->setMetaKeywords($this->translator->trans('front.new_books_page.keywords'));
        $doIndex = $page === 1;
        $seoData->setIndexPage($doIndex);

        $this->setBasicSeoData($seoData);
    }

    /**
     * @param int $page
     */
    public function setPopularBooksSeoData($page)
    {
        $doIndex = $page === 1;
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.popular_books_page.title'));
        $seoData->setMetaDescription($this->translator->trans('front.popular_books_page.description'));
        $seoData->setMetaKeywords($this->translator->trans('front.popular_books_page.keywords'));
        $seoData->setIndexPage($doIndex);

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
     * @param Genre   $genre
     * @param int $page
     */
    public function setGenreSeoData(Genre $genre, $page)
    {
        $seoData = new SeoData();
        $title   = $genre->getTitle();
        $seoData->setTitle($this->translator->trans('front.genre_page.title', [
            '%genre_title%' => $title,
        ]));
        $seoData->setMetaDescription($this->translator->trans('front.genre_page.description', [
            '%genre_title%' => $title,
        ]));
        $seoData->setMetaKeywords($this->translator->trans('front.genre_page.keywords', [
            '%genre_title%' => $title,
        ]));
        $doIndex = $page === 1;
        $seoData->setIndexPage($doIndex);

        $this->setBasicSeoData($seoData);
    }

    /**
     * @param Author $author
     * @param int $page
     */
    public function setAuthorSeoData(Author $author, $page)
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
        $doIndex = $page === 1;
        $seoData->setIndexPage($doIndex);

        $this->setBasicSeoData($seoData);
    }

    /**
     * @param array $book
     */
    public function setBookSeoData(array $book)
    {
        $seoData    = new SeoData();
        $title      = $book['title'];
        $authorName = $book['authors'] ? $book['authors'][0]['full_name'] : '';
        $seoData->setTitle($this->translator->trans('front.book_page.title', [
            '%book_title%'  => $title,
            '%author_name%' => $authorName,
        ]));
        $seoData->setMetaDescription($this->translator->trans('front.book_page.description', [
            '%book_title%'  => $title,
            '%author_name%' => $authorName,
        ]));
        $seoData->setMetaKeywords($this->translator->trans('front.book_page.keywords', [
            '%book_title%'  => $title,
            '%author_name%' => $authorName,
        ]));
        $this->setBasicSeoData($seoData);
    }

    /**
     * @param Tag $tag
     * @param int $page
     */
    public function setTagSeoData(Tag $tag, $page)
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
        $doIndex = $page === 1;
        $seoData->setIndexPage($doIndex);

        $this->setBasicSeoData($seoData);
    }

    /**
     * @param Sequence $sequence
     * @param int $page
     */
    public function setSequenceSeoData(Sequence $sequence, $page)
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
        $doIndex = $page === 1;
        $seoData->setIndexPage($doIndex);

        $this->setBasicSeoData($seoData);
    }

    /**
     * @param array|PageInterface $entity
     *
     * @return array
     */
    public function buildBreadcrumbs($entity) // TODO maybe refactor this to adhere Liskov and Open-Closed principles
    {
        $breadcrumbs[] = [
            'url'  => '/',
            'name' => $this->translator->trans('front.index_page.breadcrumb_title'),
        ];

        if ($entity instanceof Sequence) {
            $name = $entity->getName();
        } elseif ($entity instanceof Genre) {
            $name          = $entity->getTitle();
            $breadcrumbs[] = [
                'name' => $entity->getParent()->getTitle(),
            ];
        } elseif ($entity instanceof Tag) {
            $name = $entity->getTitle();
        } elseif ($entity instanceof Author) {
            $name = $entity->getShortName();
        } else {
            $name = $entity['title'];
            if ($genre = array_shift($entity['genres'])) {
                $genreTitle    = $genre['title'];
                $genrePath     = $genre['path'];
                $breadcrumbs[] = [
                    'url'  => '/' . $genrePath,
                    'name' => $genreTitle,
                ];
            }

            if ($author = array_shift($entity['authors'])) {
                $breadcrumbs[] = [
                    'url'  => '/' . $author['path'],
                    'name' => $author['short_name'],
                ];
            }
        }

        $breadcrumbs[] = [
            'name' => $name,
        ];

        return $breadcrumbs;
    }
}
