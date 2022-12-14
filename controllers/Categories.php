<?php namespace IceCollection\News\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

class Categories extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $requiredPermissions = ['icecollection.news.access_categories'];

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('IceCollection.News', 'news', 'categories');
    }
}
