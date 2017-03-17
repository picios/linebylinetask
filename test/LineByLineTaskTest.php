<?php

require_once __DIR__ . '/../LineByLineTask.php';

/**
 * Description of LineByLineTaskTest
 *
 * @author picios
 */
class LineByLineTaskTest extends PHPUnit_Framework_TestCase
{
	/**
	 *
	 * @var string 
	 */
	private $inputfile;


	protected function setUp()
	{
		$this->inputfile = __DIR__ . '/test_input_file.txt';
	}
	
	public function testConstruct()
	{
		$test = new Picios\Lib\LineByLineTask($this->inputfile, function($line, $of) {
				echo $line;
			}, '/tmp/LineByLineTaskTest.tmp');
		$test->setLimit(3);
		$test->reset();
		$this->expectOutputRegex('/Rick Grimes/s');
		$test->run();
	}

	public function testOutput()
	{
		$test = new Picios\Lib\LineByLineTask($this->inputfile, function($line, $of) {
				echo $line;
				fwrite($of, $line);
			}, '/tmp/LineByLineTaskTest.tmp');
		$test->setLimit(3);
		$test->reset();
		$test->setOutput(fopen('php://stdin', 'w'));
		$this->expectOutputRegex('/Maggie Greene/s');
		$test->run();
	}
	
	public function testDefaultTmpFile()
	{
		$test = new Picios\Lib\LineByLineTask($this->inputfile, function($line, $of) {
				echo $line;
			});
		$test->setLimit(6);
		$test->reset();
		$this->expectOutputRegex('/Daryl Dixon/s');
		$test->run();
	}
	
	public function testInputException()
	{
		try {
			new Picios\Lib\LineByLineTask('not_existing_file.txt', function($line, $of) {
				echo $line;
			});
		} catch (Exception $e) {
			//echo $e->getMessage();
			$this->assertTrue(true);
		}
	}
	
	public function testTmpException()
	{
		try {
			new Picios\Lib\LineByLineTask($this->inputfile, function($line, $of) {
				echo $line;
			}, '/not/existing/directory/not_existing_file.txt');
		} catch (Exception $e) {
			//echo $e->getMessage();
			$this->assertTrue(true);
		}
	}
}
