<?php
/**
 * Created by PhpStorm.
 * User: Vittel
 * Date: 17.12.14
 * Time: 23:12
 */

namespace RumbleTeam\BBCodeParser;


class Token
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $match;

    /**
     * @var array
     */
    private $attributes = array();

    /**
     * @var string
     */
    private $type = self::TYPE_UNDEFINED;

    const TYPE_UNDEFINED = 'undefined';
    const TYPE_TEXT = 'text';
    const TYPE_TAG_OPENING = 'opening';
    const TYPE_TAG_CLOSING = 'closing';
    const TYPE_TAG_SELF_CLOSING = 'selfClosing';


    static $initialized = false;
    static $tagRegex = null;
    static $attributeRegex = null;

    private static function __static()
    {
        if (!self::$initialized)
        {
            $unnamedNameRegex = '\w+[\d\w]*';
            $regexSymbols = '\d\w_,.?!@#$%&*()^=:\+\-\'';
            $quotedSymbols = '\s\/' . $regexSymbols;
            $namedNameRegex = '(?<NAME>' . $unnamedNameRegex . ')';

            $unnamedValueRegex = '(?:\"[' . $quotedSymbols . ']*\"|[' . $regexSymbols . ']*)';
            $namedValueRegex = '(?:\"(?<QUOTED_VALUE>[' . $quotedSymbols . ']*)\"|(?<VALUE>[' . $regexSymbols . ']*))';

            $unnamedAttributesRegex =
                '(?:'
                . $unnamedNameRegex
                . '\s*\=\s*'
                . $unnamedValueRegex
                . ')';

            self::$attributeRegex =
                '/'
                . $namedNameRegex
                . '\s*\=\s*'
                . $namedValueRegex
                . '/S';

            self::$tagRegex =
                '/\[(?<CLOSING>\/?)'
                . $namedNameRegex
                . '(?:\s*\=\s*'
                . $namedValueRegex
                . ')?(?<ATTRIBUTES>(?:\s+'
                . $unnamedAttributesRegex
                . ')*)?\s*(?<SELF_CLOSING>\/)?\]/S';

            self::$initialized = true;
        }
    }

    /**
     * @param string $match
     */
    public function __construct($match)
    {
        $this->match = $match;
    }

    /**
     * @param string $input
     * @return string[] stack of text-token
     */
    public static function tokenize($input)
    {
        self::__static();
        $matchCount = preg_match_all(self::$tagRegex, $input, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE, 0);

        //print_r($matches);
        $tokenList = array();
        if (empty($matchCount))
        {
            self::addToken($tokenList, $input);
        }
        else
        {
            //print_r($matches);
            $endOfLastMatch = 0;
            foreach ($matches as $match)
            {
                //print_r($match);
                $fullMatch = $match[0][0];
                $position = $match[0][1];
                $length = strlen($fullMatch);
                $endPosition = $position + $length;

                //echo "End Position:" . $endPosition . PHP_EOL;

                $textLength = $position - $endOfLastMatch;
                if ($textLength > 0)
                {
                    $textTokenContent = substr($input, $endOfLastMatch, $textLength);
                    self::addToken($tokenList, $textTokenContent);
                }

                $endOfLastMatch = $endPosition;

                $name = strtoupper($match['NAME'][0]);
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

                $attributes = self::parseAttributes($match['ATTRIBUTES'][0]);
                self::addToken($tokenList, $fullMatch, $name, $value, $attributes, $closing, $selfClosing);
            }

            $endOfInput = strlen($input);

            if ($endOfInput > $endOfLastMatch)
            {
                $textTokenContent = substr($input, $endOfLastMatch, $endOfInput-$endOfLastMatch);
                self::addToken($tokenList, $textTokenContent);
            }
        }

        return $tokenList;
    }

    private static function addToken(
        &$tokenList,
        $match,
        $name = '',
        $value = '',
        $attributes = array(),
        $closing = false,
        $selfClosing = false
    ) {
        $token = new Token($match);
        $token->setName($name);
        $token->setValue($value);
        $token->setAttributes($attributes);

        if (empty($name))
        {
            $type = self::TYPE_TEXT;
        }
        else
        {
            $type = self::TYPE_TAG_OPENING;
            if ($closing)
            {
                $type = self::TYPE_TAG_CLOSING;
            }
            else if ($selfClosing)
            {
                $type = self::TYPE_TAG_SELF_CLOSING;
            }
        }

        $token->setType($type);
        $tokenList[] = $token;
    }

    private static function parseAttributes($text)
    {
        $attributes = array();

        preg_match_all(self::$attributeRegex, $text, $matches, PREG_SET_ORDER);
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

        return $attributes;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    public static function isTagType($type)
    {
        $result = $type == self::TYPE_TAG_OPENING
            || $type == self::TYPE_TAG_CLOSING
            || $type == self::TYPE_TAG_SELF_CLOSING;
        return $result;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    private function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    private function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     */
    private function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }
}