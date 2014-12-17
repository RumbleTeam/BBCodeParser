<?php
/**
 * Created by PhpStorm.
 * User: Vittel
 * Date: 17.12.14
 * Time: 23:12
 */

namespace RumbleTeam\BBCodeParser\Token;


class Token
{
    /**
     * @var string
     */
    private $text = '';

    private $type = self::TYPE_UNDEFINED;

    const TYPE_OPENING = 'opening';
    const TYPE_CLOSING = 'closing';
    const TYPE_SELF_CLOSING = 'selfClosing';
    const TYPE_TEXT = 'text';
    const TYPE_UNDEFINED = 'undefined';

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}