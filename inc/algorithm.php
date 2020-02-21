<?php

/**
 * This file is part of the BSQ project.
 * It is in the "inc" folder in the "inc" namespace.
 *
 * @author Gregory KOENIG <koenig.gregory@epitech.eu>
 */

namespace inc;

/**
 * Algorithm class for the BSQ project to resolve the first maximum square
 */
class Algorithm
{
	/**
	 * Declaration of properties
	 */
	private $handle;
	private $content;
	private $strTotLines;
	private $intTotLines;
	private $accBuffer;
	private $line;
	private $tmpMap;
	private $count;
	private $maxSq;
	private $countY;
	private $coordY;
	private $totCoordY;
	private $errors;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		global $argv;
		include_once('errors.php');

		$this->handle      = @fopen($argv[1], 'r');
		$this->content     = @file_get_contents($argv[1]);
		$this->strTotLines = @fgets($this->handle);
		$this->strTotLines = @trim($this->strTotLines);
		$this->intTotLines = @intval($this->strTotLines);
		$this->accBuffer   = '';
		$this->line        = 1;
		$this->tmpMap      = [];
		$this->count       = 0;
		$this->maxSq       = 1;
		$this->countY      = 1;
		$this->coordY      = strval($this->countY);
		$this->totCoordY   = strval($this->intTotLines);
		$this->errors      = new Errors;
	}

	/**
	 * Convert the content of the map into integer of 0 and 1
	 * 
	 * @param  string  $line Content of a single line of the map
	 *
	 * @return boolean       FALSE if an error is detected
	 */
	public function convertToInt($line)
	{
		for ($i = 0; $i < strlen($line); $i++) {
			$char = substr($line, $i, 1);

			if ($this->errors->checkForbiddenChar($this->line, $char)
				=== FALSE) {
				return FALSE;
			}

			if ($char == 'o') {
				$char = 0;
			} elseif ($char == '.') {
				$char = 1;
			}

			$this->tmpMap[$this->count][$i] = $char;
		}
	}

	/**
	 * Find all the squares in the map
	 */
	public function findAllSquares()
	{
		for ($y = 1; $y < count($this->tmpMap); $y++) {
			for($x = 1; $x < count($this->tmpMap[0]); $x++) {
				if ($this->tmpMap[$y][$x] == 1) {
					$this->tmpMap[$y][$x] = min($this->tmpMap[$y - 1][$x],
						$this->tmpMap[$y][$x - 1],
						$this->tmpMap[$y - 1][$x - 1]) + 1;

					if ($this->tmpMap[$y][$x] > $this->maxSq) {
						$this->maxSq = $this->tmpMap[$y][$x];
					}
				}
			}
		}
	}

	/**
	 * Read the content of the map line by line
	 *
	 * @return boolean FALSE if an error is detected
	 */
	public function readByLine()
	{
		while (($buffer = fgets($this->handle, 4096)) !== FALSE) {
			$this->line      += 1;    
			$nbCharLine       = strlen($buffer) - 1;
			$totChar[]        = strlen($buffer) - 1;
			$this->accBuffer .= $buffer;

			if ($this->errors->checkEmptyLine($this->line, $buffer) === FALSE
				|| $this->errors->checkLengthLine($this->line, $totChar,
					$nbCharLine) === FALSE) {
				return FALSE;
			}
			
			$buffer = preg_replace('#\n#', '', $buffer);

			if ($this->convertToInt($buffer) === FALSE) {
				return FALSE;
			}
			
			$this->convertToInt($buffer);
			$this->count++;
			$this->findAllSquares();
		}
	}

	/**
	 * Draw the first biggest square found in the map
	 */
	public function drawSquare()
	{
		for ($y = 0; $y < count($this->tmpMap); $y++) {
			for($x = 0; $x < count($this->tmpMap[0]); $x++) {
				if ($this->tmpMap[$y][$x] == $this->maxSq) {
					$this->tmpMap[$y][$x] = 'X';

					for ($i = 0; $i <= $this->maxSq - 1; $i++) {
						for ($j = 0; $j <= $this->maxSq - 1; $j++) {
							$this->tmpMap[$y - $i][$x - $j] = 'X';
						}
					}

					break 2;
				}
			}
		}
	}

	/**
	 * Add spaces for the alignment of the ordinate column
	 * 
	 * @param  string $solvedMap Number of the row
	 *
	 * @return string $row       Number of the row
	 */
	public function addSpaces($row = NULL)
	{
		while (strlen($this->coordY) < strlen($this->totCoordY)) {
			$this->coordY = substr_replace($this->coordY, ' ', 0, 0);
		}

		if ($row == NULL) {
			$row = $this->coordY;
		} else {
			$row .= "\n$this->coordY";
		}

		return $row;
	}

	/**
	 * Fill the solved map in a string with the result stored in an array,
	 * reconvert the characters of the map and format it
	 * 
	 * @param  string $solvedMap Beginning of the solved map
	 * 
	 * @return string $solvedMap Complete solved map
	 */
	public function fillSolvedMap($solvedMap)
	{
		foreach ($this->tmpMap as $key => $l) {
			foreach ($l as $c) {
				if ($c != 'X' && $c != 0) {
					$c = '.';
				} elseif ($c === 0) {
					$c = 'o';
				}

				$solvedMap .= $c;
			}

			$this->countY++;
			$this->coordY = strval($this->countY);

			$solvedMap = $this->addSpaces($solvedMap);
		}

		$solvedMap = preg_replace('#[X]#', "\033[32;7;1mX\033[00m",
			$solvedMap);
		$solvedMap = preg_replace('#[.]#', "\033[34;7m.\033[00m", $solvedMap);
		$solvedMap = preg_replace('#[o]#', "\033[34;7mo\033[00m", $solvedMap);
		$solvedMap = preg_replace('#[\n0-9]+$#', '', $solvedMap);

		return $solvedMap;
	}

	/**
	 * Write in the terminal the final result with details
	 * 
	 * @param string $solvedMap Solved map with the first biggest square
	 */
	public function writeTerminal($solvedMap)
	{
		$totCharX = substr_count($solvedMap, 'X');
		$sideSq   = sqrt($totCharX);
		$time     = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];

		echo "\033[1;4mMap:\033[00m";
		echo "\n\n" . $this->content . "\n\n\n";
		echo "\033[1;4mResult:\033[00m";
		echo "\n\n" . $solvedMap . "\n\n\n";
		echo "\033[1;4mSize of the side square:\033[00m $sideSq\n";
		echo "\033[1;4mRun time of the script:\033[00m $time seconds.\n\n\n";
	}

	/**
	 * Final function which calls all other
	 */
	public function renderResolvedMap()
	{
		if ($this->errors->checkArgs() === FALSE
			|| $this->errors->checkFile($this->handle, $this->content) 
				=== FALSE
			|| $this->errors->checkLineValue($this->strTotLines,
				$this->intTotLines) === FALSE) {
			return;
		}

		if ($this->readByLine() === FALSE) {
			return;
		}
		
		$this->readByLine();

		if ($this->errors->checkFormatChar($this->accBuffer) === FALSE
			|| $this->errors->checkNbLines($this->line, $this->strTotLines) 
				=== FALSE
			|| $this->errors->checkEndFile($this->handle) === FALSE) {
			return;
		}

		fclose($this->handle);

		$this->drawSquare();

		$solvedMap = $this->addSpaces();
		$solvedMap = $this->fillSolvedMap($solvedMap);

		$this->writeTerminal($solvedMap);
	}
}