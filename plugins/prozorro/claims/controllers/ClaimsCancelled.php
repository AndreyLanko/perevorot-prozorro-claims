<?php namespace prozorro\Claims\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use BackendAuth;

class ClaimsCancelled extends ClaimsPending
{
    public $implement = ['Backend\Behaviors\ListController'];
    
    public $listConfig = 'config_list.yaml';
    
    public $claim_statuses;
    public $tender_statuses=['cancelled'];
    public $ignore_tender_statuses;

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('prozorro.Claims', 'claims-main', 'prozorro-cancelled');
    }
}