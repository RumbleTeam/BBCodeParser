<?php
/**
 * Created by PhpStorm.
 * User: Vittel
 * Date: 17.12.14
 * Time: 21:40
 */

namespace RumbleTeam\BBCodeParser\Nodes;


class TagNode extends ContainerNode
{
    /**
     * @var string
     */
    private $match;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var string
     */
    private $value;

    public function __construct($match = '', $name = '', $value = '', array $attributes = array())
    {
        parent::__construct(self::TYPE_TAG);

        $this->match = $match;
        $this->name = $name;
        $this->attributes = $attributes;
        $this->value = $value;
    }

    /**
     * @return bool
     */
    public function hasMatch()
    {
        return !empty($this->match);
    }

    /**
     * @return string
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * @param string $match
     */
    public function setMatch($match)
    {
        $this->match = $match;
    }

    /**
     * @return bool
     */
    public function hasName()
    {
        return !empty($this->name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function hasValue()
    {
        return !empty($this->value);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}