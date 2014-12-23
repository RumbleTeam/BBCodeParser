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

$testSet = array(
    array(
        'in'=>'123',
        'out'=>'123'
    ),
    array(
        'in'=>'[br]',
        'out'=>'<br>'
    ),
    array(
        'in'=>'[/br]',
        'out'=>'[/br]'
    ),
    array(
        'in'=>'[img src="http://www.bild.de"]',
        'out'=>'<img src="http://www.bild.de">'
    ),
    array(
        'in'=>'[alf]',
        'out'=>'[alf]'
    ),
    array(
        'in'=>'[div]test[/div]',
        'out'=>'<div>test</div>'
    ),
    array(
        'in'=>'[div][div]gwe[/div]test',
        'out'=>'<div><div>gwe</div>test</div>'
    ),
);

$parser = new BBCodeParser($definitions);

$successCount = 0;
$failureCount = 0;
$failures = '';
foreach ($testSet as $test)
{
    $result = $parser->parse($test['in']);
    if ($result === $test['out'])
    {
        $successCount++;
    }
    else
    {
        $failureCount++;
        $failures .= "In: '{$test['in']}', Out: '{$result}', Exp.: '{$test['out']}'" . PHP_EOL;
    }
}

echo 'Success: ' . $successCount . PHP_EOL;
echo 'Failure: ' . $failureCount . PHP_EOL;
if ($failureCount>0)
{
    echo 'Failures: ' . PHP_EOL . $failures;

}

$start = microtime(true);

$i = 100000;
while ($i > 0)
{
    foreach ($testSet as $test)
    {
        if ($i > 0)
        {
            $parser->parse($test['in']);
            $i--;
        }
    }
}

$end = microtime(true);

echo 'Time for 100000 runs:    ' . ($end-$start) . PHP_EOL;
