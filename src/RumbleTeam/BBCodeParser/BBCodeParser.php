<?php
/**
 * Created by PhpStorm.
 * User: Christian.Seidlitz
 * Date: 17.12.2014
 * Time: 15:46
 */

namespace RumbleTeam\BBCodeParser;

use RumbleTeam\BBCodeParser\Nodes\ContainerNode;
use RumbleTeam\BBCodeParser\Nodes\RootNode;
use RumbleTeam\BBCodeParser\Nodes\TagNode;
use RumbleTeam\BBCodeParser\Nodes\TextNode;
use RumbleTeam\BBCodeParser\Tags\TagDefinitionInterface;

class BBCodeParser
{
    /**
     * @var array
     */
    private $definitions;

    /**
     * @param TagDefinitionInterface[] $bbCodeDefinitions
     */
    public function __construct(array $bbCodeDefinitions)
    {
        $definitions = array();
        foreach ($bbCodeDefinitions as $definition)
        {
            $definitions[Token::getIdForName($definition->getName())] = $definition;
        }

        $this->definitions = $definitions;
    }

    /**
     * @param string $text
     *
     * @return string html
     */
    public function parse($text)
    {
        $tokenizer = BBCodeTokenizer::instance();
        $tokenList = $tokenizer->tokenize($text);
        $bbCodeTree = $this->lex($tokenList);
        $html = $bbCodeTree->render();
        return $html;
    }

    public function lex(array $tokenList)
    {
        $rootNode = new RootNode();
        $this->buildTree($tokenList, $rootNode);
        return $rootNode;
    }

    private function buildTree(array $tokenList, ContainerNode $parent)
    {
        reset($tokenList);

        /** @var $token Token */
        $token = current($tokenList);

        do
        {
            if ($isTag = Token::isTagType($tokenType = $token->getType())
                && isset($this->definitions[$tokenId = $token->getId()])
            ) {
                /** @var TagDefinitionInterface $definition */
                $definition = $this->definitions[$tokenId];
                $isVoid = $definition->isVoid();
                if ($isVoid)
                {
                    switch ($tokenType)
                    {
                        case Token::TYPE_TAG_OPENING:
                        case Token::TYPE_TAG_SELF_CLOSING:
                            $parent->add(new TagNode($token, $definition));
                            break;
                        default:
                            $parent->add(new TextNode($token));
                    }
                }
                else
                {
                    switch ($tokenType)
                    {
                        case Token::TYPE_TAG_OPENING:
                            $newNode = new TagNode($token, $definition);
                            $parent->add($newNode);
                            $parent = $newNode;
                            break;
                        case Token::TYPE_TAG_CLOSING:
                            if ($parent instanceof TagNode
                                && $parent->hasParent()
                                && $parent->getToken()->getId() == $tokenId
                            ) {
                                $parent = $parent->getParent();
                            }
                            else
                            {
                                $parent->add(new TextNode($token));
                            }
                            break;
                        default:
                            $parent->add(new TextNode($token));
                    }
                }
            }
            else
            {
                $parent->add(new TextNode($token));
            }
        }
        while ($token = next($tokenList));
    }
}