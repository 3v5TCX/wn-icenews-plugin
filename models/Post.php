<?php namespace icecollection\icenews\Models;

use App;
use Str;
use Lang;
use Model;
use Markdown;
use ValidationException;
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
        'categorys'=> [
            \icecollection\icenews\models\category::class,
            "table"=>"icecollection_icenews_post_category",
            "key"=>"post_id",
            "otherKey"=>"category_id",
        ],
    ];

    public function scopeIsPublished($query) {
        return $query->whereNotNull('is_published')->where('is_published', true);
    }


}
