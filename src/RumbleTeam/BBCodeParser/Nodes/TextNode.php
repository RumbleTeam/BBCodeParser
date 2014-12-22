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
        parent::__construct(self::TYPE_TEXT);
        $this->content = $token->getMatch();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    function render()
    {
        return $this->getContent();
    }
}