<?php
/**
 * Created by PhpStorm.
 * User: Vittel
 * Date: 19.12.2014
 * Time: 19:59
 */

namespace RumbleTeam\BBCodeParser\Tags;


class TagDefinition implements TagDefinitionInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $void;

    /**
     * @param string $name
     * @param bool $void
     */
    public function __construct($name, $void = false)
    {
        $this->name = $name;
        $this->void = $void;
    }

    public static function create($name, $void = false)
    {
        return new TagDefinition($name, $void);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isVoid()
    {
        return $this->void;
    }

    /**
     * @param string $value
     * @param array $attributes
     * @param string $content
     * @return string
     */
    public function render($value = '', $attributes = array(), $content = '')
    {
        $result = '';

        $result .= '<' . $this->getName();

        foreach ($attributes as $key=>$value)
        {
            $result .= ' ' . $key . '="' . $value . '"';
        }

        $result .= '>';

        if (!$this->isVoid())
        {
            $result .= $content . '</' . $this->getName() . '>';
        }

        return $result;
    }
}