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
    private $id;

    /**
     * @var bool
     */
    private $void;

    /**
     * @param string $id
     * @param bool $void
     */
    public function __construct($id, $void = false)
    {
        $this->id = $id;
        $this->void = $void;
    }

    public static function create($id, $void = false)
    {
        return new TagDefinition($id, $void);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isVoid()
    {
        return $this->void;
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $attributes
     * @param string $content
     * @return string
     */
    public function render($name, $value = '', $attributes = array(), $content = '')
    {
        $result = '';

        $result .= '<' . $this->id;

        foreach ($attributes as $key => $value)
        {
            $result .= ' ' . $key . '="' . $value . '"';
        }

        $result .= '>';

        if (!$this->void)
        {
            $result .= $content . '</' . $this->id . '>';
        }

        return $result;
    }
}