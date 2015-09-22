<?php
namespace SparkPost\Test;

use SparkPost\SparkPost;
use Ivory\HttpAdapter\CurlHttpAdapter;

class SparkPostTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @desc Ensures that the configuration class is not instantiable.
	 */
	public function testConstructorSetsUpTransmissions() {
		$sparky = new SparkPost(new CurlHttpAdapter(), ['key'=>'a key']);
		$this->assertEquals('SparkPost\Transmission', get_class($sparky->transmission));
	}
}
?>
