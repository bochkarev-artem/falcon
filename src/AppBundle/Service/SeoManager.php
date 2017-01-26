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
    public function setSeoData(SeoData $data)
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

    public function setIndexSeo()
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.index_page.title'));
        $seoData->setMetaDescription($this->translator->trans('front.index_page.description'));
        $seoData->setMetaKeywords($this->translator->trans('front.index_page.keywords'));
        $this->setSeoData($seoData);
    }

    public function setSearchSeo()
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.search_page.title'));
        $seoData->setMetaDescription($this->translator->trans('front.search_page.description'));
        $seoData->setMetaKeywords($this->translator->trans('front.search_page.keywords'));
        $seoData->setIndexPage(false);
        $this->setSeoData($seoData);
    }

    public function setNewBooksSeo()
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.new_books_page.keywords'));
        $seoData->setMetaDescription($this->translator->trans('front.new_books_page.description'));
        $seoData->setMetaKeywords($this->translator->trans('front.new_books_page.keywords'));
        $this->setSeoData($seoData);
    }

    /**
     * @param Genre $genre
     */
    public function setGenreSeo(Genre $genre)
    {
        $seoData = new SeoData();
        $seoData->setTitle($genre->getTitle());
        $seoData->setMetaDescription($genre->getTitle());
        $seoData->setMetaKeywords($genre->getTitle());
        $this->setSeoData($seoData);
    }

    public function setTagsSeo()
    {
        $seoData = new SeoData();
        $seoData->setTitle($this->translator->trans('front.tags_page.title'));
        $seoData->setMetaDescription($this->translator->trans('front.tags_page.description'));
        $seoData->setMetaKeywords($this->translator->trans('front.tags_page.keywords'));
        $this->setSeoData($seoData);
    }

    /**
     * @param Author $author
     */
    public function setAuthorSeo(Author $author)
    {
        $seoData = new SeoData();
        $seoData->setTitle($author->getFullName());
        $seoData->setMetaDescription($author->getFullName());
        $seoData->setMetaKeywords($author->getFullName());
        $this->setSeoData($seoData);
    }

    /**
     * @param array $book
     */
    public function setBookSeo(array $book)
    {
        $seoData = new SeoData();
        $seoData->setTitle($book['title']);
        $seoData->setMetaDescription($book['title']);
        $seoData->setMetaKeywords($book['title']);
        $this->setSeoData($seoData);
    }

    /**
     * @param Tag $tag
     */
    public function setTagSeo(Tag $tag)
    {
        $seoData = new SeoData();
        $seoData->setTitle($tag->getTitle());
        $seoData->setMetaDescription($tag->getTitle());
        $seoData->setMetaKeywords($tag->getTitle());
        $this->setSeoData($seoData);
    }

    /**
     * @param Sequence $sequence
     */
    public function setSequenceSeo(Sequence $sequence)
    {
        $seoData = new SeoData();
        $seoData->setTitle($sequence->getName());
        $seoData->setMetaDescription($sequence->getName());
        $seoData->setMetaKeywords($sequence->getName());
        $this->setSeoData($seoData);
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
