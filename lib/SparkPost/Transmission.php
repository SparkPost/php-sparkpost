<?php

namespace SparkPost;

class Transmission extends Resource
{
    protected $customHeaders = array();

    public function __construct(SparkPost $sparkpost)
    {
        parent::__construct($sparkpost, 'transmissions');
    }

    public function fixBlindCarbonCopy($payload)
    {
        //TODO: Manage recipients. "Vincent Song <vincentsong@sparkpost.com>"
        
        $modifiedPayload = $payload;
        $bccList = &$modifiedPayload['bcc'];
        $recipientsList = &$modifiedPayload['recipients'];
        
        //Format: Original Recipient" <original.recipient@example.com>
        //if a name exists, then do "name" <email>. Otherwise, just do <email>
        if(isset($modifiedPayload['recipients'][0]['name']))
        {
            $originalRecipient = '"' . $modifiedPayload['recipients'][0]['name'] 
                . '" <' . $modifiedPayload['recipients'][0]['address'] . '>';
        } else {
            $originalRecipient = '<' . $modifiedPayload['recipients'][0]['address'] 
                . '>';
        }

        //loop through all BCC recipients
        if(isset($bccList)){
            foreach ($bccList as $bccRecipient) { 
                $newRecipient = [
                        'address' => $bccRecipient['address'],
                        'header_to' => $originalRecipient,
                ];
                array_push($recipientsList, $newRecipient);
            }
        }
        
        //Delete the BCC object/array
        unset($modifiedPayload['bcc']); 

        return $modifiedPayload;
    }

    public function fixCarbonCopy($payload)
    {
        $ccRecipients = array();
        $modifiedPayload = $payload;
        $ccList = &$modifiedPayload['cc'];
        $recipientsList = &$modifiedPayload['recipients'];
        
        //If a name exists, then use format "name" <email>. Otherwise, use format <email>
        if(isset($modifiedPayload['recipients'][0]['name'])) {
            $originalRecipient = '"' . $modifiedPayload['recipients'][0]['name'] 
                . '" <' . $modifiedPayload['recipients'][0]['address'] . '>';
        } else {
            $originalRecipient =  $modifiedPayload['recipients'][0]['address'];
        }
        
        if(isset($ccList)){
             foreach ($ccList as $ccRecipient) {
                $newRecipient = [
                        'address' => $ccRecipient['address'],
                        'header_to' => $originalRecipient,
                ];
                //if name exists, then use "Name" <Email> format. Otherwise, just email will suffice. 
                if(is_array($ccRecipient['address'])) {
                    $ccRecipientData = ' "' . $ccRecipient['address']['name'] . '" ' . '<' . $ccRecipient['address']['email'] . '>';
                    
                } else {
                    $ccRecipientData = $ccRecipient['address'];
                }
                array_push($ccRecipients, $ccRecipientData);
                array_push($recipientsList, $newRecipient);
            }

            if(!empty($ccRecipients)){ //If there are CC'd people
                $this->customHeaders = array("CC" => implode(',', $ccRecipients));
            }
            
            //create new object 'headers' under content 
            $content = &$modifiedPayload['content'];
            $content['headers'] = $this->customHeaders;
            
        }
        
        //delete CC
        unset($modifiedPayload['cc']);
        
        return $modifiedPayload;
    }

    public function post($payload)
    {
        $modifiedPayload = $this->fixBlindCarbonCopy($payload); //Fixes BCCs into payload
        $modifiedPayload = $this->fixCarbonCopy($modifiedPayload); //Fixes CCs into payload
        return parent::post($modifiedPayload, $this->customHeaders);
    }
}

?>