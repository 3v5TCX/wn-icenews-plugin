<?php namespace icecollection\icenews\Models;

use App;
use Str;
use Lang;
use Model;
use Markdown;
use ValidationException;
use Avers\News\Classes\TagProcessor;
use Backend\Models\User;

/**
 * Model
 */
class Post extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    use \Winter\Storm\Database\Traits\SoftDelete;

    use \October\Rain\Database\Traits\Sluggable;

    protected $dates = ['deleted_at'];

    /**
     * @var array Generate slugs for these attributes.
     */
    protected $slugs = ['slug' => 'title'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'icecollection_icenews_posts';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'title' => 'required',
        'slug' => ['required', 'regex:/^[a-z0-9\/\:_\-\*\[\]\+\?\|]*$/i'],
    ];

    public $belongsTo = [
        'user' => ['Backend\Models\User']
    ];

    public $belongsToMany = [
        'tags'=> [
            \icecollection\icenews\models\tag::class,
            "table"=>"icecollection_icenews_post_tag",
            "key"=>"post_id",
            "otherKey"=>"tag_id",
        ],


        'categories'=> [
            \icecollection\icenews\models\category::class,
            "table"=>"icecollection_icenews_post_category",
            "key"=>"post_id",
            "otherKey"=>"category_id",
        ],
        /**/
    ];


    public static $allowedSortingOptions = array(
        'title asc' => 'Название (по возрастанию)',
        'title desc' => 'Название (по убыванию)',
        'created_at asc' => 'Создано (по возрастанию)',
        'created_at desc' => 'Создано (по убыванию)',
        'updated_at asc' => 'Обновлено (по возрастанию)',
        'updated_at desc' => 'Обновлено (по убыванию)',
        'published_at asc' => 'Дата новости (по возрастанию)',
        'published_at desc' => 'Дата новости (по убыванию)'
    );


    public $preview = null;

    /**
     * Lists posts for the front end
     * @param  array $options Display options
     * @return self
     */
    public function scopeListFrontEnd($query, $options)
    {
        /*
         * Default options
         */
        extract(array_merge([
            'page'       => 1,
            'perPage'    => 30,
            'sort'       => 'created_at',
            'categories' => null,
            'search'     => '',
            'published'  => true
        ], $options));

        $searchableFields = ['title', 'slug', 'short_story', 'full_story'];

        if ($published)
            $query->isPublished();

        /*
         * Sorting
         */
        if (!is_array($sort)) $sort = [$sort];
        foreach ($sort as $_sort) {

            if (in_array($_sort, array_keys(self::$allowedSortingOptions))) {
                $parts = explode(' ', $_sort);
                if (count($parts) < 2) array_push($parts, 'desc');
                list($sortField, $sortDirection) = $parts;
                $query->orderBy($sortField, $sortDirection);
            }
        }

        /*
         * Search
         */
        $search = trim($search);
        if (strlen($search)) {
            $query->searchWhere($search, $searchableFields);
        }

        /*
         * Categories
         */
        if ($categories !== null) {
            if (!is_array($categories)) $categories = [$categories];
            $query->whereHas('categories', function($q) use ($categories) {
                $q->whereIn('id', $categories);
            });
        }

        return $query->paginate($perPage, $page);
    }

    /**
     * Allows filtering for specifc categories
     * @param  Illuminate\Query\Builder  $query      QueryBuilder
     * @param  array                     $categories List of category ids
     * @return Illuminate\Query\Builder              QueryBuilder
     */
    public function scopeFilterCategories($query, $categories)
    {
        return $query->whereHas('categories', function($q) use ($categories) {
            $q->whereIn('id', $categories);
        });
    }

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

        if (array_key_exists('categories', $this->getRelations())) {
            $params['category'] = $this->categories->count() ? $this->categories->first()->slug : null;
        }

        return $this->url = "/newspage/".$this->slug;
        /**/

    }
    /**/

    public function scopeIsPublished($query) {
        return $query->whereNotNull('is_published')->where('is_published', true);
    }

}
