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
        $html = $this->directRender($tokenList);
        //$bbCodeTree = $this->lex($tokenList);
        //$html = $bbCodeTree->render();
        return $html;
    }

    public function lex(array $tokenList)
    {
        $rootNode = new ContainerNode();
        $this->buildTree($tokenList, $rootNode);
        return $rootNode;
    }

    private function directRender(array $tokenList)
    {
        reset($tokenList);
        $result = '';
        $resultStack = array();
        $lastElementPosition = -1;

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
                        if ($isVoid)
                        {
                            $result .= $definition->render(
                                $token->getName(),
                                $token->getValue(),
                                $token->getAttributes()
                            );
                        }
                        else
                        {
                            $resultStack[] = array(
                                $definition->getId(),
                                $definition,
                                $token,
                                $result
                            );

                            $lastElementPosition++;
                            $result = '';
                        }
                        break;
                    case Token::TYPE_TAG_SELF_CLOSING:
                        // normal node, but has no content.
                        $result .= $definition->render(
                            $token->getName(),
                            $token->getValue(),
                            $token->getAttributes()
                        );
                        break;
                    case Token::TYPE_TAG_CLOSING:
                        // check if the tag matches the current parent.
                        // If so, an opened tag is closed. We can now
                        // use the parent of the formerly opened tag again
                        // to proceed.
                        if (!$isVoid
                            && $lastElementPosition >= 0
                            && $resultStack[$lastElementPosition][0] === $tokenId
                        ) {
                            $parentTag = array_pop($resultStack);
                            $lastElementPosition--;
                            /** @var TagDefinitionInterface $parentDefinition */
                            $parentDefinition = $parentTag[1];
                            /** @var Token $parentToken */
                            $parentToken = $parentTag[2];
                            $oldResult = $parentTag[3];
                            $oldResult .= $parentDefinition->render(
                                $parentToken->getName(),
                                $parentToken->getValue(),
                                $parentToken->getAttributes(),
                                $result
                            );

                            $result = $oldResult;
                        }
                        else
                        {
                            $result .= $token->getMatch();
                        }
                        break;
                    default:
                        $result .= $token->getMatch();
                }
            }
            else
            {
                $result .= $token->getMatch();
            }
        }
        while ($token = next($tokenList));

        while ($lastElementPosition >= 0)
        {
            $parentTag = array_pop($resultStack);
            $lastElementPosition--;
            /** @var TagDefinitionInterface $parentDefinition */
            $parentDefinition = $parentTag[1];
            /** @var Token $parentToken */
            $parentToken = $parentTag[2];
            $oldResult = $parentTag[3];
            $oldResult .= $parentDefinition->render(
                $parentToken->getName(),
                $parentToken->getValue(),
                $parentToken->getAttributes(),
                $result
            );

            $result = $oldResult;
        }

        return $result;
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