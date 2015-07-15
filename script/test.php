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
use RumbleTeam\BBCodeParser\Tags\TestTagDefinition;

$definitions = array(
    TestTagDefinition::create('a'),
    TestTagDefinition::create('b'),
    TestTagDefinition::create('c'),

    TagDefinition::create('d'),

    TestTagDefinition::create('e'),
    TestTagDefinition::create('f'),

    TagDefinition::create('br', true),
    TagDefinition::create('div', false),
    TagDefinition::create('img', true),

    TestTagDefinition::create('url', false, array('url')),
    TestTagDefinition::create('xrl', true, array('url')),

    TagDefinition::create('pre', false, array('br', 'div')),

    TestTagDefinition::create('fnt', false),
    TestTagDefinition::create('xfont', true),

    TagDefinition::create('wrapped', false, array('wrapped')),
);

$testSet = array(
    array(
        'in'  => '123',
        'out' => '§123%',
        'nac' => '123'
    ),
    array(
        'in'  => '[br]',
        'out' => '<br>',
        'nac' => '<br>',
    ),
    array(
        'in'  => '[/br]',
        'out' => '§[/br]%',
        'nac' => '[/br]',
    ),
    array(
        'in' => '[img src="http://www.bild.de"]',
        'out' => '<img src="http://www.bild.de">',
        'nac' => '<img src="http://www.bild.de">',
    ),
    array(
        'in' => '[img src=http://www.bild.de]',
        'out' => '<img src="http://www.bild.de">',
        'nac' => '<img src="http://www.bild.de">',
    ),
    array(
        'in'  => '[alf]',
        'out' => '§[alf]%',
        'nac' => '[alf]',
    ),
    array(
        'in'  => '[div]test[/div]',
        'out' => '<div>§test%</div>',
        'nac' => '<div>test</div>',
    ),
    array(
        'in'  => ' [div][div] gw[br] e[/div]test ',
        'out' => '§ %<div><div>§ gw%<br>§ e%</div>§test %</div>',
        'nac' => ' [div]<div> gw<br> e</div>test ',
    ),
    array(
        'in'  => ' [pre][div] gw[br] [/div]test',
        'out' => '§ %<pre>§[div]%§ gw%§[br]%§ %§[/div]%§test%</pre>',
        'nac' => ' [pre]<div> gw<br> </div>test',
    ),
    array(
        'in'  => ' [pre][div] gw[br] [/div]test[/pre]alal[br]off',
        'out' => '§ %<pre>§[div]%§ gw%§[br]%§ %§[/div]%§test%</pre>§alal%<br>§off%',
        'nac' => ' <pre>[div] gw[br] [/div]test</pre>alal<br>off',
    ),
    array(
        'in' => '[div]x[br]x[/div]',
        'out' => '<div>§x%<br>§x%</div>',
        'nac' => '<div>x<br>x</div>',
    ),
    array(
        'in' => '[url=http://www.asdf.com/bla/ a=tfd b="asd" c=asd /]',
        'out' => '<url="http://www.asdf.com/bla/" a="tfd" b="asd" c="asd"/>',
        'nac' => '<url="http://www.asdf.com/bla/" a="tfd" b="asd" c="asd"/>',
    ),
    array(
        'in' => '[url=http://www.asdf.com/bla/ a=tfd b="asd" c="asd"/]',
        'out' => '<url="http://www.asdf.com/bla/" a="tfd" b="asd" c="asd"/>',
        'nac' => '<url="http://www.asdf.com/bla/" a="tfd" b="asd" c="asd"/>',
    ),
    array(
        'in' => '[url="http://www.asdf.com/bla/"/]',
        'out' => '<url="http://www.asdf.com/bla/"/>',
        'nac' => '<url="http://www.asdf.com/bla/"/>',
    ),
    array(
        'in' => '[url=http://www.asdf.com/bla/]',
        'out' => '<url="http://www.asdf.com/bla/"></url>',
        'nac' => '[url=http://www.asdf.com/bla/]',
    ),
    array(
        'in' => '[xrl=http://www.asdf.com/bla/ a=tfd b="asd" c=asd]',
        'out' => '<xrl="http://www.asdf.com/bla/" a="tfd" b="asd" c="asd">',
        'nac' => '<xrl="http://www.asdf.com/bla/" a="tfd" b="asd" c="asd">',
    ),
    array(
        'in' => '[xrl=http://www.asdf.com/bla/ a=tfd b="asd" c="asd"/]',
        'out' => '<xrl="http://www.asdf.com/bla/" a="tfd" b="asd" c="asd">',
        'nac' => '<xrl="http://www.asdf.com/bla/" a="tfd" b="asd" c="asd">',
    ),
    array(
        'in' => '[xrl="http://www.asdf.com/bla/"]',
        'out' => '<xrl="http://www.asdf.com/bla/">',
        'nac' => '<xrl="http://www.asdf.com/bla/">',
    ),
    array(
        'in' => '[xrl=http://www.asdf.com/bla/]',
        'out' => '<xrl="http://www.asdf.com/bla/">',
        'nac' => '<xrl="http://www.asdf.com/bla/">',
    ),
    array(
        'in' => '[FNT=comic sans ms]sdfs[/FNT]',
        'out' => '<fnt="comic sans ms">§sdfs%</fnt>',
        'nac' => '<fnt="comic sans ms">sdfs</fnt>',
    ),
    array(
        'in' => '[xFONT=comic sans ms]',
        'out' => '<xfont="comic sans ms">',
        'nac' => '<xfont="comic sans ms">',
    ),
    array(
        'in' => '[a=orange][b][c=15]c15ba[/c][/b][/a]',
        'out' => '<a="orange"><b><c="15">§c15ba%</c></b></a>',
        'nac' => '<a="orange"><b><c="15">c15ba</c></b></a>',
    ),
    array(
        'in' => '[wrapped][wrapped][a]wwaww[/a][/wrapped][/wrapped]',
        'out' => '<wrapped>§[wrapped]%<a>§wwaww%</a>§[/wrapped]%</wrapped>',
        'nac' => '<wrapped>[wrapped]<a>wwaww</a>[/wrapped]</wrapped>',
    ),
    array(
        'in' => '[wrapped][wrapped][a]wwaww[/a][/wrapped][/wrapped][/wrapped]',
        'out' => '<wrapped>§[wrapped]%<a>§wwaww%</a>§[/wrapped]%</wrapped>§[/wrapped]%',
        'nac' => '<wrapped>[wrapped]<a>wwaww</a>[/wrapped]</wrapped>[/wrapped]',
    ),
    array(
        'in' => '[wrapped][wrapped][wrapped][a]wwaww[/a][/wrapped][/wrapped]',
        'out' => '<wrapped>§[wrapped]%§[wrapped]%<a>§wwaww%</a>§[/wrapped]%§[/wrapped]%</wrapped>',
        'nac' => '[wrapped]<wrapped>[wrapped]<a>wwaww</a>[/wrapped]</wrapped>',
    ),
    // closed tags without corresponding opening-tags on the same level being auto-closed at the end
    array(
        'in' => '[a][b][/a][/b][c][b][/c][/b]',
        'out' => '<a><b>§[/a]%</b><c><b>§[/c]%</b></c></a>',
        'nac' => '[a]<b>[/a]</b>[c]<b>[/c]</b>',
    ),
    array(
        'in' => '[url=http://asd.org/x.php?1&amp;2]asd[/url]',
        'out' => '<url="http://asd.org/x.php?1&amp;2">§asd%</url>',
        'nac' => '<url="http://asd.org/x.php?1&amp;2">asd</url>',
    ),
//    array(
//        'in' => '[url=http://pl.xyz.com/video/?language[]=pl]xyz[/url]',
//        'out' => '<url="http://pl.xyz.com/video/?language[]=pl">xyz</url>'
//    ),
    array(
        'in' => '[url="http://pl.xyz.com/video/?language[]=pl"]xyz[/url]',
        'out' => '<url="http://pl.xyz.com/video/?language[]=pl">§xyz%</url>',
        'nac' => '<url="http://pl.xyz.com/video/?language[]=pl">xyz</url>',
    ),
    array(
        'in' => '[url="http://pl.xyz.com/video/?language[]=pl" b="asd{[]}xyz"]x{}[]yz[/url]',
        'out' => '<url="http://pl.xyz.com/video/?language[]=pl" b="asd{[]}xyz">§x{}[]yz%</url>',
        'nac' => '<url="http://pl.xyz.com/video/?language[]=pl" b="asd{[]}xyz">x{}[]yz</url>',
    ),
    array(
        'in' => '[d]asd[d/]',
        'out' => '<d>§asd%<d/></d>',
        'nac' => '[d]asd<d/>',
    ),
    // broken closing tag
    array(
        'in' => '[e]asd[/e[f]][/f]',
        'out' => '<e>§asd[/e%<f>§]%</f></e>',
        'nac' => '[e]asd[/e<f>]</f>',
    ),
);

$parsers = array(
    'out' => new BBCodeParser(
        $definitions,
        0,
        function ($text) {
            return '§' . $text . '%';
        },
        true
    ),
    'nac' => new BBCodeParser(
        $definitions,
        0,
        null,
        false
    )
);

$successCount = 0;
$failureCount = 0;
$failures = '';
foreach ($testSet as $test)
{
    /**
     * @var  $key
     * @var BBCodeParser $parser
     */
    foreach ($parsers as $key => $parser) {
        if (isset($test[$key])) {
            $result = $parser->parse($test['in']);
            if ($result === $test[$key]) {
                $successCount++;
            } else {
                $failureCount++;
                $failures .= "[" . $key . "] In: '{$test['in']}', Out: '{$result}', Exp.: '{$test[$key]}'" . PHP_EOL;
            }
        }
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
//$limit = 0;
$i = $limit;
while ($i > 0)
{
    foreach ($testSet as $test)
    {
        if ($i > 0)
        {
            foreach ($parsers as $parser)
            {
                $parser->parse($test['in']);
                $i--;
            }
        }
    }
}

$end = microtime(true);
if ($limit > 0) {
    echo 'Time for ' . $limit . ' runs:    ' . ($end - $start) . PHP_EOL;
}
