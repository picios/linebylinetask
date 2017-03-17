<?php

/**
 * Line By Line Task
 * 
 * The class allows to stagger complex tasks processed on individual lines of the text file
 * Reads one line of an input file per one call
 * Runs limited times per one PHP process
 * Remebers the read pointer and continues from the last stop point
 * 
 * @version 1.0.3
 * @author Picios
 * @copyright (c) 2017, Picios
 * @link pcios.pl website
 * @link https://github.com/picios/linebylinetask github repository
 * @license https://github.com/github/hubot/blob/master/LICENSE.md MIT License
 */

namespace Picios\Lib;

class LineByLineTask
{

	/**
	 *
	 * @var string 
	 */
	private $inputfilename;
	
	/**
	 *
	 * @var resource 
	 */
	private $inputHandle;

	/**
	 *
	 * @var resource 
	 */
	private $outputHandle = null;

	/**
	 *
	 * @var string 
	 */
	private $tmpfilename;

	/**
	 *
	 * @var callable 
	 */
	private $callback;

	/**
	 *
	 * @var integer 
	 */
	private $limit = 100;

	/**
	 * 
	 * @param string $infilename	input file path, which will be read line by line
	 * @param callable $callback	callable function((string) $line, (resource) $outputHandle)
	 * 								The main task function, which will be called after
	 * 								a line beeing read
	 * @param string $tmpfilename	tmp file path. In this file will be written current
	 * 								input file pointer position.
	 */
	public function __construct($infilename, $callback, $tmpfilename = null)
	{
		$this->setInputFile($infilename);
		$this->checkIfWrittable($tmpfilename);
		$this->setTmpFile($tmpfilename);
		$this->callback = $callback;
	}
	
	/**
	 * Set input file
	 * 
	 * @param string $filename
	 * @return \LineByLineTask
	 */
	public function setInputFile($filename)
	{
		$this->checkIfReadable($filename);
		$this->inputfilename = $filename;
		$this->inputHandle = fopen($filename, "r");
		
		return $this;
	}

	/**
	 * Optional
	 * 
	 * @param integer $limit	limit of operations per one php process
	 * @return \LineByLineTask
	 */
	public function setLimit($limit)
	{
		$this->limit = $limit;

		return $this;
	}
	
	/**
	 * Optional output file
	 * It's handle will be passed to callback function as a second argument
	 * 
	 * @param string $filename
	 * @return \LineByLineTask
	 */
	public function setOutputFile($filename)
	{
		$this->checkIfWrittable($filename);
		$this->outputHandle = fopen($filename, "a");
		
		return $this;
	}
	
	/**
	 * 
	 * @param resource $outputHandle
	 * @return \LineByLineTask
	 */
	public function setOutput($outputHandle)
	{
		$this->outputHandle = $outputHandle;
		
		return $this;
	}

	/**
	 * Runs the callable function limited times
	 * Sends arguments
	 * - string $line				the read line of an input file
	 * - resource $outputHandle		
	 */
	public function run()
	{
		$this->setPointer();
		$counter = 0;
		while (($line = fgets($this->inputHandle)) !== false) {
			call_user_func_array($this->callback, array(
				$line,
				$this->outputHandle
			));
			$counter++;
			if ($counter >= $this->limit) {
				break;
			}
		}
		$this->stop();
	}
	
	/**
	 * Resets read pointer
	 */
	public function reset()
	{
		file_put_contents($this->tmpfilename, '0');
		fseek($this->inputHandle, 0);
	}
	
	/**
	 * 
	 * @param type $filename
	 */
	protected function setTmpFile($filename = null)
	{
		if (null === $filename) {
			// creating tmp file attempt
			$this->tmpfilename = '/tmp/' . md5($this->inputfilename) . '.txt';
		} else {
			$this->tmpfilename = $filename;
		}
	}

	/**
	 * Reads and sets pointer to last position
	 */
	protected function setPointer()
	{
		if (!file_exists($this->tmpfilename)) {
			file_put_contents($this->tmpfilename, '0');
		}

		$ftell = (int) file_get_contents($this->tmpfilename);
		fseek($this->inputHandle, $ftell);
	}

	/**
	 * Stops processing
	 */
	protected function stop()
	{
		if (null !== $this->outputHandle) {
			fclose($this->outputHandle);
		}
		file_put_contents($this->tmpfilename, ftell($this->inputHandle));
		fclose($this->inputHandle);
	}
	
	/**
	 * 
	 * @param string $filename
	 * @throws Exception
	 */
	protected function checkIfReadable($filename)
	{
		if (!is_readable($filename)) {
			throw new \Exception("Unable to locate file {$filename}");
		}
	}
	
	/**
	 * 
	 * @param string $filename
	 * @throws Exception
	 */
	protected function checkIfWrittable($filename)
	{
		$tmppath = realpath(dirname($filename));
		if (!is_writable($tmppath)) {
			throw new \Exception("Unable to write data to {$tmppath}");
		}
	}

}
