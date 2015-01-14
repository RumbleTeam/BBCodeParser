<?php
/**
 * @Author Christian Seidlitz
 * Date: 17.12.2014
 * Time: 15:46
 */

namespace RumbleTeam\BBCodeParser;

use RumbleTeam\BBCodeParser\Tags\TagDefinitionInterface;

class BBCodeParser
{
    /**
     * @var array
     */
    private $definitions;

    /**
     * @var int
     */
    private $maxCodes;

    /**
     * @param TagDefinitionInterface[] $bbCodeDefinitions
     * @param int $maxCodes
     */
    public function __construct(array $bbCodeDefinitions, $maxCodes = 0)
    {
        $definitions = array();
        foreach ($bbCodeDefinitions as $definition)
        {
            $definitions[Token::getIdForName($definition->getId())] = $definition;
        }

        $this->definitions = $definitions;
        $this->maxCodes = $maxCodes;
    }

    /**
     * @param string $text
     *
     * @param int $maxCodes
     * @return string html
     */
    public function parse($text, $maxCodes = 0)
    {
        $maxCodes = (($maxCodes > 0) && ($maxCodes < $this->maxCodes))
            ? $maxCodes
            : $this->maxCodes;

        $tokenizer = BBCodeTokenizer::instance();
        $tokenList = $tokenizer->tokenize($text, $maxCodes);
        $html = $this->render($tokenList);
        return $html;
    }

    /**
     * Direct rendering of all the tokens
     *
     * @param array $tokenList
     * @return string
     */
    private function render(array $tokenList)
    {
        reset($tokenList);
        $result = '';
        $parentStack = array();
        $lastElementPosition = -1;

        /** @var $token Token */
        $token = current($tokenList);

        do
        {
            if ($isTag = Token::isTagType($tokenType = $token->getType())
                && isset($this->definitions[$tokenId = $token->getId()])
                && $this->isLegalChild($parentStack, $tokenId)
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
                            $result .= $definition->render($token);
                        }
                        else
                        {
                            $parentStack[] = array(
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
                        $result .= $definition->render($token);
                        break;
                    case Token::TYPE_TAG_CLOSING:
                        // check if the tag matches the current parent.
                        // If so, an opened tag is closed. We can now
                        // use the parent of the formerly opened tag again
                        // to proceed.
                        if (!$isVoid
                            && $lastElementPosition >= 0
                            && $parentStack[$lastElementPosition][0] === $tokenId
                        ) {
                            $parentTag = array_pop($parentStack);
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
            $parentTag = array_pop($parentStack);
            $lastElementPosition--;
            $result = $this->renderParent($parentTag, $result);
        }

        return $result;
    }

    /**
     * Renders the tag which is one level above the current
     * content, thus enclosing the content
     *
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
        $parentResult .= $parentDefinition->render($parentToken, $result);

        return $parentResult;
    }

    /**
     * @param array $parentStack
     * @param string $tokenId
     *
     * @return bool
     */
    private function isLegalChild($parentStack, $tokenId)
    {
        $legal = true;
        foreach ($parentStack as $parentTagArray)
        {
            /** @var TagDefinitionInterface $parentDefinition */
            $parentDefinition = $parentTagArray[1];
            $legal = $parentDefinition->isLegalChildId($tokenId);
            if (!$legal)
            {
                break;
            }
        }

        return $legal;
    }
}