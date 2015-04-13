<?php
/**
 * @Author Christian Seidlitz
 * Date: 19.12.2014
 * Time: 19:59
 */

namespace RumbleTeam\BBCodeParser\Tags;


class TestTagDefinition extends TagDefinition
{
    public static function create($id, $void = false, $childBlacklist = array())
    {
        return new TestTagDefinition($id, $void, $childBlacklist);
    }

    /**
     * @param TagInterface $tag
     * @param string $content
     * @return string
     */
    public function render(TagInterface $tag, $content = '')
    {
        $result = '';

        $result .= '<' . $this->getId();

        $value = $tag->getValue();

        if (!empty($value)) {
            $result .= '="' . $value . '"';
        }

        foreach ($tag->getAttributes() as $key => $value)
        {
            $result .= ' ' . $key . '="' . $value . '"';
        }

        $result .= '>';

        if (!$this->isVoid() && !$tag->isSelfClosing())
        {
            $result .= $content . '</' . $this->getId() . '>';
        }

        return $result;
    }
}