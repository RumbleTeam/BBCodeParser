<?php
/**
 * Created by PhpStorm.
 * User: Vittel
 * Date: 19.12.2014
 * Time: 14:38
 */

// Your custom class dir
define('CLASS_DIR', realpath('../src'));

// Add your class dir to include path
set_include_path(get_include_path().PATH_SEPARATOR.CLASS_DIR);

// You can use this trick to make autoloader look for commonly used "My.class.php" type filenames
spl_autoload_extensions('.php');

// Use default autoload implementation
spl_autoload_register();

try
{
    \RumbleTeam\BBCodeParser\Token\Token::tokenizeDirect('alf [alal] test');
}
catch (\Exception $ex)
{
    var_dump($ex);
}