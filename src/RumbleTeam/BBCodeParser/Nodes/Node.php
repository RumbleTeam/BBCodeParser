<?php
/**
 * Created by PhpStorm.
 * User: Vittel
 * Date: 18.12.2014
 * Time: 23:26
 */

namespace RumbleTeam\BBCodeParser\Nodes;

abstract class Node
{
    /**
     * @var TagNode
     */
    private $parent = null;

    /**
     * @return bool
     */
    public function isRoot()
    {
        return !$this->hasParent();
    }

    /**
     * @return \RumbleTeam\BBCodeParser\Nodes\TagNode
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param \RumbleTeam\BBCodeParser\Nodes\ContainerNode $parent
     */
    public function setParent(ContainerNode $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return bool
     */
    public function hasParent()
    {
        return $this->parent != null;
    }

    /**
     * @return string
     */
    abstract function render();
}