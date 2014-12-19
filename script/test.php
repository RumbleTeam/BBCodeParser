<?php
/**
 * Created by PhpStorm.
 * User: Christian Seidlitz
 * Date: 19.12.2014
 * Time: 14:38
 */

set_include_path(realpath('../src'));
spl_autoload_register();

$tokenList = \RumbleTeam\BBCodeParser\Token\Token::tokenize(
    ' 123 [/a=1 b=2 c=3/] 123 [/d="4 5" e="6 7" c=3/] 123 '
);

print_r($tokenList);