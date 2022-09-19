<?php namespace IceCollection\News;

use Backend;
use Controller;
use System\Classes\PluginBase;
use IceCollection\News\Classes\TagProcessor;
use IceCollection\News\Models\Category;
use Event;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'icecollection.news::lang.plugin.name',
            'description' => 'icecollection.news::lang.plugin.description',
            'author'      => 'icecollection.news::lang.plugin.author',
            'icon'        => 'icon-pencil',
        ];
    }

    public function registerComponents()
    {
        return [
            'IceCollection\News\Components\Post'            => 'newsPost',
            'IceCollection\News\Components\Posts'           => 'newsPosts',
            'IceCollection\News\Components\Categories'      => 'newsCategories',
            'IceCollection\News\Components\Newsfeed'        => 'newsNewsfeed',
            'IceCollection\News\Components\RegionNewsPosts' => 'regionNewsPosts',
            'IceCollection\News\Components\RegionNewsFeed'  => 'regionNewsFeed'
        ];
    }

    public function registerPermissions()
    {
        return [
            'icecollection.news.access_posts'       => ['tab' => 'icecollection.news::lang.news.tab', 'label' => 'icecollection.news::lang.news.access_posts'],
            'icecollection.news.access_categories'  => ['tab' => 'icecollection.news::lang.news.tab', 'label' => 'icecollection.news::lang.news.access_categories'],
            'icecollection.news.access_other_posts' => ['tab' => 'icecollection.news::lang.news.tab', 'label' => 'icecollection.news::lang.news.access_other_posts']
        ];
    }

    public function registerNavigation()
    {
        return [
            'news' => [
                'label'       => 'icecollection.news::lang.news.menu_label',
                'url'         => Backend::url('icecollection/news/posts'),
                'icon'        => 'icon-pencil',
                'permissions' => ['icecollection.news.*'],
                'order'       => 500,

                'sideMenu' => [
                    'posts' => [
                        'label'       => 'icecollection.news::lang.news.posts',
                        'icon'        => 'icon-copy',
                        'url'         => Backend::url('icecollection/news/posts'),
                        'permissions' => ['icecollection.news.access_posts']
                    ],
                    'categories' => [
                        'label'       => 'icecollection.news::lang.news.categories',
                        'icon'        => 'icon-list-ul',
                        'url'         => Backend::url('icecollection/news/categories'),
                        'permissions' => ['icecollection.news.access_categories']
                    ],
                ]
            ]
        ];
    }

    public function registerFormWidgets()
    {
        return [
            'IceCollection\News\FormWidgets\Preview' => [
                'label' => 'Preview',
                'code'  => 'preview'
            ]
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register()
    {
        /*
         * Register the image tag processing callback
         */
        TagProcessor::instance()->registerCallback(function($input, $preview){
            if (!$preview) return $input;

            return preg_replace('|\<img src="image" alt="([0-9]+)"([^>]*)\/>|m',
                '<span class="image-placeholder" data-index="$1">
                    <span class="upload-dropzone">
                        <span class="label">Click or drop an image...</span>
                        <span class="indicator"></span>
                    </span>
                </span>',
            $input);
        });
    }

    public function boot()
    {
        /*
         * Register menu items for the icecollection.Pages plugin
         */
        Event::listen('pages.menuitem.listTypes', function() {
            return [
                'news-category' => 'News category',
                'all-news-categories' => 'All news categories'
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function($type) {
            if ($type == 'news-category' || $type == 'all-news-categories')
                return Category::getMenuTypeInfo($type);
        });

        Event::listen('pages.menuitem.resolveItem', function($type, $item, $url, $theme) {
            if ($type == 'news-category' || $type == 'all-news-categories')
                return Category::resolveMenuItem($item, $url, $theme);
        });
    }
}
