<?php
/**
 * @Author: Christian Seidlitz (Christian.Seidlitz@gmx.de)
 * @Date: 10.04.2015
 * @Time: 17:50
 */
namespace RumbleTeam\BBCodeParser\Tags;

/**
 * Class CallbackTagDefinition
 */
class ReplaceTagDefinition extends TagDefinition
{
    /**
     * @var bool
     */
    private $template;

    /**
     * Initializes a new instance of the ReplaceTagDefinition class.
     *
     * @param string $id
     * @param $template
     * @param bool $void
     * @param array $childBlackList
     */
    public function __construct($id, $template, $void = false, $childBlackList = array())
    {
        parent::__construct($id, $void, $childBlackList);
        $this->template = $template;
    }

    /**
     * @param TagInterface $tag
     * @param string $content
     * @return string
     */
    public function render(TagInterface $tag, $content = '')
    {
        $result = $this->template;

        $result = str_replace('{content}', $content, $result);
        $result = str_replace('{value}', $tag->getValue(), $result);

        foreach ($tag->getAttributes() as $name=>$value) {
            $result = str_replace('{'.$name.'}', $value, $result);
        }

        return $result;
    }
}