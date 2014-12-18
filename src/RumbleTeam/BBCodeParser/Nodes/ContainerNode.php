<?php
/**
 * Created by PhpStorm.
 * User: Vittel
 * Date: 18.12.2014
 * Time: 23:40
 */

namespace RumbleTeam\BBCodeParser\Nodes;


abstract class ContainerNode extends Node
{
    /**
     * @var TagNode[]
     */
    private $children = array();

    /**
     * @return \RumbleTeam\BBCodeParser\Nodes\TagNode[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Adds a child to the node
     * @param \RumbleTeam\BBCodeParser\Nodes\Node $node
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
}