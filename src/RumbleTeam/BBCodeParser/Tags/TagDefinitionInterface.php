<?php
/**
 * Created by PhpStorm.
 * User: Vittel
 * Date: 19.12.2014
 * Time: 19:55
 */

namespace RumbleTeam\BBCodeParser\Tags;


interface TagDefinitionInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return bool
     */
    public function isVoid();

    /**
     * @param string $value
     * @param array $attributes
     * @param string $content
     * @return string
     */
    public function render($value = '', $attributes = array(), $content = '');
}