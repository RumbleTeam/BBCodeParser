<?php
/**
 * Created by PhpStorm.
 * User: Christian Seidlitz
 * Date: 19.12.2014
 * Time: 14:38
 */

set_include_path(realpath('../src'));
spl_autoload_register();
$start = microtime(true);
$tokenList = \RumbleTeam\BBCodeParser\Token\Token::tokenize(
    ' 123 [url] 234 [tataaa/] 345 [/url] 456 '
);
$parser = new \RumbleTeam\BBCodeParser\BBCodeParser(array());
$tree = $parser->lex($tokenList);
$end = microtime(true);
print_r($tokenList);
print_r($tree);
echo "Time:" . ($end-$start) . PHP_EOL;
