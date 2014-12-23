<?php
/**
 * Created by PhpStorm.
 * User: Vittel
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