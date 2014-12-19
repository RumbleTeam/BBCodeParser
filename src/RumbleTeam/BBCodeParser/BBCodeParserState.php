<?php
/**
 * Created by PhpStorm.
 * User: Christian.Seidlitz
 * Date: 17.12.2014
 * Time: 18:30
 */

namespace RumbleTeam\BBCodeParser;

use RumbleTeam\BBCodeParser\Token\Token;

class BBCodeParserState
{
    public function __construct(array $textTokenList) {
        $this->tokenList = $textTokenList;
        reset($this->tokenList);
    }

    public function next()
    {
        return next($this->tokenList);
    }

    /**
     * @var array
     */
    private $tokenList;

    /**
     * @var Token
     */
    public $token;
}