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
    private $text = '';

    private $type = self::TYPE_UNDEFINED;

    const TYPE_OPENING = 'opening';
    const TYPE_CLOSING = 'closing';
    const TYPE_SELF_CLOSING = 'selfClosing';
    const TYPE_TEXT = 'text';
    const TYPE_UNDEFINED = 'undefined';

    const REGEX_NAME = '\w+[\d\w]*';
    const REGEX_SYMBOLS = '\d\w_,.?!@#$%&*()^=:\+\-\'\/';

    public function __construct($match)
    {
        $matches = array();
        $matchRegex = $this->getMatchRegex();
        preg_match($matchRegex, $match, $matches);
    }

    /**
     * @param string $input
     * @return string[] stack of text-token
     */
    public static function tokenize($input)
    {
        $splitRegex = self::getSplitRegex();

        $tokenList = preg_split($splitRegex, $input, null, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        return $tokenList;
    }

    /**
     * @return string regex
     */
    public static function getSplitRegex()
    {
        $symbolsWithWhitespace = '\s' . self::REGEX_SYMBOLS;
        $assignment = '\s*\=\s*(?:\"[' . $symbolsWithWhitespace . ']*\"|[' . self::REGEX_SYMBOLS . ']*)';
        $regex = '/(\[\/?' . self::REGEX_NAME . '(?:' . $assignment . ')?(?:\s+' . self::REGEX_NAME . $assignment .')*\/?\])/';

        return $regex;
    }

    /**
     * @return string regex
     */
    public static function getMatchRegex()
    {
        $symbolsWithWhitespace = '\s' . self::REGEX_SYMBOLS;
        $assignment = '\s*\=\s*(?:\"[' . $symbolsWithWhitespace . ']*\"|[' . self::REGEX_SYMBOLS . ']*)';
        $regex = '/(\[\/?' . self::REGEX_NAME . '(?:' . $assignment . ')?(?:\s+' . self::REGEX_NAME . $assignment .')*\/?\])/';

        return $regex;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
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
}