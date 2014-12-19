<?php
/**
 * Created by PhpStorm.
 * User: Christian Seidlitz
 * Date: 19.12.2014
 * Time: 14:38
 */

set_include_path(realpath('../src'));
spl_autoload_register();
$parser = new \RumbleTeam\BBCodeParser\BBCodeParser(array());
$input = ' 123 [url=albern und=er] 234 [tataaa alf="cool"/] 345 [/url] 456 ';
$start = microtime(true);
$result = $parser->parse($input);
$end = microtime(true);
echo 'IN  >|' . $input . '|<' . PHP_EOL;
echo 'OUT >|' . $result . '|<' . PHP_EOL;
echo "Time:" . ($end-$start) . PHP_EOL;
