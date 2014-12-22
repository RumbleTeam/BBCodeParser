<?php
/**
 * Created by PhpStorm.
 * User: Vittel
 * Date: 17.12.14
 * Time: 21:40
 */

namespace RumbleTeam\BBCodeParser\Nodes;

use RumbleTeam\BBCodeParser\Tags\TagDefinition;
use RumbleTeam\BBCodeParser\Tags\TagDefinitionInterface;
use RumbleTeam\BBCodeParser\Token;

class TagNode extends ContainerNode
{
    /**
     * @var TagDefinition
     */
    private $definition;

    /**
     * @var \RumbleTeam\BBCodeParser\Token
     */
    private $token;

    public function __construct(Token $token, TagDefinitionInterface $definition)
    {
        parent::__construct(self::TYPE_TAG);

        $this->definition = $definition;
        $this->token = $token;
    }

    /**
     * @return TagDefinition
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }

    function render()
    {
        $token = $this->getToken();
        return $this->getDefinition()->render(
            $token->getValue(),
            $token->getAttributes(),
            parent::render()
        );
    }
}