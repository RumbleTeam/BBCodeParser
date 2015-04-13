<?php
/**
 * @Author: Christian Seidlitz (Christian.Seidlitz@gmx.com)
 * @Date: 10.04.2015
 * @Time: 17:50
 */
namespace RumbleTeam\BBCodeParser\Tags;

use Closure;

/**
 * Class CallbackTagDefinition
 */
class CallbackTagDefinition extends TagDefinition
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * Initializes a new instance of the CallbackTagDefinition class.
     *
     * @param string $id
     * @param Closure $callback
     * @param bool $void
     * @param array $childBlackList
     */
    public function __construct($id, Closure $callback, $void = false, $childBlackList = array())
    {
        parent::__construct($id, $void, $childBlackList);
        $this->callback = $callback;
    }

    /**
     * @param TagInterface $tag
     * @param string $content
     * @return mixed
     */
    public function render(TagInterface $tag, $content = '')
    {
        $cb = $this->callback;
        return $cb($tag, $content);
    }
}