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
    'attachments'=>'content.attachments',
    'campaign'=>'campaign_id',
    'customHeaders'=>'content.headers',
    'description'=>'description',
    'from'=>'content.from',
    'html'=>'content.html',
    'inlineCss'=>'options.inline_css',
    'inlineImages'=>'content.inline_images',
    'metadata'=>'metadata',
    'recipientList'=>'recipients.list_id',
    'recipients'=>'recipients',
    'replyTo'=>'content.reply_to',
    'returnPath'=>'return_path',
    'rfc822'=>'content.email_rfc822',
    'sandbox'=>'options.sandbox',
    'startTime'=>'options.start_time',
    'subject'=>'content.subject',
    'substitutionData'=>'substitution_data',
    'template'=>'content.template_id',
    'text'=>'content.text',
    'trackClicks'=>'options.click_tracking',
    'trackOpens'=>'options.open_tracking',
    'transactional'=>'options.transactional',
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
   *  'attachments': array,
   *  'campaign': string,
   *  'customHeaders': array,
   *  'description': string,
   *  'from': string,
   *  'html': string,
   *  'inlineCss': boolean,
   *  'inlineImages': array,
   *  'metadata': array,
   *  'recipientList': string,
   *  'recipients': array,
   *  'replyTo': string,
   *  'rfc822': string,
   *  'sandbox': boolean,
   *  'startTime': string,
   *  'subject': string,
   *  'substitutionData': array,
   *  'template': string,
   *  'text': string,
   *  'trackClicks': boolean,
   *  'trackOpens': boolean,
   *  'transactional': boolean,
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
}

?>
