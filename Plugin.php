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
    }

    public function registerSettings()
    {
    }
}
