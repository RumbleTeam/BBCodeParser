<?php
/**
 * @Author Christian Seidlitz
 * Date: 23.12.2014
 * Time: 17:47
 */

namespace RumbleTeam\BBCodeParser\Tags;


interface TagInterface
{
    public function getName();
    public function getValue();
    public function getAttributes();
}