<?php namespace prozorro\Claims\Models;

use Model;
use Input;
use BackendAuth;
use Flash;
use prozorro\Claims\Classes\Api as API;
use GuzzleHttp;

/**
 * Model
 */
class Claim extends Model
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

    public $jsonable = ['complaint_json'];

    /**
     * @var string The database table used by the model.
     */
    public $table='complaints';
    public $primaryKey = 'complaint_id';

    public $hasMany = [
        'documents' => 'prozorro\Claims\Models\Document'
    ];

    /**
     * @var array attachments
     */
    public $attachOne = [
        'file' => ['System\Models\File']
    ];

/*
    public function __construct()
    {
        $this->table = env('DB_complaints_table', 'complaints');
    }
*/

    public function filterFields($fields, $context = null)
    {
        if(in_array($this->complaint_status, ['pending', 'accepted', 'stopping']))
            $fields->file->hidden=false;
    }
    
    public function beforeSave()
    {
        if(in_array(Input::get('action'), ['accepted', 'invalid', 'satisfied', 'declined', 'file', 'stopped']))
        {
            $claim=Claim::where('complaint_id', '=', Input::get('claim_id'))->first();

            if($claim)
            {
                $client = new GuzzleHttp\Client();

                if(Input::get('action')!='file')
                {
                    $jar = new \GuzzleHttp\Cookie\CookieJar();
                    
                    $client->request('GET', API::claimUrl($claim->tender_id.'/'.$claim->complaint_path, $claim->complaint_id), [
                        'cookies' => $jar
                    ]);
    
                    $this->complaint_status=Input::get('action');

                    try{
                        
                        $data=[
                            'data'=>[
                                'status'=>Input::get('action')
                            ]
                        ];

                        //if(Input::get('action')=='stopped')
                        //    $data['data']['decision']="Тендер скасовується замовником";

                        $client->request('PATCH', API::claimUrl($claim->tender_id.'/'.$claim->complaint_path, $claim->complaint_id), [
                            'auth'=>[
                                \Config::get('claims.api_key'),
                                ''
                            ],
                            'headers' => [
                                'Content-Type' => 'application/json',
                                'X-Client-Request-ID'=>'claims-dev'
                            ],
                            'cookies'=>$jar,
                            'body'=>json_encode($data)
                        ]);
                    } catch (GuzzleHttp\Exception\ClientException $e) {            
                        //dd($e->getResponse());
                        return Flash::success('Помилка при зміні статусу');
                    }

                    Flash::success('Статус змінено');
                }
                else
                {
                    $file=$this->file()->withDeferred(post('_session_key'))->first();

                    if(!$file)
                        throw new \ApplicationException('Будь ласка, оберіть файл для завантаження');

                    try{
                        $jar = new \GuzzleHttp\Cookie\CookieJar();
                        
                        $client->request('GET', API::claimDocumentUrl($claim->tender_id.'/'.$claim->complaint_path, $claim->complaint_id), [
                            'cookies' => $jar
                        ]);

                        $response=$client->request('POST', API::claimDocumentUrl($claim->tender_id.'/'.$claim->complaint_path, $claim->complaint_id), [
                            'auth'=>[
                                \Config::get('claims.api_key'),
                                ''
                            ],
                            'headers' => [
                                'X-Client-Request-ID'=>'claims-dev'
                            ],
                            'cookies'=>$jar,
                            'multipart' => [
                                [
                                    'name'     => 'file',
                                    'filename' => $file->getFileName(),
                                    'contents' => fopen($file->getLocalPath(), 'r')
                                ]
                            ]
                        ]);

                        $file->delete();
                        $this->file=null;

                        $claim_document=new Document;

                        $json=json_decode((string) $response->getBody());

                        if(!empty($json->data))
                        {
                            $claim_document->claim_id=$claim->id;
                            $claim_document->json=$json->data;
    
                            $claim->documents()->saveMany([
                                $claim_document
                            ]);
                        }
                    } catch (GuzzleHttp\Exception\ClientException $e) {            
                        //dd($e->getResponse());

                        $file->delete();
                        $this->file=null;

                        return Flash::success('Помилка при завантаженні');
                    }

                    Flash::success('Опублікувано');
                }
            }
        }
    }

    public function getJsonTenderIdAttribute()
    {
        return !empty($this->complaint_json['tender']['tenderID']) ? $this->complaint_json['tender']['tenderID'] : '';
    }

    public function getJsonTenderHashAttribute()
    {
        return !empty($this->complaint_json['tender']['id']) ? $this->complaint_json['tender']['id'] : '';
    }

    public function getJsonTenderTitleAttribute()
    {
        return $this->complaint_json['tender']['title'];
    }

    public function getJsonProcuringEntityAttribute()
    {
        if(!empty($this->complaint_json['tender']))
        {
            $tender=$this->complaint_json['tender'];

            return (!empty($tender['procuringEntity']['identifier']['id']) ? 'Код ЄДРПОУ: '.$tender['procuringEntity']['identifier']['id'] : '').(!empty($tender['procuringEntity']['name']) ? ' «'.$tender['procuringEntity']['name'].'»' : '');
        }

        return '';
    }

    public function getJsonAuthorAttribute()
    {
        return (!empty($this->complaint_json['author']['identifier']['id']) ? 'Код ЄДРПОУ: '.$this->complaint_json['author']['identifier']['id'] : '').(!empty($this->complaint_json['author']['name']) ? ' «'.$this->complaint_json['author']['name'].'»' : '');
    }
    
    public function getJsonDateSubmittedAttribute()
    {
        return !empty($this->complaint_json['dateSubmitted']) ? date("Y-m-d H:i:s", strtotime($this->complaint_json['dateSubmitted'])) : null;
    }    

    public function getJsonTitleAttribute()
    {
        return $this->complaint_json['title'];
    }

    public function getJsonDescriptionAttribute()
    {
        return $this->complaint_json['description'];
    }

    public function getUploadDocumentsAttribute()
    {
        return array_where($this->documents, function($key, $document){
            return $document->json['author']!='complaint_owner';
        });
    }

    public function getAuthorDocumentsAttribute()
    {
        return array_where($this->documents, function($key, $document){
            return $document->json['author']=='complaint_owner';
        });        
    }
    
    public function getTenderStatusTranslatedAttribute()
    {
        $statuses=[
            'active.enquiries'=>'Період уточнень',
            'active.tendering'=>'Період прийому пропозицій',
            'active.pre-qualification'=>'Прекваліфікація',
            'active.pre-qualification.stand-still'=>'Прекваліфікація (період оскаржень)',
            'active.auction'=>'Аукціон',
            'active.qualification'=>'Кваліфікація',
            'active'=>'Активна',
            'unsuccessfull'=>'Не успішна',
            'cancelled'=>'Закупівля скасована',
            'active.awarded'=>'Контракт'
        ];

        return !empty($statuses[$this->tender_status]) ? $statuses[$this->tender_status] : $this->tender_status;
    }

    public function getStatusTranslatedAttribute()
    {
        $statuses=[
            'pending'=>'Нова скарга',
            'accepted'=>'Прийнято до розгляду',
            'invalid'=>'Не прийнято до розгляду',
            'declined'=>'Не задоволено',
            'satisfied'=>'Задоволено',
            'cancelled'=>'Скасовано',
            'resolved'=>'Виконано',
            'stopping'=>'Відкликано скаржником',
            'stopped'=>'Розгляд зупинено',
        ];

        return !empty($statuses[$this->complaint_status]) ? $statuses[$this->complaint_status] : $this->complaint_status;
    }
}