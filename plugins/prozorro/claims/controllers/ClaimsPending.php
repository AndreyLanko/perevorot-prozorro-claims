<?php namespace prozorro\Claims\Controllers;

use BackendMenu;
use BackendAuth;
use Backend\Classes\Controller;
use prozorro\Claims\Models\Tender as Tender;

class ClaimsPending extends Controller
{
    public $implement = ['Backend\Behaviors\ListController'];

    public $listConfig = 'config_list.yaml';

    public $statuses=['pending'];

    var $tender;

    var $userId;

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('prozorro.Claims', 'claims-main', 'prozorro-claims-pending');
    }

    public function listExtendQuery($query)
    {
        $query->where('tender_procurementMethodType', (\Config::get('claims.tender_type')=='!belowThreshold' ? '!=' : '='), 'belowThreshold');
        $query->whereIn('complaint_status', $this->statuses);
    }
}