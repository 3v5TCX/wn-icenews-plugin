<?php namespace icecollection\icenews\Models;

use Model;

/**
 * Model
 */
class Category extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    use \Winter\Storm\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'icecollection_icenews_categories';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];


    /**
     * Sets the "url" attribute with a URL to this object
     * @param string $pageName
     * @param Cms\Classes\Controller $controller
     */
    public function setUrl($pageName, $controller)
    {
        $params = [
            'id' => $this->id,
            'slug' => $this->slug,
        ];

        return $this->url = $controller->pageUrl($pageName, $params);
    }


    /**
     * Returns URL of a category page.
     */
    protected static function getCategoryPageUrl($pageCode, $category, $theme)
    {
        $page = CmsPage::loadCached($theme, $pageCode);
        if (!$page) return;

        $properties = $page->getComponentProperties('newsPosts');
        if (!isset($properties['categoryFilter'])) {
            return;
        }

        /*
         * Extract the routing parameter name from the category filter
         * eg: {{ :someRouteParam }}
         */
        if (!preg_match('/^\{\{([^\}]+)\}\}$/', $properties['categoryFilter'], $matches)) {
            return;
        }

        $paramName = substr(trim($matches[1]), 1);
        $url = CmsPage::url($page->getBaseFileName(), [$paramName => $category->slug]);

        return $url;
    }
}
