<?php namespace IceCollection\News\Components;

use App;
use Request;
use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use IceCollection\Sync\Models\Settings;

class RegionNewsFeed extends ComponentBase
{
    /**
     * Url of region site
     * @var string
     */
    public $dep_url;

    /**
     * A collection of posts to display
     * @var Collection
     */
    public $region_posts;

    /**
     * Parameter to use for the page number
     * @var string
     */
    public $pageParam;

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

    public function componentDetails()
    {
        return [
            'name'        => 'Новости и события регионального уровня',
            'description' => 'Новости в виде слайдшоу'
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
            'imgShowParam' => [
                'title'       => 'Выводить картинку для новости?',
                'description' => 'Показывать или нет картинки в новостях',
                'type'        => 'checkbox',
                'default'     => true
            ],
            'noPostsMessage' => [
                'title'        => 'icecollection.news::lang.settings.posts_no_posts',
                'description'  => 'icecollection.news::lang.settings.posts_no_posts_description',
                'type'         => 'string',
                'default'      => 'Не найдено ни одной новости'
            ],
            'postPage' => [
                'title'       => 'icecollection.news::lang.settings.posts_post',
                'description' => 'icecollection.news::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'news/post',
                'group'       => 'Ссылки',
            ],
            'newsPage' => [
                'title'       => 'Страница новостей',
                'description' => 'Ссылка на страницу новостей',
                'type'        => 'dropdown',
                'default'     => 'news/post',
                'group'       => 'Ссылки',
            ],
        ];
    }

    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getNewsPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->page['regionNewsPage'] = $this->property('newsPage');
        $this->dep_url = $this->page['dep_url'] = Settings::get('dep_url');
        $this->region_posts = $this->page['region_posts'] = json_decode(file_get_contents($this->dep_url.'/storage/app/uploads/public/news.json'), true);
    }

}
