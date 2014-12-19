<?php
/**
 * Created by PhpStorm.
 * User: Vittel
 * Date: 17.12.14
 * Time: 23:12
 */

namespace RumbleTeam\BBCodeParser\Token;


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

    const TYPE_TEXT = 'text';
    const TYPE_OPENING = 'opening';
    const TYPE_CLOSING = 'closing';
    const TYPE_SELF_CLOSING = 'selfClosing';
    const TYPE_UNDEFINED = 'undefined';

    const REGEX_NAME = '\w+[\d\w]*';
    const REGEX_SYMBOLS = '\d\w_,.?!@#$%&*()^=:\+\-\'';

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
        $quotedSymbols = '\s\/' . self::REGEX_SYMBOLS;
        $namedNameRegex = '(?<NAME>' . self::REGEX_NAME . ')';

        $valueRegex = '(?:\"[' . $quotedSymbols . ']*\"|[' . self::REGEX_SYMBOLS . ']*)';
        $namedValueRegex = '(?:\"(?<QUOTED_VALUE>[' . $quotedSymbols . ']*)\"|(?<VALUE>[' . self::REGEX_SYMBOLS . ']*))';

        $attributeRegex = '(?:' . self::REGEX_NAME . '\s*\=\s*' . $valueRegex . ')';
        $namedAttributeRegex = '/' . $namedNameRegex . '\s*\=\s*' . $namedValueRegex . '/';

        $regex = '/\[(?<CLOSING>\/?)' . $namedNameRegex . '(?:\s*\=\s*' . $namedValueRegex . ')?(?<ATTRIBUTES>(?:\s+' . $attributeRegex . ')*)?\s*(?<SELF_CLOSING>\/)?\]/';

        //echo $regex.PHP_EOL;
        $matchCount = preg_match_all($regex, $input, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE, 0);

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

                $attributes = self::parseAttributes($namedAttributeRegex, $match['ATTRIBUTES'][0]);
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
            $type = self::TYPE_OPENING;
            if ($closing)
            {
                $type = self::TYPE_CLOSING;
            }
            else if ($selfClosing)
            {
                $type = self::TYPE_SELF_CLOSING;
            }
        }

        $token->setType($type);
        $tokenList[] = $token;
    }

    private static function parseAttributes($regex, $text)
    {
        $attributes = array();

        preg_match_all($regex, $text, $matches, PREG_SET_ORDER);
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