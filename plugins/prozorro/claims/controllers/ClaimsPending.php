<?php namespace prozorro\Claims\Controllers;

use BackendMenu;
use BackendAuth;
use Backend\Classes\Controller;
use prozorro\Claims\Models\Tender as Tender;

class ClaimsPending extends Controller
{
    public $implement = ['Backend\Behaviors\ListController'];

    public $listConfig = 'config_list.yaml';

    public $claim_statuses=['pending'];
    public $tender_statuses;
    public $ignore_tender_statuses=['cancelled'];

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

        if(!empty($this->claim_statuses)){
            $query->whereIn('complaint_status', $this->claim_statuses);
        }

        if(!empty($this->tender_statuses)){
            $query->whereIn('tender_status', $this->tender_statuses);
        }

        if(!empty($this->ignore_tender_statuses)){
            $query->whereNotIn('tender_status', $this->ignore_tender_statuses);
        }

        if((boolean) env('SANDBOX'))
            $query->where('tender_mode', '=', 'test');
    }
}