<?php
/**
 * @Author Christian Seidlitz
 * Date: 19.12.2014
 * Time: 19:55
 */

namespace RumbleTeam\BBCodeParser\Tags;


interface TagDefinitionInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return bool
     */
    public function isVoid();

    /**
     * @param $id
     * @return bool
     */
    public function isLegalChildId($id);

    /**
     * @param TagInterface $tag
     * @param string $content
     * @return string
     */
    public function render(TagInterface $tag, $content = '');
}