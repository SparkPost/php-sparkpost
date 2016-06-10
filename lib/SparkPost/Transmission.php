<?php

namespace SparkPost;

require '../../vendor/autoload.php';

class Transmission extends Resource
{
    protected $endpoint = 'transmission';

    public function __constructor(SparkPost $sparkpost)
    {
        parent::__construct($sparkpost, $endpoint);
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
                . '" &lt;' . $modifiedPayload['recipients'][0]['address'] . '&gt;';
        } else {
            $originalRecipient = '&lt;' . $modifiedPayload['recipients'][0]['address'] 
                . '&gt;';
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
        $ccCustomHeadersList = "";
        $modifiedPayload = $payload;
        $ccList = &$modifiedPayload['cc'];
        $recipientsList = &$modifiedPayload['recipients'];
        
        //if a name exists, then do "name" <email>. Otherwise, just do <email>
        if(isset($modifiedPayload['recipients'][0]['name'])) {
            $originalRecipient = '"' . $modifiedPayload['recipients'][0]['name'] 
                . '" &lt;' . $modifiedPayload['recipients'][0]['address'] . '&gt;';
        } else {
            $originalRecipient = '&lt;' . $modifiedPayload['recipients'][0]['address'] 
                . '&gt;';
        }
        
        if(isset($ccList)){
             foreach ($ccList as $ccRecipient) {
                $newRecipient = [
                        'address' => $ccRecipient['address'],
                        'header_to' => $originalRecipient,
                ];

                //if name exists, then use "Name" <Email> format. Otherwise, just email will suffice. 
                if(isset($ccRecipient['name'])) {
                    $ccCustomHeadersList = $ccCustomHeadersList . ' "' . $ccRecipient['name'] 
                        . '" &lt;' . $ccRecipient['address'] . '&gt;,';
                } else {
                    $ccCustomHeadersList = $ccCustomHeadersList . ' ' . $ccRecipient['address'];
                }

                array_push($recipientsList, $newRecipient);
            }   
        }
        
        
        //Creates customHeaders and adds CSV list of CC emails
        $customHeaders = array("CC" => $ccCustomHeadersList); 
        $modifiedPayload['customHeaders'] = $customHeaders; 
        //delete CC    
        unset($modifiedPayload['cc']);
        
        return $modifiedPayload;
    }

    public function post($payload)
    {
        $modifiedPayload = $this->fixBlindCarbonCopy($payload); //Accounts for any BCCs
        $modifiedPayload = $this->fixCarbonCopy($modifiedPayload); //Accounts for any CCs
        parent::post($modifiedPayload);
    }
}