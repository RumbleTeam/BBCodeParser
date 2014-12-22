<?php
/**
 * Created by PhpStorm.
 * User: Vittel
 * Date: 18.12.2014
 * Time: 23:38
 */

namespace RumbleTeam\BBCodeParser\Nodes;


use RumbleTeam\BBCodeParser\Token;

class TextNode extends Node
{
    /**
     * @var string
     */
    private $content;

    /**
     * @param Token $token
     */
    public function __construct(Token $token)
    {
        $this->content = $token->getMatch();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    function render()
    {
        return $this->getContent();
    }
}