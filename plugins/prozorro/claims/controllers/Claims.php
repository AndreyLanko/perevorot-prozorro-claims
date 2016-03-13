<?php namespace prozorro\Claims\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use BackendAuth;
use Redirect;
use prozorro\Claims\Classes\Api as API;
use prozorro\Claims\Models\Claim as Claim;
use prozorro\Claims\Models\Tender as Tender;
use prozorro\Claims\Models\Settings as Settings;
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

        if(API::refreshClaimJson($claim)===false)
            return 'Не вказано номер тендеру для скарги';

        switch($claim->complaint_status)
        {
            case 'accepted':
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

        $this->asExtension('FormController')->update($claimId);
    }
    
    /*
    public function index($tenderId = null)
    {
        $userClaims=0;

        if($tenderId)
        {
            $this->userId=BackendAuth::getUser()->id;
            $this->tenderId=$tenderId;
    
            $user_claims=Claim::where('user_id', '=', $this->userId)->get();
            $claims_id=array_pluck($user_claims, 'id');

            Document::whereIn('claim_id', $claims_id)->delete();

            Claim::where('user_id', '=', $this->userId)->delete();

            Tender::where('user_id', '=', $this->userId)->delete();
    
            $claims=@file_get_contents(Settings::get('api').'/tenders/'.$tenderId.'/complaints');

            if(!empty($claims))
            {
                $claims=json_decode($claims);

                $tender_api=@file_get_contents(Settings::get('api').'/tenders/'.$tenderId);            

                if(!empty($tender_api))
                {
                    $tender_object=json_decode($tender_api);

                    foreach($claims->data as $claim)
                    {
                        if(!in_array($claim->status, ['draft', 'answered', 'claim']))
                        {
                            $claim_claim=new Claim;
                    
                            $claim_claim->id=$claim->id;
                            $claim_claim->tender_id=$tender_object->data->tenderID;
                            $claim_claim->tid=$tender_object->data->id;
                            $claim_claim->user_id=$this->userId;
                            $claim_claim->author='Код ЄДРПОУ: '.$claim->author->identifier->id.' «'.$claim->author->name.'»';
                            $claim_claim->procuring_entity='Код ЄДРПОУ: '.$tender_object->data->procuringEntity->identifier->id.' «'.$tender_object->data->procuringEntity->name.'»';
                            $claim_claim->status=$claim->status;
                            $claim_claim->title=$claim->title;
                            $claim_claim->tender_title=(!empty($tender_object->data->title) ? $tender_object->data->title : '');
                            $claim_claim->description=$claim->description;
                            $claim_claim->date_escalated=$claim->date;

                            $claim_documents=[];

                            if(!empty($claim->documents))
                            {
                                foreach($claim->documents as $document)
                                {
                                    $claim_document=new Document;

                                    $claim_document->claim_id=$claim->id;
                                    $claim_document->json=$document;

                                    array_push($claim_documents, $claim_document);
                                }
                            }

                            if(sizeof($claim_documents))
                                $claim_claim->documents()->saveMany($claim_documents);

                            $claim_claim->save();
                            $userClaims++;
                        }
                    }
                    
                    $tender=new Tender;

                    $tender->user_id=$this->userId;
                    $tender->id=$tender_object->data->id;
                    $tender->json=$tender_object->data;

                    $tender->save();
                }
            }
        }

        if($userClaims)
        {
            return Redirect::to('backend/prozorro/claims/claimspending');
        }
        else
        {
            $this->asExtension('ListController')->index();
        }
    }    
    */
}