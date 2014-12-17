<?php
/**
 * Created by PhpStorm.
 * User: Vittel
 * Date: 17.12.14
 * Time: 21:40
 */

namespace RumbleTeam\BBCodeParser\Nodes;


class TreeNode
{
    /**
     * @var TreeNode
     */
    private $parent;

    /**
     * @var TreeNode[]
     */
    private $children = array();

    /**
     * @var string
     */
    private $nodeText;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $attributes;

    public function __construct($nodeText = '', $name = '', array $attributes = array())
    {

        $this->nodeText = $nodeText;
        $this->name = $name;
        $this->attributes = $attributes;
    }

    /**
     * @return \RumbleTeam\BBCodeParser\BBCodeNode
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param \RumbleTeam\BBCodeParser\BBCodeNode $parent
     */
    public function setParent($parent)
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
     * @return bool
     */
    public function hasChildren()
    {
        return !empty($this->children);
    }

    /**
     * @return \RumbleTeam\BBCodeParser\BBCodeNode[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Adds a child to the node
     * @param \RumbleTeam\BBCodeParser\BBCodeNode $node
     */
    public function add(TreeNode $node)
    {
        $this->children[] = $node;
        $node->setParent($this);
    }

    /**
     * @return string
     */
    public function getNodeText()
    {
        return $this->nodeText;
    }

    /**
     * @param string $nodeText
     */
    public function setNodeText($nodeText)
    {
        $this->nodeText = $nodeText;
    }
}