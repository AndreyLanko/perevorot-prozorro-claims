<?php namespace prozorro\Claims\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use BackendAuth;

class ClaimsAccepted extends ClaimsPending
{
    public $implement = ['Backend\Behaviors\ListController'];
    
    public $listConfig = 'config_list.yaml';
    
    public $statuses=['accepted'];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('prozorro.Claims', 'claims-main', 'prozorro-accepted');
    }
}