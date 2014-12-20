<?php
/**
 * Created by PhpStorm.
 * User: Vittel
 * Date: 20.12.2014
 * Time: 14:44
 */

namespace RumbleTeam\BBCodeParser\Tags;


class CallbackTagDefinition extends TagDefinition
{
    /**
     * @var \Closure
     */
    private $callback;

    /**
     * @param string $name
     * @param bool $void
     * @param callable $renderCallback name/value/attributes/content
     */
    public function __construct($name, $void, \Closure $renderCallback)
    {
        parent::__construct($name, $void);

        $this->callback = $renderCallback;
    }

    /**
     * @param string $value
     * @param array $attributes
     * @param string $content
     * @return string
     */
    public function render($value = '', $attributes = array(), $content = '')
    {
        $name = $this->getName();
        $renderFunction = $this->callback;
        $renderFunction($name, $value, $attributes, $content);
    }
}