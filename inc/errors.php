<?php

/**
 * This file is part of the BSQ project.
 * It is in the "inc" folder in the "inc" namespace.
 *
 * @author Gregory KOENIG <koenig.gregory@epitech.eu>
 */

namespace inc;

/**
 * Errors class for the BSQ project to manage all the possible errors
 * If you notice that one or many are missing, please contact me.
 */
class Errors
{
	/**
	 * Declaration of properties
	 */
	private $argv;
	private $argvs;
	private $argc;
	private $info;
	private $path;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		global $argv, $argc;

		if (isset($argv[1])) {
			$this->argv = $argv[1];
		}

		$this->argvs = $argv;
		$this->argc  = $argc;
		$this->info  = @new \SplFileInfo($this->argvs[1]);
		$this->path  = @realpath($this->argvs[1]);
	}

	/**
	 * Check the different errors about given arguments in the terminal
	 * 
	 * @return boolean FALSE if an error is detected
	 */
	public function checkArgs()
	{
		if (!isset($this->argvs[1])) {
			echo "\033[31mError: Missing argument: one argument is required "
				. "(ex. \"php bsq.php [file].txt\").\n\033[00m";

			return FALSE;
		} elseif ($this->argvs[1] == NULL) {
			echo "\033[31mError: Null argument: the required argument must be "
				. "a file (ex. \"php bsq.php [file].txt\").\n\033[00m";

			return FALSE;
		} elseif ($this->info->getExtension() != 'txt') {
			echo "\033[31mError: Bad format argument: the argument must be a "
				. "text file (ex. \"php bsq.php [file].txt\").\n\033[00m";

			return FALSE;
		} elseif ($this->argc > 2) {
			echo "\033[31mError: Too much arguments: only one argument is "
				. "required (ex. \"php bsq.php [file].txt\").\n\033[00m";

			return FALSE;
		}
	}

	/**
	 * Check the different errors about the map
	 * 
	 * @param  resource $handle  Map file
	 * @param  string   $content Content of the file map
	 * 
	 * @return boolean           FALSE if an error is detected
	 */
	public function checkFile($handle, $content)
	{
		if (!$handle) {
			echo "\033[31mError: File \"$this->argv\" does not exist."
			. "\n\033[00m";

			return FALSE;
		} elseif ($content == '') {
			echo "\033[31mError: File \"$this->path\" is empty.\n\033[00m";

			return FALSE;
		}
	}

	/**
	 * Check the different errors about the map line value
	 * 
	 * @param  string  $strtotLines Total of lines
	 * @param  integer $inttotLines Total of lines
	 * 
	 * @return boolean              FALSE if an error is detected
	 */
	public function checkLineValue($strTotLines, $intTotLines)
	{
		if (preg_match('#^[.o]+$#', $strTotLines)) {
			echo "\033[31mError: Missing map line value: please set a line "
				. "value (\"$strTotLines\" in \"$this->path\" on line 1)."
				. "\n\033[00m";

			return FALSE;
		} elseif (!preg_match('#^[0-9]+$#', $strTotLines)
			&& $strTotLines != NULL) {
			echo "\033[31mError: Bad format map line value: it must be an "
				. "integer (\"$strTotLines\" in \"$this->path\" on line 1)."
				. "\n\033[00m";

			return FALSE;
		} elseif ($intTotLines == NULL) {
			echo "\033[31mError: Empty line value: this value must be at least"
				. " \"1\" (in \"$this->path\" on line 1).\n\033[00m";

			return FALSE;
		}
	}

	/**
	 * Check if the map is not empty
	 * 
	 * @param  integer $line        Current line
	 * @param  string  $contentLine Content of the current line
	 * 
	 * @return boolean              FALSE if an error is detected
	 */
	public function checkEmptyLine($line, $contentLine)
	{
		if ($contentLine == "\n" || $contentLine == '') {
			echo "\033[33mWarning: Empty line: there must be at least one "
				. "character (in \"$this->path\" on line $line).\n\033[00m";

			return FALSE;
		}
	}

	/**
	 * Check if all the lines are similar
	 * 
	 * @param  integer $line       Current line
	 * @param  array   $totChar    Total characters by line
	 * @param  integer $nbCharLine Total characters for the current line
	 * 
	 * @return boolean             FALSE if an error is detected
	 */
	public function checkLengthLine($line, $totChar, $nbCharLine)
	{
		$nbFirstLine = $totChar[0];

		if ($nbCharLine != $nbFirstLine) {
			echo "\033[33mWarning: the lines do not have the exact same length"
				. " ($nbCharLine character(s) instead of $nbFirstLine in "
				. "\"$this->path\" on line $line).\n\033[00m";

			return FALSE;
		}
	}

	/**
	 * Check if there are only "." and "o" in the map
	 * 
	 * @param  integer $line Current line
	 * @param  string  $char Current character
	 * 
	 * @return boolean       FALSE if an error is detected
	 */
	public function checkForbiddenChar($line, $char)
	{
		if (!preg_match('#[.o]#', $char)) {
			echo "\033[33mWarning: Forbidden character: only \".\" and \"o\" "
				. "characters are allowed (character \"$char\" in "
				. "\"$this->path\" on line $line).\n\033[00m";

			return FALSE;
		}
	}

	/**
	 * Check if there is at least one "." for generating a square
	 * 
	 * @param  string $content Content from the start to the current line
	 * 
	 * @return boolean         FALSE if an error is detected
	 */
	public function checkFormatChar($content)
	{
		if (!preg_match('#[.]+#', $content)) {
			echo "\033[31mError: Bad format character: there must be at least "
				. "one \".\" character to generate a square (in "
				. "\"$this->path\").\n\033[00m";

			return FALSE;
		}
	}

	/**
	 * Check if the number of lines match the map line value
	 * 
	 * @param  integer $lastLine Number of the last line
	 * @param  integer $totLines Total of lines
	 * 
	 * @return boolean           FALSE if an error is detected
	 */
	public function checkNbLines($lastLine, $totLines)
	{
		$lastLine -= 1;

		if ($lastLine < $totLines || $lastLine > $totLines) {
			echo "\033[33mWarning: Number of lines incorrect: the number does "
				. "not match the map line value (in \"$this->path\", $lastLine"
				. " line(s) instead of $totLines).\n\033[00m";

			return FALSE;
		}
	}

	/**
	 * Check if the function "fgets()" points at the end of the map
	 * 
	 * @param  resource $handle Map given in $argv[1]
	 * 
	 * @return boolean          FALSE if an error is detected
	 */
	public function checkEndFile($handle)
	{
		 if (!@feof($handle)) {
			echo "\033[33mWarning: function \"fgets()\" failed.\n\033[00m";

			return FALSE;
		}
	}
}