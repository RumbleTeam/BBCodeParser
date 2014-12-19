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

    public function __construct(TagDefinitionInterface $definition, Token $token)
    {
        parent::__construct(self::TYPE_TAG);

        $this->definition = $definition;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getMatch()
    {
        return $this->token->getMatch();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->token->getName();
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->token->getValue();
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->token->getAttributes();
    }

    /**
     * @return TagDefinition
     */
    public function getDefinition()
    {
        return $this->definition;
    }
}