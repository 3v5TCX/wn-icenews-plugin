<?php namespace IceCollection\News\Components;

use App;
use Request;
use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use IceCollection\News\Models\Post as NewsPost;
use IceCollection\News\Models\Category as NewsCategory;

class Articles extends ComponentBase
{
    /**
     * A collection of posts to display
     * @var Collection
     */
    public $posts;

    /**
     * Parameter to use for the page number
     * @var string
     */
    public $pageParam;

    /**
     * If the post list should be filtered by a category, the model to use.
     * @var Model
     */
    public $category;

    /**
     * Message to display when there are no messages.
     * @var string
     */
    public $noPostsMessage;

    /**
     * Reference to the page name for linking to posts.
     * @var string
     */
    public $postPage;

    /**
     * Reference to the page name for linking to categories.
     * @var string
     */
    public $categoryPage;

    /**
     * If the post list should be ordered by another attribute.
     * @var string
     */
    public $postOrderAttr;

    public function componentDetails()
    {
        return [
            'name'        => 'Список статей',
            'description' => 'Добавить список статей'
        ];
    }

    public function defineProperties()
    {
        return [
            'pageParam' => [
                'title'       => 'icecollection.news::lang.settings.posts_pagination',
                'description' => 'icecollection.news::lang.settings.posts_pagination_description',
                'type'        => 'string',
                'default'     => ':page',
            ],
            'categoryFilter' => [
                'title'       => 'icecollection.news::lang.settings.posts_filter',
                'description' => 'icecollection.news::lang.settings.posts_filter_description',
                'type'        => 'dropdown'
            ],
            'postsPerPage' => [
                'title'             => 'icecollection.news::lang.settings.posts_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'icecollection.news::lang.settings.posts_per_page_validation',
                'default'           => '10',
            ],
            'noPostsMessage' => [
                'title'        => 'icecollection.news::lang.settings.posts_no_posts',
                'description'  => 'icecollection.news::lang.settings.posts_no_posts_description',
                'type'         => 'string',
                'default'      => 'No posts found'
            ],
            'postOrderAttr' => [
                'title'       => 'icecollection.news::lang.settings.posts_order',
                'description' => 'icecollection.news::lang.settings.posts_order_description',
                'type'        => 'dropdown',
                'default'     => 'published_at desc'
            ],
            'categoryPage' => [
                'title'       => 'icecollection.news::lang.settings.posts_category',
                'description' => 'icecollection.news::lang.settings.posts_category_description',
                'type'        => 'dropdown',
                'default'     => 'news/category',
                'group'       => 'Links',
            ],
            'postPage' => [
                'title'       => 'icecollection.news::lang.settings.posts_post',
                'description' => 'icecollection.news::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'news/post',
                'group'       => 'Links',
            ],
        ];
    }

    public function getCategoryFilterOptions()
    {
        return NewsCategory::select('slug')->orderBy('slug')->lists('slug', 'slug');
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getPostOrderAttrOptions()
    {
        return NewsPost::$allowedSortingOptions;
    }

    public function onRun()
    {
        $this->addCss('/plugins/icecollection/news/assets/css/normalize.css');
        $this->addCss('/plugins/icecollection/news/assets/css/set1.css');

        $this->prepareVars();

        $this->category = $this->page['category'] = $this->loadCategory();
        $this->posts = $this->page['posts'] = $this->listPosts();

        $currentPage = $this->property('pageParam');
        if ($currentPage > ($lastPage = $this->posts->getLastPage()) && $currentPage > 1)
            return Redirect::to($this->controller->currentPageUrl([$this->property('pageParam') => $lastPage]));
    }

    protected function prepareVars()
    {
        $this->pageParam = $this->page['pageParam'] = $this->property('pageParam', 'page');
        $this->noPostsMessage = $this->page['noPostsMessage'] = $this->property('noPostsMessage');

        /*
         * Page links
         */
        $this->postPage = $this->page['postPage'] = $this->property('postPage');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
    }

    protected function listPosts()
    {
        $categories = $this->category ? $this->category->id : null;

        /*
         * List all the posts, eager load their categories
         */
        $posts = NewsPost::with('categories')->listFrontEnd([
            'page'       => $this->property('pageParam'),
            'sort'       => $this->property('postOrderAttr'),
            'perPage'    => $this->property('postsPerPage'),
            'categories' => $categories
        ]);

        /*
         * Add a "url" helper attribute for linking to each post and category
         */
        $posts->each(function($post){
            $post->setUrl($this->postPage, $this->controller);

            $post->categories->each(function($category){
                $category->setUrl($this->categoryPage, $this->controller);
            });
        });

        return $posts;
    }

    protected function loadCategory()
    {
        if (!$categoryId = $this->property('categoryFilter'))
            return null;

        if (!$category = NewsCategory::whereSlug($categoryId)->first())
            return null;

        return $category;
    }
}
