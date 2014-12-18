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
        $tokenStack = Token::tokenize($text);
        $bbCodeTree = $this->lex($tokenStack);
        $html = $this->render($bbCodeTree);
        return $html;
    }



    public function lex(array $scanChunks)
    {
        $state = new BBCodeParserState($scanChunks);
        $rootNode = new RootNode();
        $this->buildTree($state, $rootNode);
        return $rootNode;
    }


    private function buildTree(BBCodeParserState $state, ContainerNode $parent)
    {
        while ($tokenText = $state->next())
        {
            $token = $this->getTokenForTokenText($tokenText);
            switch ($token->getType())
            {
                case Token::TYPE_OPENING:
                    $newNode = new TagNode();
                    $parent->add($newNode);
                    $parent = $newNode;
                    break;
                case Token::TYPE_CLOSING:
                    if ($parent instanceof TagNode && $parent->hasParent() && $parent->getName() == $token->getName())
                    {
                        $parent = $parent->getParent();
                    }
                    break;
                default:
                    $parent->add(new TextNode($token->getText()));
            }
        }
    }

    /**
     * @param string $token
     * @return Token
     */
    private function getTokenForTokenText($token)
    {
        // read name and attributes. if not available return a plaintext node
        // check definitions if tag is self-closing
        // create node with given properties
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