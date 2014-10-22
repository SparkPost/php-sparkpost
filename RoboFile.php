<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    public function test () {
//     	$coverage = new PHP_CodeCoverage();
//     	$coverage->start('PHP-SDK Unit Tests');
//     	$this->taskPHPUnit('./test/unit/')->run();
    	$res = $this->taskExec('phpunit --coverage-html test/output/report --bootstrap ./vendor/autoload.php ./test/unit')->run();
    	
    	// print message when tests passed
    	if ($res->wasSuccessful()) $this->say("All tests passed");
    	
//     	$coverage->stop();
//     	$writer = new PHP_CodeCoverage_Report_HTML;
//     	$writer->process($coverage, 'test/output/report');
    	
    	return $res();
	}
}