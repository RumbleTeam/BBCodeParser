<?php
/**
 * Created by PhpStorm.
 * User: Vittel
 * Date: 18.12.2014
 * Time: 23:54
 */

namespace RumbleTeam\BBCodeParser\Nodes;

class RootNode extends ContainerNode
{
    public function __construct()
    {
        parent::__construct(self::TYPE_ROOT);
    }
}