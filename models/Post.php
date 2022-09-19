<?php namespace IceCollection\News\Models;

use App;
use Str;
use Lang;
use Model;
use Markdown;
use ValidationException;
use IceCollection\News\Classes\TagProcessor;
use Backend\Models\User;

class Post extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'icecollection_news_posts';

    /*
     * Validation
     */
    public $rules = [
        'title' => 'required',
        'slug' => ['required', 'regex:/^[a-z0-9\/\:_\-\*\[\]\+\?\|]*$/i'],
        'content' => 'required',
        'excerpt' => ''
    ];

    /**
     * The attributes that should be mutated to dates.
     * @var array
     */
    protected $dates = ['published_at'];

    /**
     * The attributes on which the post list can be ordered
     * @var array
     */
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

    /*
     * Relations
     */
    public $belongsTo = [
        'user' => ['Backend\Models\User']
    ];

    public $belongsToMany = [
        'categories' => ['IceCollection\News\Models\Category', 'table' => 'icecollection_news_posts_categories', 'order' => 'name']
    ];

    public $attachOne = [
        'featured_images' => ['System\Models\File', 'delete' => true]
    ];

    public $attachMany = [
        'additional_images' => ['System\Models\File', 'delete' => true]
    ];

    /**
     * @var array The accessors to append to the model's array form.
     */
    // protected $appends = ['summary', 'has_summary'];

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

        $searchableFields = ['title', 'slug', 'excerpt', 'content'];

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

    public function afterValidate()
    {
        if ($this->published && !$this->published_at) {
            throw new ValidationException([
               'published_at' => Lang::get('icecollection.news::lang.post.published_validation')
            ]);
        }
    }

    public function scopeIsPublished($query)
    {
        return $query
            ->whereNotNull('is_published')
            ->where('is_published', true)
        ;
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

        return $this->url = $controller->pageUrl($pageName, $params);
    }

    /**
     * Used to test if a certain user has permission to edit post,
     * returns TRUE if the user is the owner or has other posts access.
     * @param User $user
     * @return bool
     */
    public function canEdit(User $user)
    {
        return ($this->user_id == $user->id) || $user->hasAnyAccess(['icecollection.news.access_other_posts']);
    }

    /**
     * Delete main image and additional images
     */
    public function afterDelete() {
        if (is_object($this->featured_images)) {
            $this->featured_images->delete();
        }

        foreach ($this->additional_images as $additional_images) {
            $additional_images->delete();
        }
    }
}
