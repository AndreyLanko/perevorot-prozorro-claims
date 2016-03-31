<?php namespace prozorro\Claims\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use BackendAuth;
use Redirect;
use prozorro\Claims\Classes\Api as API;
use prozorro\Claims\Models\Claim as Claim;
use prozorro\Claims\Models\Tender as Tender;
use prozorro\Claims\Models\Document as Document;

class Claims extends Controller
{
    public $implement = ['Backend\Behaviors\ListController', 'Backend\Behaviors\FormController'];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('prozorro.Claims', 'claims-main');
    }
    
    var $tenderId;
    var $userId;
    
    public function update($claimId = null)
    {
        $claim=Claim::where('complaint_id', '=', $claimId)->first();

        if(!(boolean) env('SANDBOX') && $claim->tender_mode=='test')
            abort(404);

        if((boolean) env('SANDBOX') && $claim->tender_mode!=='test')
            abort(404);

        if(API::refreshClaimJson($claim)===false)
            return 'Не вказано номер тендеру для скарги';

        switch($claim->complaint_status)
        {
            case 'accepted':
            case 'stopping':
                BackendMenu::setContext('prozorro.Claims', 'claims-main', 'prozorro-accepted');
                break;

            case 'invalid':
            case 'declined':
            case 'resolved':
            case 'cancelled':
            case 'satisfied':
                BackendMenu::setContext('prozorro.Claims', 'claims-main', 'prozorro-archive');
                break;

            case 'pending':
                BackendMenu::setContext('prozorro.Claims', 'claims-main', 'prozorro-claims-pending');
            break;
        }

        switch($claim->tender_status)
        {
            case 'cancelled':
                BackendMenu::setContext('prozorro.Claims', 'claims-main', 'prozorro-cancelled');
                break;
        }
    
        $this->asExtension('FormController')->update($claimId);
    }
}