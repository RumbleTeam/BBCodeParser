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
        $html = $this->render($tokenList);
        return $html;
    }

    private function render(array $tokenList)
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
                            $result = $this->renderParent($parentTag, $result);
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

        // one while to close them all.
        while ($lastElementPosition >= 0)
        {
            $parentTag = array_pop($resultStack);
            $lastElementPosition--;
            $result = $this->renderParent($parentTag, $result);
        }

        return $result;
    }

    /**
     * @param array $parentTagArray
     * @param string $result
     * @return string
     */
    private function renderParent($parentTagArray, $result)
    {
        /** @var TagDefinitionInterface $parentDefinition */
        $parentDefinition = $parentTagArray[1];
        /** @var Token $parentToken */
        $parentToken = $parentTagArray[2];
        $parentResult = $parentTagArray[3];
        $parentResult .= $parentDefinition->render(
            $parentToken->getName(),
            $parentToken->getValue(),
            $parentToken->getAttributes(),
            $result
        );

        return $parentResult;
    }
}