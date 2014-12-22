<?php
/**
 * Created by PhpStorm.
 * User: Christian.Seidlitz
 * Date: 17.12.2014
 * Time: 15:46
 */

namespace RumbleTeam\BBCodeParser;

use RumbleTeam\BBCodeParser\Nodes\ContainerNode;
use RumbleTeam\BBCodeParser\Nodes\Node;
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
            $definitions[strtoupper($definition->getName())] = $definition;
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
        $html = $this->render($bbCodeTree);
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
        /**
         * @var $token Token
         */
        $token = current($tokenList);
        do
        {
            $match = $token->getMatch();
            if ($isTag = Token::isTagType($tokenType = $token->getType())
                && isset($this->definitions[$tokenName = $token->getName()])
            ) {
                /** @var TagDefinitionInterface $definition */
                $definition = $this->definitions[$tokenName];
                $isVoid = $definition->isVoid();
                if ($isVoid)
                {
                    switch ($tokenType)
                    {
                        case Token::TYPE_TAG_OPENING:
                        case Token::TYPE_TAG_SELF_CLOSING:
                            $parent->add(new TagNode($definition, $token));
                            break;
                        default:
                            $parent->add(new TextNode($match));
                    }
                }
                else
                {
                    switch ($tokenType)
                    {
                        case Token::TYPE_TAG_OPENING:
                            $newNode = new TagNode($definition, $token);
                            $parent->add($newNode);
                            $parent = $newNode;
                            break;
                        case Token::TYPE_TAG_CLOSING:
                            if ($parent instanceof TagNode
                                && $parent->hasParent()
                                && $parent->getName() == $tokenName
                            ) {
                                $parent = $parent->getParent();
                            }
                            else
                            {
                                $parent->add(new TextNode($token->getMatch()));
                            }
                            break;
                        default:
                            $parent->add(new TextNode($match));
                    }
                }

            }
            else
            {
                $parent->add(new TextNode($match));
            }
        }
        while ($token = next($tokenList));
    }

    /**
     * @param Node $node
     * @return string
     */
    private function render(Node $node)
    {
        $result = '';
        switch ($node->getType())
        {
            case Node::TYPE_TEXT:
                /** @var $node TextNode */
                $result .= $node->getContent();
                break;
            case Node::TYPE_TAG:
                /** @var $node TagNode */
                if ($node->hasChildren())
                {
                    $renderedChildren = '';
                    foreach ($node->getChildren() as $child)
                    {
                        $renderedChildren .= $this->render($child);
                    }

                    $result .= $node->getDefinition()->render(
                        $node->getValue(),
                        $node->getAttributes(),
                        $renderedChildren
                    );
                }
                else
                {
                    $result .= $node->getDefinition()->render(
                        $node->getValue(),
                        $node->getAttributes()
                    );
                }

                break;
            case RootNode::TYPE_ROOT:
                /** @var $node RootNode */
                foreach ($node->getChildren() as $child)
                {
                    $result .= $this->render($child);
                }
        }

        return $result;
    }
}