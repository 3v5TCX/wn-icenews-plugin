<?php namespace icecollection\icenews\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

use Flash;
use icecollection\icenews\Models\Category;
use Redirect;
use ApplicationException;

class CategoryController extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'            ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('icecollection.icenews', 'main-menu-item', 'side-menu-item2');
    }
}
