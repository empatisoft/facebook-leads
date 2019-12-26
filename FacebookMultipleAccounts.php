<?php
/**
 * Developer: ONUR KAYA
 * Contact: empatisoft@gmail.com
 */

use GuzzleHttp\Client;

class FacebookMultipleAccounts {

    private $client;
    private $accounts;
    private $baseUrl = 'https://graph.facebook.com/v5.0/';
    private $leads = array();
    private $forms = array();

    public function __construct($accounts)
    {
        $this->accounts = $accounts;
        $this->client = new Client();
    }

    public function getForms($type = 'JSON') {

        foreach ($this->accounts as $page_id => $token) {

            $params = array(
                "fields" => "id,locale,name,questions,page_id,page,status",
                "filtering" => "[{'field':'status','operator':'IN','value':['ACTIVE']}]",
                "access_token" => $token
            );

            $response = $this->client->request('GET', $this->baseUrl.'/'.$page_id.'/leadgen_forms', array('query' => $params));

            $contents = $response->getBody()->getContents();

            $forms = json_decode($contents);

            if(!empty($forms->data)) {
                foreach ($forms->data as $form)
                {
                    $this->pushForms($form);
                }
            }

            if(isset($forms->paging) && isset($forms->paging->next) && $forms->paging->next)
                $this->getFormsNextPage($forms->paging->next);

        }

        $result = $this->toObject($this->forms);

        if($type == 'JSON') {
            header('Content-type: application/json');
            echo json_encode($result, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
            exit();
        }

        return $result;

    }

    public function getLeads($form_id, $token, $type = 'JSON') {

        $date = date('Y-m-d', strtotime('-3 days', strtotime(date('Y-m-d'))));
        $filter = '[{"field":"time_created","operator":"GREATER_THAN","value":'.strtotime($date.'T00:00:01+0000').'}]';

        $params = array(
            "fields" => "id,ad_id,ad_name,adset_id,adset_name,campaign_id,campaign_name,created_time,custom_disclaimer_responses,field_data,form_id,home_listing,is_organic,partner_name,platform,retailer_item_id,vehicle",
            'limit' => 500,
            "filtering" => $filter,
            "access_token" => $token
        );

        $response = $this->client->request('GET', $this->baseUrl.$form_id.'/leads', array('query' => $params));

        $contents = $response->getBody()->getContents();

        $leads = json_decode($contents);

        if(!empty($leads->data)) {
            foreach ($leads->data as $lead)
            {
                $this->pushLeads($lead);
            }
        }

        if(isset($leads->paging) && isset($leads->paging->next) && $leads->paging->next)
            $this->getLeadsNextPage($leads->paging->next);

        $result = $this->toObject($this->leads);

        if($type == 'JSON') {
            header('Content-type: application/json');
            echo json_encode($result, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
            exit();
        }

        return $result;

    }

    /**
     * Başvuru listesi güncelleniyor.
     */
    private function pushLeads($lead) {

        $fields = $this->parseLeadDataFields($lead->field_data);

        array_push($this->leads, array(
            'lead_id' => isset($lead->id) ? $lead->id : NULL,
            'full_name' => isset($fields['full_name']) ? $fields['full_name'] : NULL,
            'first_name' => isset($fields['first_name']) ? $fields['first_name'] : NULL,
            'last_name' => isset($fields['last_name']) ? $fields['last_name'] : NULL,
            'email' => isset($fields['email']) ? $fields['email'] : NULL,
            'phone_number' => isset($fields['phone_number']) ? $fields['phone_number'] : NULL,
            'ad_id' => isset($lead->ad_id) ? $lead->ad_id : NULL,
            'ad_name' => isset($lead->ad_name) ? $lead->ad_name : NULL,
            'adset_id' => isset($lead->adset_id) ? $lead->adset_id : NULL,
            'adset_name' => isset($lead->adset_name) ? $lead->adset_name : NULL,
            'campaign_id' => isset($lead->campaign_id) ? $lead->campaign_id : NULL,
            'campaign_name' => isset($lead->campaign_name) ? $lead->campaign_name : NULL,
            'created_time' => isset($lead->created_time) ? $lead->created_time : NULL,
            'custom_disclaimer_responses' => isset($lead->custom_disclaimer_responses) ? $lead->custom_disclaimer_responses : NULL,
            'form_id' => isset($lead->form_id) ? $lead->form_id : NULL,
            'home_listing' => isset($lead->home_listing) ? $lead->home_listing : NULL,
            'is_organic' => isset($lead->is_organic) ? $lead->is_organic : NULL,
            'partner_name' => isset($lead->partner_name) ? $lead->partner_name : NULL,
            'platform' => isset($lead->platform) ? $lead->platform : NULL,
            'retailer_item_id' => isset($lead->retailer_item_id) ? $lead->retailer_item_id : NULL,
            'vehicle' => isset($lead->vehicle) ? $lead->vehicle : NULL
        ));
    }

    /**
     * İlgili forma ait başvuruların sonraki sayfaları alınıyor.
     */
    private function getLeadsNextPage($url) {
        $response = $this->client->request('GET', $url);
        $leads = json_decode($response->getBody()->getContents());

        if(!empty($leads->data)) {
            foreach ($leads->data as $lead)
            {
                $this->pushLeads($lead);
            }
        }

        if(isset($leads->paging) && isset($leads->paging->next) && $leads->paging->next)
            $this->getLeadsNextPage($leads->paging->next);

    }

    /**
     * Başvuru sahibinin kişisel ve iletişim bilgileri tanımlanıyor.
     */
    private function parseLeadDataFields($fields) {

        $full_name = null;
        $first_name = null;
        $last_name = null;
        $email = null;
        $phone = null;

        if(!empty($fields)) {
            foreach ($fields as $field) {

                if(isset($field->name)) {

                    if($field->name == 'full_name')
                        $full_name = isset($field->values[0]) ? $field->values[0] : null;

                    if($field->name == 'first_name')
                        $first_name = isset($field->values[0]) ? $field->values[0] : null;

                    if($field->name == 'last_name')
                        $last_name = isset($field->values[0]) ? $field->values[0] : null;

                    if($field->name == 'email')
                        $email = isset($field->values[0]) ? $field->values[0] : null;

                    if($field->name == 'phone_number')
                        $phone = isset($field->values[0]) ? $field->values[0] : null;

                }

            }
        }

        return array(
            'full_name' => $full_name,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone_number' => $phone
        );
    }

    private function getFormsNextPage($url) {
        $response = $this->client->request('GET', $url);
        $forms = json_decode($response->getBody()->getContents());

        if(!empty($forms->data)) {
            foreach ($forms->data as $form)
            {
                $this->pushForms($form);
            }
        }

        if(isset($forms->paging) && isset($forms->paging->next) && $forms->paging->next)
            $this->getFormsNextPage($forms->paging->next);

    }

    /**
     * Form listesi güncelleniyor.
     */
    private function pushForms($form) {

        array_push($this->forms, array(
            'id' => isset($form->id) ? $form->id : NULL,
            'locale' => isset($form->locale) ? $form->locale : NULL,
            'name' => isset($form->name) ? $form->name : NULL,
            'questions' => isset($form->questions) ? $form->questions : NULL,
            'page_id' => isset($form->page_id) ? $form->page_id : NULL,
            'page' => isset($form->page->name) ? $form->page->name : NULL
        ));
    }

    private function toObject($array) {
        return json_decode(json_encode($array), FALSE);
    }

}