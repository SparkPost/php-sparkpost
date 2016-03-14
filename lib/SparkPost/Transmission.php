<?php
namespace SparkPost;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

/**
 * SDK interface for managing transmissions
 */
class Transmission extends APIResource {

  public $endpoint = 'transmissions';

  /**
   * Mapping for values passed into the send method to the values needed for the Transmission API
   * @var array
   */
  protected static $parameterMappings = [
    'campaign'=>'campaign_id',
    'metadata'=>'metadata',
    'substitutionData'=>'substitution_data',
    'description'=>'description',
    'returnPath'=>'return_path',
    'replyTo'=>'content.reply_to',
    'subject'=>'content.subject',
    'from'=>'content.from',
    'html'=>'content.html',
    'text'=>'content.text',
    'rfc822'=>'content.email_rfc822',
    'customHeaders'=>'content.headers',
    'recipients'=>'recipients',
    'recipientList'=>'recipients.list_id',
    'attachments'=>'content.attachments',
    'inlineImages'=>'content.inline_images',
    'template'=>'content.template_id',
    'trackOpens'=>'options.open_tracking',
    'trackClicks'=>'options.click_tracking',
    'useDraftTemplate'=>'use_draft_template'
  ];

  /**
   * Sets up default structure and default values for the model that is acceptable by the API
   * @var array
   */
  protected static $structure = [
    'return_path'=>'default@sparkpostmail.com',
    'content'=>[
      'html'=>null,
      'text'=>null,
      'email_rfc822'=>null
    ],
    'use_draft_template'=>false
  ];

  /**
   * Method for issuing POST request to the Transmissions API
   *
   *  This method assumes that all the appropriate fields have
   *  been populated by the user through configuration.  Acceptable
   *  configuration values are:
   *  'campaign': string,
   *  'metadata': array,
   *  'substitutionData': array,
   *  'description': string,
   *  'replyTo': string,
   *  'subject': string,
   *  'from': string,
   *  'html': string,
   *  'text': string,
   *  'rfc822': string,
   *  'customHeaders': array,
   *  'recipients': array,
   *  'recipientList': string,
   *  'template': string,
   *  'trackOpens': boolean,
   *  'trackClicks': boolean,
   *  'useDraftTemplate': boolean
   *
   * @param array $transmissionConfig
   * @return array API repsonse represented as key-value pairs
   */
  public function send( $transmissionConfig ) {
    return $this->create( $transmissionConfig );
  }

  /**
   * Method for retrieving information about all transmissions
   *  Wrapper method for a cleaner interface
   *
   * @param null|string $campaignID
   * @param null|string $templateID
   * @return array result Set of transmissions
   */
  public function all( $campaignID=null, $templateID=null ) {
    $options = [];
    if( $campaignID !== NULL ) $options['campaign_id'] = $campaignID;
    if( $templateID !== NULL ) $options['template_id'] = $templateID;

    return $this->get( null, $options );
  }

  /**
   * Method for retrieving information about a single transmission
    *  Wrapper method for a cleaner interface
    *
   * @param string $transmissionID Identifier of the transmission to be found
   * @return array result Single transmission represented in key-value pairs
   */
  public function find($transmissionID) {
    return $this->get($transmissionID);
  }

  /**
   * Method for deleting a scheduled transmission
    *  Wrapper method for a cleaner interface
    *
   * @param string $transmissionID Identifier of the transmission to deleted
   * @return array result Result of delete, empty on success
   */
  public function destroy($transmissionID) {
    return $this->delete($transmissionID);
  }
}

?>
