<?php
/**
 * @Author Christian Seidlitz
 * Date: 17.12.14
 * Time: 23:12
 */

namespace RumbleTeam\BBCodeParser;


use RumbleTeam\BBCodeParser\Tags\TagInterface;

class Token implements TagInterface
{

    const TYPE_UNDEFINED = 'undefined';
    const TYPE_TEXT = 'text';
    const TYPE_TAG_OPENING = 'opening';
    const TYPE_TAG_CLOSING = 'closing';
    const TYPE_TAG_SELF_CLOSING = 'selfClosing';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $match;

    /**
     * @var array
     */
    private $attributes = array();

    /**
     * @var string
     */
    private $type = self::TYPE_UNDEFINED;

    /**
     * @param string $match
     * @param string $name
     * @param string $value
     * @param string[] $attributes
     * @param string $type
     */
    public function __construct($match, $name, $value, $attributes, $type)
    {
        $this->match = $match;
        $this->name = $name;
        $this->value = $value;
        $this->attributes = $attributes;
        $this->type = $type;
    }

    public static function isTagType($type)
    {
        $result = $type == self::TYPE_TAG_OPENING
            || $type == self::TYPE_TAG_CLOSING
            || $type == self::TYPE_TAG_SELF_CLOSING;
        return $result;
    }

    public static function getIdForName($name)
    {
        $result = strtolower($name);
        return $result;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return self::getIdForName($this->name);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}