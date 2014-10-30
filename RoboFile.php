<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    public function test () {
    	$res = $this->taskExec('phpunit --coverage-html test/output/report --bootstrap test/unit/bootstrap.php ./test/unit')->run();
    	
    	// print message when tests passed
    	if ($res->wasSuccessful()) $this->say("All tests passed");
    	
    	return $res();
	}
}