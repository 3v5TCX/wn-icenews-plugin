<?php namespace IceCollection\News\Components;

use Redirect;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use IceCollection\Sync\Models\Settings;

class RegionNewsPosts extends ComponentBase
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
    public $posts;

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
            'name'        => 'Новости регионального уровня',
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
            'noPostsMessage' => [
                'title'        => 'icecollection.news::lang.settings.posts_no_posts',
                'description'  => 'icecollection.news::lang.settings.posts_no_posts_description',
                'type'         => 'string',
                'default'      => 'No posts found'
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

    public function onRun()
    {
        $this->dep_url = $this->page['dep_url'] = Settings::get('dep_url');
        $this->posts = $this->page['posts'] = json_decode(file_get_contents($this->dep_url.'/storage/app/uploads/public/news.json'), true);
    }

}
