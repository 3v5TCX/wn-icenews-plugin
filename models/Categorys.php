<?php namespace icecollection\icenews\Models;

use Model;

/**
 * Model
 */
class Categorys extends Model
{
    use \Winter\Storm\Database\Traits\Validation;
    
    use \Winter\Storm\Database\Traits\SoftDelete;

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'icecollection_icenews_categorys';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
