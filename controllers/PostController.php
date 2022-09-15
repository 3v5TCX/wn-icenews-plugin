<?php namespace icecollection\icenews\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

use Flash;
use icecollection\icenews\Models\Posts;
use Redirect;
use ApplicationException;
use icecollection\icenews\models\post;


class PostController extends Controller
{
    public $implement = [        'Backend\Behaviors\ListController',        'Backend\Behaviors\FormController'    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('icecollection.icenews', 'main-menu-item', 'side-menu-item3');
    }

    public function formBeforeCreate($model)
    {
        $model->user_id = $this->user->id;
    }

    public function index()
    {
        $this->vars['postsTotal'] = Posts::count();
        $this->vars['postsPublished'] = Posts::isPublished()->count();
        $this->vars['postsDrafts'] = $this->vars['postsTotal'] - $this->vars['postsPublished'];

        $this->asExtension('ListController')->index();
    }

    public function create()
    {
        parent::create();
        BackendMenu::setContext('icecollection.icenews', 'main-menu-item', 'side-menu-item4');
    }

}
