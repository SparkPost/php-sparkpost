<?php
namespace SparkPost;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

/**
 * @desc SDK interface for managing transmissions
 */
class Transmission extends APIResource {
	
	public static $endpoint = 'transmissions';
	
	/**
	 * @desc Mapping for values passed into the send method to the values needed for the Transmission API 
	 * @var array
	 */
	protected static $parameterMappings = array(
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
		'template'=>'content.template_id',
		'trackOpens'=>'options.open_tracking',
		'trackClicks'=>'options.click_tracking',
		'useDraftTemplate'=>'use_draft_template'
	);
	
	/**
	 * @desc Sets up default structure and default values for the model that is acceptable by the API
	 * @var array
	 */
	protected static $structure = array(
		'return_path'=>"default@sparkpostmail.com",
		'content'=>array(
			'html'=>null, 
			'text'=>null,
			'email_rfc822'=>null
		),
		'use_draft_template'=>false
	);
	
	/**
	 * @desc Method for issuing POST request to the Transmissions API
	 *
	 *  This method assumes that all the appropriate fields have
	 *  been populated by the user through configuration.  Acceptable 
	 *  configuration values are: 
	 *  'campaign': string, 
	 *	'metadata': array,
	 *	'substitutionData': array,
	 *	'description': string,
	 *	'replyTo': string,
	 *	'subject': string,
	 *	'from': string,
	 *	'html': string,
	 *	'text': string,
	 *	'rfc822': string,
	 *	'customHeaders': array,
	 *	'recipients': array,
	 *	'recipientList': string,
	 *	'template': string,
	 *	'trackOpens': boolean,
	 *	'trackClicks': boolean,
	 *	'useDraftTemplate': boolean 
	 *
	 * @return array API repsonse represented as key-value pairs
	 */
	public static function send( $transmissionConfig ) {
		return self::sendRequest( $transmissionConfig );
	}
	
	/**
	 * @desc Method for retrieving information about all transmissions
	 *  Wrapper method for a cleaner interface
	 *  
	 * @return array result Set of transmissions
	 */
	public static function all( $campaignID=null, $templateID=null ) {
		$options = array();
		if( $campaignID !== NULL ) $options['campaign_id'] = $campaignID;
		if( $templateID !== NULL ) $options['template_id'] = $templateID;
		
		return self::fetchResource( null, $options ); 
	}
	
	/**
	 * @desc Method for retrieving information about a single transmission
 	 *  Wrapper method for a cleaner interface
 	 *  
	 * @param string $transmissionID Identifier of the transmission to be found
	 * @return array result Single transmission represented in key-value pairs
	 */
	public static function find($transmissionID) {
		return self::fetchResource($transmissionID);
	}
	
	public static function delete( $transmissionID ) {
		return self::deleteResource($transmissionID);
	}
}

?>