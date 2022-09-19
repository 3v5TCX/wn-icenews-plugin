<?php namespace IceCollection\News\Components;

use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use IceCollection\News\Models\Post as NewsPost;
use IceCollection\News\Models\Category as NewsCategory;

class Posts extends ComponentBase
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
    public $sortOrder;

    public function componentDetails()
    {
        return [
            'name'        => 'icecollection.news::lang.settings.posts_title',
            'description' => 'icecollection.news::lang.settings.posts_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'pageNumber' => [
                'title'       => 'icecollection.news::lang.settings.posts_pagination',
                'description' => 'icecollection.news::lang.settings.posts_pagination_description',
                'type'        => 'string',
                'default'     => '{{ :page }}',
            ],
            'categoryFilter' => [
                'title'       => 'icecollection.news::lang.settings.posts_filter',
                'description' => 'icecollection.news::lang.settings.posts_filter_description',
                'type'        => 'string',
                'default'     => ''
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
            'sortOrder' => [
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

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getSortOrderOptions()
    {
        return NewsPost::$allowedSortingOptions;
    }

    public function onRun()
    {
        $this->prepareVars();

        $this->category = $this->page['category'] = $this->loadCategory();
        $this->posts = $this->page['posts'] = $this->listPosts();

        /*
         * If the page number is not valid, redirect
         */
        if ($pageNumberParam = $this->paramName('pageNumber')) {
            $currentPage = $this->property('pageNumber');

            if ($currentPage > ($lastPage = $this->posts->lastPage()) && $currentPage > 1)
                return Redirect::to($this->currentPageUrl([$pageNumberParam => $lastPage]));
        }
    }

    protected function prepareVars()
    {
        $this->pageParam = $this->page['pageParam'] = $this->paramName('pageNumber');
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
            'page'       => $this->property('pageNumber'),
            'sort'       => $this->property('sortOrder'),
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
