<?php
/**
 * Created by PhpStorm.
 * User: Christian.Seidlitz
 * Date: 17.12.2014
 * Time: 15:46
 */

namespace RumbleTeam\BBCodeParser;

use RumbleTeam\BBCodeParser\Nodes\TreeNode;
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
        $tokenStack = $this->tokenize($text);
        $bbCodeTree = $this->lex($tokenStack);
        $html = $this->render($bbCodeTree);
        return $html;
    }

    /**
     * @param string $input
     * @return string[] stack of text-token
     */
    public function tokenize($input)
    {
        // Scan the input and break it down into possible tags and body text.
        $symbols = '\d\w_,.?!@#$%&*()^=:\+\-\'\/';
        $symbolsWithWhitespace = '\s' . $symbols;
        $assignment = '\s*\=\s*(?:\"['.$symbolsWithWhitespace.']*\"|['.$symbols.']*)';
        $name = '\w+[\d\w]*';
        $regex = '/(\[\/?'.$name.'(?:'.$assignment.')?(?:\s+'.$name . $assignment.')*\/?\])/';

        $tokenStack = array_reverse(preg_split($regex, $input, null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE));

        return $tokenStack;
    }

    public function lex(array $scanChunks)
    {
        $state = new BBCodeParserState($scanChunks);
        $rootNode = new TreeNode();
        $this->parseChildren($state, $rootNode);
        return $rootNode;
    }


    private function parseChildren(BBCodeParserState $state, TreeNode $parent)
    {
        $break = false;
        while ($tokenText = $state->next())
        {
            $token = $this->getTokenForTokenText($tokenText);
            switch ($token->getType())
            {
                case Token::TYPE_OPENING:
                    $newNode = new TreeNode($token);
                    $parent->add($newNode);
                    $this->parseChildren($state, $newNode);
                    break;
                case Token::TYPE_CLOSING:
                    $break = ($parent->getName() == $token->getName());
                    break;
                default:
                    $parent->add(new TreeNode($token->getText()));
            }
            if ($break) {break;}
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
     * @param TreeNode $bbCodeNode
     * @return string
     */
    private function render(TreeNode $bbCodeNode)
    {
        // traverse tree (recursively call render)
        // given by bbcode definitions: render opening part, render all children, render closing part
        // render by callbacks? or use class as definition which contains render-methods or can use template engine.
    }
}