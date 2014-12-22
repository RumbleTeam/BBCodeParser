<?php
/**
 * Created by PhpStorm.
 * User: Vittel
 * Date: 18.12.2014
 * Time: 23:40
 */

namespace RumbleTeam\BBCodeParser\Nodes;

class ContainerNode extends Node
{
    /**
     * @var TagNode[]
     */
    private $children = array();

    /**
     * @return Node[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Adds a child to the node
     * @param Node $node
     */
    public function add(Node $node)
    {
        $this->children[] = $node;
        $node->setParent($this);
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return !empty($this->children);
    }

    /**
     * @return string
     */
    public function render()
    {
        $result = '';
        foreach ($this->children as $child)
        {
            $result .= $child->render();
        }

        return $result;
    }
}