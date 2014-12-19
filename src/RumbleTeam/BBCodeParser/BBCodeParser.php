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
use RumbleTeam\BBCodeParser\Token\Token;

class BBCodeParser
{
    /**
     * @var array
     */
    private $bbCodeDefinitions;

    public function __construct(array $bbCodeDefinitions)
    {
        $this->bbCodeDefinitions = $bbCodeDefinitions;
    }

    /**
     * @param string $text
     *
     * @return string html
     */
    public function parse($text)
    {
        $tokenList = Token::tokenize($text);
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
            switch ($token->getType())
            {
                case Token::TYPE_OPENING:
                    $newNode = new TagNode($token->getMatch(), $token->getName());
                    $parent->add($newNode);
                    $parent = $newNode;
                    break;
                case Token::TYPE_CLOSING:
                    if ($parent instanceof TagNode
                        && $parent->hasParent()
                        && $parent->getName() == $token->getName()
                    ) {
                        $parent = $parent->getParent();
                    }
                    else
                    {
                        $parent->add(new TextNode($token->getMatch()));
                    }
                    break;
                case Token::TYPE_SELF_CLOSING:
                    $parent->add(new TagNode($token->getMatch(), $token->getName()));
                    break;
                default:
                    $parent->add(new TextNode($token->getMatch()));
            }
        } while ($token = next($tokenList));
    }

    /**
     * @param Node $node
     * @return string
     */
    private function render(Node $node)
    {
        // traverse tree (recursively call render)
        // given by bbcode definitions: render opening part, render all children, render closing part
        // render by callbacks? or use class as definition which contains render-methods or can use template engine.

        $result = '';
        switch ($node->getType())
        {
            case Node::TYPE_TEXT:
                /** @var $node TextNode */
                $result .= $node->getContent();
                break;
            case Node::TYPE_TAG:
                /** @var $node TagNode */
                $name = $node->getName();
                if ($node->hasChildren())
                {
                    $result .= '<<' . $name . '>>';

                    foreach ($node->getChildren() as $child)
                    {
                        $result .= $this->render($child);
                    }

                    $result .= '<</' . $name . '>>';
                }
                else
                {
                    $result .= '<<' . $name . '/>>';
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