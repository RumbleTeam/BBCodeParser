<?php
/**
 * Created by PhpStorm.
 * User: Christian Seidlitz
 * Date: 19.12.2014
 * Time: 14:38
 */

set_include_path(realpath('../src'));
spl_autoload_register();

use RumbleTeam\BBCodeParser\BBCodeParser;
use RumbleTeam\BBCodeParser\Tags\TagDefinition;

$definitions = array(
    TagDefinition::create('br', true),
    TagDefinition::create('div', false),
    TagDefinition::create('img', true),
);

$parser = new BBCodeParser($definitions);
$input = ' 123 [url=albern und=er] 2[img href="something"/][br][/img href="something"] 34 [div][tataaa alf="cool"/] 3[z]45 [/url] 4[/x]56 ';

$start = microtime(true);
$result = $parser->parse($input);
$end = microtime(true);

echo 'IN  >|' . $input . '|<' . PHP_EOL;
echo 'OUT >|' . $result . '|<' . PHP_EOL;
echo "Time:" . ($end-$start) . PHP_EOL;
