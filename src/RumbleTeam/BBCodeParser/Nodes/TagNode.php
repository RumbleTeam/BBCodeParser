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

    /**
     * @param Token $token
     * @param TagDefinitionInterface $definition
     */
    public function __construct(Token $token, TagDefinitionInterface $definition)
    {
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

    /**
     * @return string
     */
    function render()
    {
        return $this->definition->render(
            $this->token->getName(),
            $this->token->getValue(),
            $this->token->getAttributes(),
            parent::render()
        );
    }
}