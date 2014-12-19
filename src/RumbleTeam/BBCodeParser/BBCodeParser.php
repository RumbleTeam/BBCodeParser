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
     * @param Node $bbCodeNode
     * @return string
     */
    private function render(Node $bbCodeNode)
    {
        // traverse tree (recursively call render)
        // given by bbcode definitions: render opening part, render all children, render closing part
        // render by callbacks? or use class as definition which contains render-methods or can use template engine.
    }
}