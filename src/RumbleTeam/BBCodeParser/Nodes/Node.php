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
    const TYPE_TEXT = 'plaintext';
    const TYPE_ROOT = 'root';
    const TYPE_TAG = 'tag';

    /**
     * @var string
     */
    private $type;

    /**
     * @var TagNode
     */
    private $parent = null;

    /**
     * @param $type
     */
    public function __construct($type)
    {
        switch ($type)
        {
            case self::TYPE_TEXT:
            case self::TYPE_ROOT:
            case self::TYPE_TAG:
                break;
            default:
                throw new \InvalidArgumentException('Type invalid. see class-constants.');
        }

        $this->type = $type;
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        return !$this->hasParent();
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
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

    abstract function render();
}