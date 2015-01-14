<?php
/**
 * @Author Christian Seidlitz
 * Date: 22.12.2014
 * Time: 19:17
 */

namespace RumbleTeam\BBCodeParser;

class BBCodeTokenizer
{
    /**
     *
     * @var BBCodeTokenizer
     */
    private static $instance = null;

    /**
     * @var string
     */
    private $tagRegex = '';

    /**
     * @var string
     */
    private $attributeRegex = '';

    /**
     * Initializes a new instance of a BBCodeTokenizer.
     */
    private function __construct()
    {
        $unnamedNameRegex = '\w+[\d\w]*';
        $regexSymbols = '\d\w_,.?!@#$%&*()^=:\+\-\'';
        $quotedSymbols = '\s\/' . $regexSymbols;
        $namedNameRegex = '(?<NAME>' . $unnamedNameRegex . ')';

        $unnamedValueRegex =
            '(?:\"['
            . $quotedSymbols
            . ']*\"|['
            . $regexSymbols
            . ']*)';

        $namedValueRegex =
            '(?:\"(?<QUOTED_VALUE>['
            . $quotedSymbols
            . ']*)\"|(?<VALUE>['
            . $regexSymbols
            . ']*))';

        $unnamedAttributesRegex =
            '(?:'
            . $unnamedNameRegex
            . '\s*\=\s*'
            . $unnamedValueRegex
            . ')';

        $this->attributeRegex =
            '/'
            . $namedNameRegex
            . '\s*\=\s*'
            . $namedValueRegex
            . '/S';

        $this->tagRegex =
            '/\[(?<CLOSING>\/?)'
            . $namedNameRegex
            . '(?:\s*\=\s*'
            . $namedValueRegex
            . ')?(?<ATTRIBUTES>(?:\s+'
            . $unnamedAttributesRegex
            . ')*)?\s*(?<SELF_CLOSING>\/)?\]/S';
    }

    /**
     * Creates and returns a singleton instance of the BBCode Tokenizer.
     *
     * @return BBCodeTokenizer
     */
    public static function instance()
    {
        if (self::$instance === null)
        {
            self::$instance = new BBCodeTokenizer();
        }

        return self::$instance;
    }

    /**
     * Runs tokenizing on the given input.
     *
     * @param string $input
     * @return string[] stack of text-token
     */
    public function tokenize($input)
    {
        $matchCount = preg_match_all(
            $this->tagRegex,
            $input,
            $matches,
            PREG_SET_ORDER | PREG_OFFSET_CAPTURE,
            0
        );

        $tokenList = array();
        if (empty($matchCount))
        {
            self::addToken($tokenList, $input);
        }
        else
        {
            $endOfLastMatch = 0;
            foreach ($matches as $match)
            {
                $fullMatch = $match[0][0];
                $position = $match[0][1];
                $length = strlen($fullMatch);
                $endPosition = $position + $length;

                $textLength = $position - $endOfLastMatch;
                if ($textLength > 0)
                {
                    $textTokenContent = substr($input, $endOfLastMatch, $textLength);
                    self::addToken($tokenList, $textTokenContent);
                }

                $endOfLastMatch = $endPosition;

                $name = $match['NAME'][0];
                $closing = !empty($match['CLOSING'][0]);
                $selfClosing = !empty($match['SELF_CLOSING'][0]);

                if (isset($match['VALUE_QUOTED']))
                {
                    $value = $match['VALUE_QUOTED'][0];
                }
                else
                {
                    $value = $match['VALUE'][0];
                }

                $attributes = array();
                $attributesText = trim($match['ATTRIBUTES'][0]);
                if (!$closing && !empty($attributesText))
                {
                    $attributes = self::parseAttributes($attributesText);
                }

                self::addToken(
                    $tokenList,
                    $fullMatch,
                    $name,
                    $value,
                    $attributes,
                    $closing,
                    $selfClosing
                );
            }

            $endOfInput = strlen($input);

            if ($endOfInput > $endOfLastMatch)
            {
                $textTokenContent = substr(
                    $input,
                    $endOfLastMatch,
                    $endOfInput-$endOfLastMatch
                );

                self::addToken($tokenList, $textTokenContent);
            }
        }

        return $tokenList;
    }

    /**
     * @param string $text parses attributes from a
     * bb-tag attribute string like >> a="x s" b=c <<
     * @return string[] Attributes ['a'=>'x s', 'b'=>'c']
     */
    private function parseAttributes($text)
    {

        $attributes = array();
        if (!empty($text))
        {
            preg_match_all(
                $this->attributeRegex,
                $text,
                $matches,
                PREG_SET_ORDER
            );

            foreach ($matches as $match)
            {
                if (isset($match['QUOTED_VALUE']))
                {
                    $value = $match['QUOTED_VALUE'];
                }
                else
                {
                    $value = $match['VALUE'];
                }

                $attributes[$match['NAME']] = $value;
            }
        }

        return $attributes;
    }

    /**
     * Adding a token to the token list.
     *
     * @param $tokenList
     * @param $match
     * @param string $name
     * @param string $value
     * @param array $attributes
     * @param bool $closing
     * @param bool $selfClosing
     */
    private function addToken(
        &$tokenList,
        $match,
        $name = '',
        $value = '',
        $attributes = array(),
        $closing = false,
        $selfClosing = false
    ) {
        if (empty($name))
        {
            $type = Token::TYPE_TEXT;
        }
        else
        {
            $type = Token::TYPE_TAG_OPENING;
            if ($closing)
            {
                $type = Token::TYPE_TAG_CLOSING;
            }
            else if ($selfClosing)
            {
                $type = Token::TYPE_TAG_SELF_CLOSING;
            }
        }

        $token = new Token($match, $name, $value, $attributes, $type);
        $tokenList[] = $token;
    }
}