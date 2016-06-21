<?php

namespace SparkPost;

class Transmission extends Resource
{
    public function __construct(SparkPost $sparkpost)
    {
        parent::__construct($sparkpost, 'transmissions');
    }

    public function post($payload, $headers = [])
    {
        $payload = $this->formatBlindCarbonCopy($payload); //Fixes BCCs into payload
        $payload = $this->formatCarbonCopy($payload); //Fixes CCs into payload
        $payload = $this->formatShorthandRecipients($payload); //Fixes shorthand recipients format
        echo json_encode($payload);
        return parent::post($payload, $headers);
    }

    private function formatBlindCarbonCopy($payload)
    {
        
        //If there's a list of BCC recipients, move then into the correct format
        if(isset($payload['bcc'])) {
            $payload = $this->addListToRecipients($payload, 'bcc');
        }

        return $payload;
    }

    private function formatCarbonCopy($payload)
    {
        if(isset($payload['cc'])) {
            $ccAddresses = [];
            foreach ($payload['cc'] as $ccRecipient) {
                array_push($ccAddresses, $this->toAddressString($ccRecipient['address']));
            }

            // set up the content headers as either what it was before or an empty array
            $payload['content']['headers'] = isset($payload['content']['headers']) ? $payload['content']['headers'] : [];
            // add cc header
            $payload['content']['headers']['CC'] = implode(',', $ccAddresses);

            $payload = $this->addListToRecipients($payload, 'cc');
        }

        return $payload;
    }

    private function formatShorthandRecipients($payload)
    {
        
        $payload['content']['from'] = $this->toAddressObject($payload['content']['from']);
        
        for($i = 0; $i < count($payload['recipients']); $i++) {  
            $payload['recipients'][$i]['address'] = $this->toAddressObject($payload['recipients'][$i]['address']);
        }

        return $payload;
    }

    private function addListToRecipients($payload, $listName)
    {
        $originalAddress = $this->toAddressString($payload['recipients'][0]['address']);
        foreach ($payload[$listName] as $recipient) {
            $recipient['address'] = $this->toAddressObject($recipient['address']);
            $recipient['address']['header_to'] = $originalAddress;
            
            array_push($payload['recipients'], $recipient);
        }
        
        //Delete the original object from the payload.
        unset($payload[$listName]);

        return $payload;
    }

    private function toAddressObject($address)
    {
        $return = $address;
        if (is_string($address)) {
            $return = [];

            if ($this->isEmail($address)) {
                $return['email'] = $address;
            }
            else if(preg_match('/"?(.[^"]+)"?\s*<(.+)>/', $address, $matches)) {
                $name = trim($matches[1]);
                $return['name'] = $matches[1];
                $return['email'] = $matches[2];
            }
            else {
                throw \Exception('Invalid address format: '.$address);
            }

        }

        return $return;
    }

    private function toAddressString($address)
    {
        // convert object to string
        if(!is_string($address)) {
            //if a name exists, then use format "name" <email>. Otherwise, just use format <email>
            $address = (isset($address['name']) ? '"' . $address['name'] 
                . '" ' : '') . '<' . $address['email'] . '>';
        }

        return $address;
    }

    private function isEmail($email){
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            return true;
        } else {
            return false;
        }
    }
}

?>