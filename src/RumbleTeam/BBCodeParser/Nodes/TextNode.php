<?php
/**
 * Created by PhpStorm.
 * User: Vittel
 * Date: 18.12.2014
 * Time: 23:38
 */

namespace RumbleTeam\BBCodeParser\Nodes;


class TextNode extends Node
{
    /**
     * @var string
     */
    private $content;

    /**
     * @param string $content
     */
    public function __construct($content)
    {
        parent::__construct(self::TYPE_TEXT);

        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
}