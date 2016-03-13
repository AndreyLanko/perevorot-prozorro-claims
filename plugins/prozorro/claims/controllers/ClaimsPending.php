<?php namespace prozorro\Claims\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use BackendAuth;
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
        //$query->whereIn('tender_procurementMethodType', ['aboveThresholdUA', 'aboveThresholdEU', 'reporting', 'negotiation', 'negotiation.quick']);
        $query->whereIn('complaint_status', $this->statuses);
    }
}