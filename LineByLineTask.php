<?php

/**
 * Line By Line Task
 * 
 * The class allows to stagger complex tasks processed on individual lines of the text file
 * Reads one line of an input file per one call
 * Runs limited times per one PHP process
 * Remebers the read pointer and continues from the last stop point
 * 
 * @version 1.0.1
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
	 * @var resource 
	 */
	private $inputHandle;

	/**
	 *
	 * @var resource 
	 */
	private $outputHandle;

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
	 * @param string $outfilename	outpuf file path. May be helpfull for loging or as
	 * 								a main result keeper
	 * @param string $tmpfilename	tmp file path. In this file will be written current
	 * 								input file pointer position.
	 */
	public function __construct($infilename, $callback, $outfilename = null, $tmpfilename = 'tmp.txt')
	{
		$this->checkFiles($infilename, $outfilename, $tmpfilename);
		$this->inputHandle = fopen($infilename, "r");
		$this->outputHandle = null !== $outfilename ? fopen($outfilename, "a") : null;
		$this->tmpfilename = $tmpfilename;
		$this->callback = $callback;
	}

	/**
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
	 * @param string $infilename
	 * @param string $outfilename
	 * @param string $tmpfilename
	 * @throws Exception
	 */
	protected function checkFiles($infilename, $outfilename, $tmpfilename)
	{
		if (!is_readable($infilename)) {
			throw new Exception("Unable to locate an input file {$infilename}");
		}

		if (null !== $outfilename) {
			$outpath = realpath(dirname($outfilename));
			if (!is_writable($outpath)) {
				throw new Exception("Unable to write output to {$outpath}");
			}
		}

		$tmppath = realpath(dirname($tmpfilename));
		if (!is_writable($tmppath)) {
			throw new Exception("Unable to write tmp data to {$tmppath}");
		}
	}

}
