<?php namespace IceCollection\News\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use IceCollection\News\Models\Post as NewsPost;

class Post extends ComponentBase
{
    /**
     * @var IceCollection\News\Models\Post The post model used for display.
     */
    public $post;

    /**
     * @var string Reference to the page name for linking to categories.
     */
    public $categoryPage;

    public function componentDetails()
    {
        return [
            'name'        => 'icecollection.news::lang.settings.post_title',
            'description' => 'icecollection.news::lang.settings.post_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title'       => 'icecollection.news::lang.settings.post_slug',
                'description' => 'icecollection.news::lang.settings.post_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string'
            ],
            'categoryPage' => [
                'title'       => 'icecollection.news::lang.settings.post_category',
                'description' => 'icecollection.news::lang.settings.post_category_description',
                'type'        => 'dropdown',
                'default'     => 'news/category',
                'group'       => 'Ссылки',
            ],
            'newsPage' => [
                'title'       => 'Страница новостей',
                'description' => 'Ссылка на страницу новостей',
                'type'        => 'dropdown',
                'group'       => 'Ссылки',
            ],
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getNewsPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->newsPage = $this->page['newsPage'] = $this->property('newsPage');
        $this->post = $this->page['post'] = $this->loadPost();
    }

    protected function loadPost()
    {
        $slug = $this->property('slug');
        $post = NewsPost::isPublished()->where('slug', '=', $slug)->first();

        /*
         * Add a "url" helper attribute for linking to each category
         */
        if ($post && $post->categories->count()) {
            $post->categories->each(function($category){
                $category->setUrl($this->categoryPage, $this->controller);
            });
        }

        return $post;
    }
}
