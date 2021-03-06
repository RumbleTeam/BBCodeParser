<?php
/**
 * @Author Christian Seidlitz
 * Date: 17.12.2014
 * Time: 15:46
 */

namespace RumbleTeam\BBCodeParser;

use Closure;
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
     * @var callable
     */
    private $textRenderCallback;

    /**
     * @var bool
     */
    private $autoClose;

    /**
     * @param TagDefinitionInterface[] $bbCodeDefinitions
     * @param int $maxCodes
     * @param callable $textRenderCallback
     * @param bool $autoClose
     */
    public function __construct(array $bbCodeDefinitions, $maxCodes = 0, Closure $textRenderCallback = null, $autoClose = true)
    {
        $definitions = array();
        foreach ($bbCodeDefinitions as $definition)
        {
            $definitions[Token::getIdForName($definition->getId())] = $definition;
        }

        $this->definitions = $definitions;
        $this->maxCodes = $maxCodes;
        $this->autoClose = $autoClose;

        $this->textRenderCallback = $textRenderCallback === null
            ? function($text) {return $text;}
            : $textRenderCallback;
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
        $this->validate($tokenList);
        $html = $this->render($tokenList);
        return $html;
    }

    /**
     * Checks the tokenlist for validity and updates it.
     *
     * @param $tokenList
     */
    private function validate($tokenList)
    {
        reset($tokenList);
        $openingTags = array();

        /** @var $token Token */
        $token = current($tokenList);

        do
        {
            if ($isTag = Token::isTagType($tokenType = $token->getType())
                && isset($this->definitions[$tokenId = $token->getId()])
            ) {
                /** @var TagDefinitionInterface $definition */
                $definition = $this->definitions[$tokenId];

                if($token->getType() == Token::TYPE_TAG_OPENING && !$definition->isVoid() && !$this->autoClose) {
                    $openingTags[] = $token;
                }

                if($token->getType() == Token::TYPE_TAG_CLOSING) {
                    if($openingTags && end($openingTags)->getName() == $token->getName())
                    {
                        array_pop($openingTags);
                    }
                }
            }
        }
        while ($token = next($tokenList));

        /** @var $invalidToken Token */
        foreach($openingTags as $invalidToken){
            $invalidToken->setType(Token::TYPE_TEXT);
        }
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
        $renderText = $this->textRenderCallback;

        /** @var $token Token */
        $token = current($tokenList);

        do
        {
            if ($isTag = Token::isTagType($tokenType = $token->getType())
                && isset($this->definitions[$tokenId = $token->getId()])
                //&& $this->isLegalChild($parentStack, $tokenId)
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
                            $result .= $this->renderTokenDefinition($parentStack, $definition, $token);
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
                        $result .= $this->renderTokenDefinition($parentStack, $definition, $token);
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
                            $parentTagData = array_pop($parentStack);
                            $lastElementPosition--;
                            $result = $this->renderParent($parentStack, $parentTagData, $result, $token);
                        }
                        else
                        {
                            $result .= $renderText($token->getMatch());
                        }
                        break;
                    default:
                        $result .= $renderText($token->getMatch());
                }
            }
            else
            {
                $result .= $renderText($token->getMatch());
            }
        }
        while ($token = next($tokenList));

        // one while to close them all.
        while ($lastElementPosition >= 0)
        {
            $parentTagData = array_pop($parentStack);
            $lastElementPosition--;
            $result = $this->renderParent($parentStack, $parentTagData, $result);
        }

        return $result;
    }

    /**
     * Renders the tag which is one level above the current
     * content, thus enclosing the content
     *
     * @param array $parentStack
     * @param array $parentTagData
     * @param string $content
     * @param Token $closingToken
     * @return string
     */
    private function renderParent(array $parentStack, $parentTagData, $content, Token $closingToken = null)
    {
        /** @var TagDefinitionInterface $definition */
        $definition = $parentTagData[1];

        /** @var Token $openingToken */
        $openingToken = $parentTagData[2];

        /** @var string $parentResultState */
        $parentResultState = $parentTagData[3];

        // Append the rendering result of the current parent to the original result, then return the whole.
        $parentResultState .= $this->renderTokenDefinition($parentStack, $definition, $openingToken, $content, $closingToken);

        return $parentResultState;
    }

    private function renderTokenDefinition(array $parentStack, TagDefinitionInterface $definition, Token $openingToken, $content = '', Token $closingToken = null)
    {
        $renderText = $this->textRenderCallback;
        $result = '';

        if ($this->isLegalChild($parentStack, $definition->getId()) &&
            (
                $openingToken->isSelfClosing()
                || $definition->isVoid()
                || $this->autoClose
                || $closingToken != null
            )
        ) {
            $result .= $definition->render($openingToken, $content);
        }
        else
        {
            $result .= $renderText($openingToken->getMatch());

            if (!$definition->isVoid())
            {
                $result .= $content;
            }

            if ($closingToken != null)
            {
                $result .= $renderText($closingToken->getMatch());
            }
        }

        return $result;
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