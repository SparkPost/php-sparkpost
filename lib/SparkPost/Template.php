<?php
namespace SparkPost;

/**
 * SDK interface for managing templates 
 */
class Template extends APIResource {

  public $endpoint = 'templates';

  /**
   * Mapping for values passed into the send method to the values needed for the Transmission API
   * @var array
   */
  protected static $parameterMappings = [
    'customHeaders'=>'content.headers',
    'description'=>'description',
    'from'=>'content.from',
    'html'=>'content.html',
    'id'=>'id',
    'name'=>'name',
    'published'=>'published',
    'replyTo'=>'content.reply_to',
    'rfc822'=>'content.email_rfc822',
    'subject'=>'content.subject',
    'trackClicks'=>'options.click_tracking',
    'trackOpens'=>'options.open_tracking',
    'transactional'=>'options.transactional',
    'text'=>'content.text',
    'substitution_data'=>'substitution_data'
    ];

  /**
   * Sets up default structure and default values for the model that is acceptable by the API
   * @var array
   */
  protected static $structure = [ ];


  /**
   * Method for retrieving information about all templates
   *  Wrapper method for a cleaner interface
   *
   * @return array result Set of templates
   */
  public function all() {
    $options = [];

    return $this->get( null, $options );
  }

  /**
   * Method for retrieving information about a single template
   *  Wrapper method for a cleaner interface
   *
   * @param string $templateID Identifier of the template to be found
   * @return array result Single template represented in key-value pairs
   */
  public function find($templateID) {
    return $this->get($templateID);
  }

  /**
   * Method for retrieving a preview of a template rendered with 
   * substitutionData
   *
   * @param string $templateID Identifier of the template to be found
   * @param bool $draft If true, previews the most recent draft template. If false, previews the most recent published template. 
   * @return array result Template rendered with substituted data
   */
  public function preview($templateID, $draft, $substituionData) {
      $templateID = urlencode($templateID);
      $draft = ( $draft == 0 ) ? "true" : "false";
      return $this->callResource('post', $templateID.'/preview', ['query' => ['draft' => $draft], 'body' => ['substitution_data' => $substituionData]]);
  }
}

?>
