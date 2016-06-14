<?php

namespace SparkPost;

class Transmission extends Resource
{
    protected $customHeaders = array();

    public function __construct(SparkPost $sparkpost)
    {
        parent::__construct($sparkpost, 'transmissions');
    }

    public function fixShorthandRecipients($payload){ 
        $modifiedPayload = $payload;
        $recipients = &$modifiedPayload['recipients'];
        $from = &$modifiedPayload['content']['from'];
        $from = $this->shorthandRecipientsParser($from);
        for($i = 0; $i < count($recipients); $i++){  
            $recipients[$i]['address'] = $this->shorthandRecipientsParser($recipients[$i]['address']);
        }
        return $modifiedPayload;
    }
    
    
    /*
        cases:
        1) 'vincentwsong@gmail.com'
        2) '"Vincent Song" <vincentwsong@gmail.com>'
        3) 'vincent song vincentwsong@gmail.com'
    */
    public function shorthandRecipientsParser($value){

        if(!is_array($value)){ //if the given value isn't an array
            $name = "";
            $email = "";
            $newPerson = array();
            
            if(preg_match('/"(.+)"/', $value, $matches)){ //if "NAME" is found 
                $name = $matches[0];
                if(preg_match('/<(.+)>/', $value, $matches)){ //if <EMAIL> is found
                    $email = $matches[1];
                    if(!$this->isEmail($email)){
                        throw new \Exception("Invalid email address. Use format \"NAME_HERE\" <EMAIL_HERE>");
                    } else {
                      $newPerson = [
                            'name' => trim($name,'""'),
                            'email' => $email
                        ];   
                        return $newPerson;
                    }
                } else { //Has name, needs email in <EMAIL> format
                    throw new \Exception("Invalid email address. Use format \"NAME_HERE\" <EMAIL_HERE>");
                }
            } else if ($this->isEmail($value)){ //if the original $value is just an email, like postmaster@sparkpost.com
                $newPerson = [
                    'email' => $value 
                ];
                
                return $newPerson;
            } else { //$value isn't a valid email at all. E.g. postmastersparkpost.com
                //echo $value;
                throw new \Exception("Invalid email address.");
            }
        } else { //it's already an object, nothing we can do here
            return $value;
        }
    }
    
    private function isEmail($email){
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            return true;
        } else {
            return false;
        }
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
        $modifiedPayload = $this->fixShorthandRecipients($modifiedPayload);
        return parent::post($modifiedPayload, $this->customHeaders);
    }
}

?>