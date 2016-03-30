<?php namespace prozorro\Claims\Classes;

use BackendAuth;
use prozorro\Claims\Models\Document as Document;
use GuzzleHttp;
use Cache;

class API
{
    public static function refreshClaimJson(&$claim)
    {
        if(empty($claim->jsonTenderHash))
            return false;

        $client = new GuzzleHttp\Client();

        $json_claims=$client->request('GET', self::claimsUrl($claim->tender_id.'/'.$claim->complaint_path, $claim->complaint_id));
        $json_tender=$client->request('GET', self::tenderUrl($claim->jsonTenderHash));

        $response_claims=(string) $json_claims->getBody();
        $response_tender=(string) $json_tender->getBody();

        $json_claims=json_decode($response_claims, true);
        $json_tender=json_decode($response_tender, true);

        $json_claim=array_where($json_claims['data'], function($k, $item) use ($claim){
            return $item['id']==$claim->complaint_id;
        });

        $json_claim=array_values($json_claim)[0];

        $json=[
            'status'=>$json_claim['status'],
            'description'=>!empty($json_claim['description']) ? $json_claim['description'] : '',
            'title'=>!empty($json_claim['title']) ? $json_claim['title'] : '',
            'author'=>!empty($json_claim['author']) ? $json_claim['author'] : (!empty($claim->complaint_json['author']) ? $claim->complaint_json['author']: ''),
            'tender'=>[
                'procuringEntity'=>$json_tender['data']['procuringEntity'],
                'title'=>$json_tender['data']['title'],
                'id'=>$json_tender['data']['id'],
                'tenderID'=>$json_tender['data']['tenderID'],
                'procurementMethod'=>$json_tender['data']['procurementMethod'],
                'procurementMethodType'=>$json_tender['data']['procurementMethodType'],
            ],
            'dateSubmitted'=>!empty($json_claim['dateSubmitted']) ? $json_claim['dateSubmitted'] : $claim->complaint_json['dateSubmitted'],
            'complaintID'=>$claim->complaint_json['complaintID'],
            'date'=>$json_claim['date'],
            'type'=>$json_claim['type'],
            'id'=>$json_claim['id'],
        ];

        $claim->complaint_json=$json;

        $claim->save();
        
        if(!empty($json_claim['documents']))
        {
            $claim_documents=[];

            foreach($json_claim['documents'] as $document)
            {
                $claim_document=new Document;

                $claim_document->claim_id=$claim->id;
                $claim_document->json=$document;

                array_push($claim_documents, $claim_document);
            }

            Document::where('claim_id', '=', $claim->complaint_id)->delete();
            $claim->documents()->saveMany($claim_documents);
        }
    }
    
    public static function claimUrl($claimPath, $claimId)
    {
        return self::url('/tenders/%s/%s', $claimPath, $claimId);
    }

    public static function claimDocumentUrl($claimPath, $claimId)
    {
        return self::url('/tenders/%s/%s/documents', $claimPath, $claimId);
    }
    
    private static function claimsUrl($claimPath)
    {
        return self::url('/tenders/%s', $claimPath);
    }

    private static function tenderUrl($tenderId)
    {
        return self::url('/tenders/%s', $tenderId);
    }
    
    private static function url($url)
    {
        return \Config::get('claims.api_url').vsprintf($url, array_slice(func_get_args(), 1));
    }
}