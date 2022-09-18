<?php namespace icecollection\icenews;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function pluginDetails()
    {
        return [
            'name'        => 'icecollection.icenews::lang.plugin.name',
            'description' => 'icecollection.icenews::lang.plugin.description',
            'author'      => 'Belonogov Ilya',
            'icon'        => 'icon-book'
        ];
    }

    public function registerComponents()
    {
        return [
            'icecollection\icenews\components\Posts'           => 'newsPosts',
            /*
            'icecollection\icenews\components\Post'            => 'newsPost',
            'icecollection\icenews\components\Categories'      => 'newsCategories',
            'icecollection\icenews\components\Newsfeed'        => 'newsNewsfeed',
            'icecollection\icenews\components\RegionNewsPosts' => 'regionNewsPosts',
            'icecollection\icenews\components\RegionNewsFeed'  => 'regionNewsFeed'
            /**/
        ];
    }

    public function registerSettings()
    {
    }
}
