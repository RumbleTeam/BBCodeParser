<?php
/**
 * Created by PhpStorm.
 * User: Christian.Seidlitz
 * Date: 17.12.2014
 * Time: 15:46
 */

namespace RumbleTeam\BBCodeParser;

use RumbleTeam\BBCodeParser\Nodes\ContainerNode;
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
            $definitions[Token::getIdForName($definition->getId())] = $definition;
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
        $rootNode = new ContainerNode();
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
                switch ($tokenType)
                {
                    case Token::TYPE_TAG_OPENING:
                        // New opening tag, ad it to the parent,
                        // then use the new node as parent until
                        // the correct closing tag shows up.
                        $newNode = new TagNode($token, $definition);
                        $parent->add($newNode);
                        if (!$isVoid)
                        {
                            $parent = $newNode;
                        }
                        break;
                    case Token::TYPE_TAG_SELF_CLOSING:
                        // normal node, but has no content.
                        $parent->add(new TagNode($token, $definition));
                        break;
                    case Token::TYPE_TAG_CLOSING:
                        // check if the tag matches the current parent.
                        // If so, an opened tag is closed. We can now
                        // use the parent of the formerly opened tag again
                        // to proceed.
                        if (!$isVoid
                            && $parent instanceof TagNode
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
            else
            {
                $parent->add(new TextNode($token));
            }
        }
        while ($token = next($tokenList));
    }
}