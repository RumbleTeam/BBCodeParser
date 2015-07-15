<?php
/**
 * @Author Christian Seidlitz
 * Date: 19.12.2014
 * Time: 19:59
 */

namespace RumbleTeam\BBCodeParser\Tags;


class TagDefinition implements TagDefinitionInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var bool
     */
    private $void;

    /**
     * @var array
     */
    private $childBlackList;

    /**
     * @param string $id
     * @param bool $void
     * @param array $childBlackList
     */
    public function __construct($id, $void = false, $childBlackList = array())
    {
        $this->id = strtolower($id);
        $this->void = $void;
        $this->childBlackList = $childBlackList;
    }

    public static function create($id, $void = false, $childBlacklist = array())
    {
        return new TagDefinition($id, $void, $childBlacklist);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isVoid()
    {
        return $this->void;
    }

    /**
     * @param $id
     * @return bool
     */
    public function isLegalChildId($id)
    {
        return !in_array($id, $this->childBlackList);
    }

    /**
     * @param TagInterface $tag
     * @param string $content
     * @return string
     */
    public function render(TagInterface $tag, $content = '')
    {
        $result = '';

        $result .= '<' . $this->id;

        $result .= $this->renderAttributes($tag);

        $result .= $this->renderTagFinish($tag, $content);

        return $result;
    }

    /**
     * Renders the end-part of the tag.
     *
     * @param TagInterface $tag
     * @param $content
     * @return string
     *
     */
    protected function renderTagFinish(TagInterface $tag, $content)
    {
        $result = '';

        if ($this->isVoid())
        {
            $result .= '>';
        }
        else
        {
            if ($tag->isSelfClosing())
            {
                $result .= '/>';
            }
            else
            {
                $result .= '>' . $content . '</' . $this->id . '>';
            }
        }

        return $result;
    }

    /**
     * Renders the list of attributes including the leading space.
     *
     * @param TagInterface $tag
     * @return string
     */
    protected function renderAttributes(TagInterface $tag)
    {
        $result = '';

        foreach ($tag->getAttributes() as $key => $value) {
            $result .= ' ' . $key . '="' . $value . '"';
        }

        return $result;
    }


}