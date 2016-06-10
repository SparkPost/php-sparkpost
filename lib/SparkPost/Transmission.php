<?php

namespace SparkPost;

require '../../vendor/autoload.php';

class Transmission extends Resource
{
    protected $endpoint = 'transmission';

    public function __constructor(SparkPost $sparkpost){
        parent::__construct($sparkpost, $endpoint);
    }

    public function fixBlindCarbonCopy($payload)
    {

        //TODO: Manage recipients. "Vincent Song <vincentsong@sparkpost.com>"

        $modifiedPayload = $payload;
        $bccList = &$modifiedPayload['bcc'];
        $recipientsList = &$modifiedPayload['recipients'];
       
        if(isset($modifiedPayload['recipients'][0]['name'])) //if a name exists, then do "name" <email>. Otherwise, just do <email>
        {
            $originalRecipient = '"'.$modifiedPayload['recipients'][0]['name'].'" &lt;'.$modifiedPayload['recipients'][0]['address'].'&gt;';
        } else {
            $originalRecipient = '&lt;'.$modifiedPayload['recipients'][0]['address'].'&gt;';
        } //Format: Original Recipient" <original.recipient@example.com>

        foreach ($bccList as $bccRecipient) { //loop through all BCC recipients
            $newRecipient = [
                    'address' => $bccRecipient['address'],
                    'header_to' => $originalRecipient,
            ];
            array_push($recipientsList, $newRecipient);
        }
        
        unset($modifiedPayload['bcc']); //Delete the BCC object/array

        return $modifiedPayload;
    }

    public function fixCarbonCopy($payload)
    {

        $ccCustomHeadersList = "";
        $modifiedPayload = $payload;
        $ccList = &$modifiedPayload['cc'];
        $recipientsList = &$modifiedPayload['recipients'];
        
        if(isset($modifiedPayload['recipients'][0]['name'])) //if a name exists, then do "name" <email>. Otherwise, just do <email>
        {
            $originalRecipient = '"'.$modifiedPayload['recipients'][0]['name'].'" &lt;'.$modifiedPayload['recipients'][0]['address'].'&gt;';
        } else {
            $originalRecipient = '&lt;'.$modifiedPayload['recipients'][0]['address'].'&gt;';
        }

        foreach ($ccList as $ccRecipient) {
            $newRecipient = [
                    'address' => $ccRecipient['address'],
                    'header_to' => $originalRecipient,
            ];
            
            //if name exists, then use "Name" <Email> format. Otherwise, just email will suffice. 
            
            if(isset($ccRecipient['name'])){
                $ccCustomHeadersList = $ccCustomHeadersList . ' "' . $ccRecipient['name'] . '" &lt;' . $ccRecipient['address'] . '&gt;,';
            } else {
                $ccCustomHeadersList = $ccCustomHeadersList . ' ' . $ccRecipient['address'];
            }
            
            array_push($recipientsList, $newRecipient);
        }
        
        $customHeaders = array("CC" => $ccCustomHeadersList); //Creates customHeaders and adds CSV list of CC emails
        $modifiedPayload['customHeaders'] = $customHeaders; 
        
        unset($modifiedPayload['cc']); //delete CC
        
        return $modifiedPayload;
    }

    public function post($payload)
    {
        $modifiedPayload = $this->fixBlindCarbonCopy($payload); //Accounts for any BCCs
        $modifiedPayload = $this->fixCarbonCopy($modifiedPayload); //Accounts for any CCs
        parent::post($modifiedPayload);
    }
}

//$testPayload = 
//[
//    'content' => [
//        'from' => [
//            'name' => 'Sparkpost Team',
//            'email' => 'from@sparkpostbox.com',
//        ],
//        'subject' => 'First Mailing From PHP',
//        'html' => '<html><body><h1>Congratulations, {{name}}!</h1><p>You just sent your very first mailing!</p></body></html>',
//        'text' => 'Congratulations, {{name}}!! You just sent your very first mailing!',
//    ],
//    'substitution_data' => ['name' => 'YOUR_FIRST_NAME'],
//    'recipients' => [
//        [
//            'address' => 'EMAIL_ADDRESS1',
//            'name' => 'NAME_1'
//        ],
//    ],
//    'bcc' => [
//        [
//            'address' => 'BCC_EMAIL_ADDRESS1',
//            'name' => 'BCC_NAME1'
//        ],
//        [
//            'address' => 'BCC_EMAIL_ADDRESS2',
//            'name' => 'BCC_NAME2'
//        ],
//    ], 
//    'cc' => [
//        [
//            'address' => 'CC_EMAIL_ADDRESS1',
//            'name' => 'CC_NAME1'
//        ],
//        [
//            'address' => 'CC_EMAIL_ADDRESS2',
//            'name' => 'CC_NAME2'
//        ],
//        [
//            'address' => 'CC_EMAIL_ADDRESS3',
//        ]
//    ]
//];
//$transmission = new Transmission();
//$transmission->post($testPayload);

//$output = 
//[
//    'content' => [
//        'from' => [
//            'name' => 'Sparkpost Team',
//            'email' => 'from@sparkpostbox.com',
//        ],
//        'subject' => 'First Mailing From PHP',
//        'html' => '<html><body><h1>Congratulations, {{name}}!</h1><p>You just sent your very first mailing!</p></body></html>',
//        'text' => 'Congratulations, {{name}}!! You just sent your very first mailing!',
//    ],
//    'substitution_data' => ['name' => 'YOUR_FIRST_NAME'],
//    'recipients' => [
//        [
//            'address' => 'EMAIL_ADDRESS1',
//            'name' => 'NAME_1'
//        ],
//        [
//            'address' => 'BCC_EMAIL_ADDRESS1',
//            'header_to' => '"NAME_1" <EMAIL_ADDRESS1>'
//        ],
//        [
//            'address' => 'BCC_EMAIL_ADDRESS2',
//            'header_to' => '"NAME_1" <EMAIL_ADDRESS1>'
//        ],
//        [
//            'address' => 'CC_EMAIL_ADDRESS1',
//            'header_to' => '"NAME_1" <EMAIL_ADDRESS1>'
//        ],
//        [
//            'address' => 'CC_EMAIL_ADDRESS2',
//            'header_to' => '"NAME_1" <EMAIL_ADDRESS1>'
//        ],
//        [
//            'address' => 'CC_EMAIL_ADDRESS3',
//            'header_to' => '"NAME_1" <EMAIL_ADDRESS1>'
//        ],
//    ],
//    'customHeaders' => [
//        'CC' => '"CC_NAME1" <CC_EMAIL_ADDRESS1>, "CC_NAME2" <CC_EMAIL_ADDRESS2>, CC_EMAIL_ADDRESS3'
//    ]
//];






