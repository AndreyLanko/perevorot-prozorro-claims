<?php namespace prozorro\Claims\Models;

use Model;

/**
 * Model
 */
class Document extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Validation
     */
    public $rules = [
    ];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;

    protected $jsonable = ['json'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'prozorro_claims_documents';

    public $belongsTo = [
        'claim' => 'prozorro\Claims\Models\Claim'
    ];
}