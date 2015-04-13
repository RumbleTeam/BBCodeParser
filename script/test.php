<?php
/**
 * @Author Christian Seidlitz
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
    TagDefinition::create('url', false, array('url')),
    TagDefinition::create('xrl', true, array('url')),
    TagDefinition::create('pre', false, array('br', 'div'))
);

$testSet = array(
    array(
        'in'  => '123',
        'out' => '123'
    ),
    array(
        'in'  => '[br]',
        'out' => '<br>'
    ),
    array(
        'in'  => '[/br]',
        'out' => '[/br]'
    ),
    array(
        'in'  => '[img src="http://www.bild.de"]',
        'out' => '<img src="http://www.bild.de">'
    ),
    array(
        'in'  => '[alf]',
        'out' => '[alf]'
    ),
    array(
        'in'  => '[div]test[/div]',
        'out' => '<div>test</div>'
    ),
    array(
        'in'  => ' [div][div] gw[br] e[/div]test ',
        'out' => ' <div><div> gw<br> e</div>test </div>'
    ),
    array(
        'in'  => ' [pre][div] gw[br] [/div]test',
        'out' => ' <pre>[div] gw[br] [/div]test</pre>'
    ),
    array(
        'in'  => ' [pre][div] gw[br] [/div]test[/pre]alal[br]off',
        'out' => ' <pre>[div] gw[br] [/div]test</pre>alal<br>off'
    ),
    array(
        'in' => '[div]x[br]x[/div]',
        'out' => '<div>x<br>x</div>'
    ),
    array(
        'in' => '[url=http://www.asdf.com/bla/ a=tfd b="asd" c=asd /]',
        'out' => '<url a="tfd" b="asd" c="asd">'
    ),
    array(
        'in' => '[url=http://www.asdf.com/bla/ a=tfd b="asd" c="asd"/]',
        'out' => '<url a="tfd" b="asd" c="asd">'
    ),
    array(
        'in' => '[url="http://www.asdf.com/bla/"/]',
        'out' => '<url>'
    ),
    array(
        'in' => '[url=http://www.asdf.com/bla/]',
        'out' => '<url></url>'
    ),
    array(
        'in' => '[xrl=http://www.asdf.com/bla/ a=tfd b="asd" c=asd]',
        'out' => '<xrl a="tfd" b="asd" c="asd">'
    ),
    array(
        'in' => '[xrl=http://www.asdf.com/bla/ a=tfd b="asd" c="asd"/]',
        'out' => '<xrl a="tfd" b="asd" c="asd">'
    ),
    array(
        'in' => '[xrl="http://www.asdf.com/bla/"]',
        'out' => '<xrl>'
    ),
    array(
        'in' => '[xrl=http://www.asdf.com/bla/]',
        'out' => '<xrl>'
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

$limit = 100000;
$limit = 0;
$i = $limit;
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
if ($limit > 0) {
    echo 'Time for ' . $limit . ' runs:    ' . ($end - $start) . PHP_EOL;
}
