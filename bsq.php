<?php

/**
 * This the main file for the BSQ project.
 *
 * To generate a map :
 * - perl generator.pl [nbCharactersPerLine] [nbLines] [PercentageObstacles]
 *
 * To launch the resolver :
 * - php bsq.php map.txt
 *
 * @author Gregory KOENIG <koenig.gregory@epitech.eu>
 */

include_once('inc/algorithm.php');

$algorithm = new inc\Algorithm;
$algorithm->renderResolvedMap();