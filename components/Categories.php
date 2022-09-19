<?php namespace IceCollection\News\Components;

use DB;
use App;
use Request;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use IceCollection\News\Models\Category as NewsCategory;

class Categories extends ComponentBase
{
    /**
     * @var Collection A collection of categories to display
     */
    public $categories;

    /**
     * @var string Reference to the page name for linking to categories.
     */
    public $categoryPage;

    /**
     * @var string Reference to the current category slug.
     */
    public $currentCategorySlug;

    public function componentDetails()
    {
        return [
            'name'        => 'icecollection.news::lang.settings.category_title',
            'description' => 'icecollection.news::lang.settings.category_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'idParam' => [
                'title'       => 'icecollection.news::lang.settings.category_slug',
                'description' => 'icecollection.news::lang.settings.category_slug_description',
                'default'     => ':slug',
                'type'        => 'string'
            ],
            'displayEmpty' => [
                'title'       => 'icecollection.news::lang.settings.category_display_empty',
                'description' => 'icecollection.news::lang.settings.category_display_empty_description',
                'type'        => 'checkbox',
                'default'     => 0
            ],
            'categoryPage' => [
                'title'       => 'icecollection.news::lang.settings.category_page',
                'description' => 'icecollection.news::lang.settings.category_page_description',
                'type'        => 'dropdown',
                'default'     => 'news/category',
                'group'       => 'Links',
            ],
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->currentCategorySlug = $this->page['currentCategorySlug'] = $this->property('idParam');
        $this->categories = $this->page['categories'] = $this->loadCategories();
    }

    protected function loadCategories()
    {
        $categories = NewsCategory::orderBy('name');
        if (!$this->property('displayEmpty')) {
            $categories->whereExists(function($query) {
                $query->select(DB::raw(1))
                ->from('icecollection_news_posts_categories')
                ->join('icecollection_news_posts', 'icecollection_news_posts.id', '=', 'icecollection_news_posts_categories.post_id')
                ->whereNotNull('icecollection_news_posts.published')
                ->whereRaw('icecollection_news_categories.id = icecollection_news_posts_categories.category_id');
            });
        }

        $categories = $categories->get();

        /*
         * Add a "url" helper attribute for linking to each category
         */
        $categories->each(function($category){
            $category->setUrl($this->categoryPage, $this->controller);
        });

        return $categories;
    }
}
