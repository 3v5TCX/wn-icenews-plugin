<?php namespace icecollection\icenews\Models;

use Model;

/**
 * Model
 */
class Tag extends Model
{
    use \Winter\Storm\Database\Traits\Validation;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'icecollection_icenews_tags';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $belongsToMany = [
        'posts'=> [
            \icecollection\icenews\models\post::class,
            "table"=>"icecollection_icenews_post_tag",
            "key"=>"tag_id",
            "otherKey"=>"post_id",
        ],
    ];
}
