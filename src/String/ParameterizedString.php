<?php

namespace Corviz\String;


class ParameterizedString
{

    /**
     *
     */
    const PARAMS_SEARCH_REGEXP = "/(\\{([^\\}]+)\\})/";

    /**
     * @var string
     */
    private $str;

    /**
     * @var string
     */
    private $strRegExp;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var string
     */
    private $uniqueReplacement = '@ªuniqueª@';

    /**
     * @param string $val
     * @return ParameterizedString
     */
    public static function make(string $val)
    {
        return new static($val);
    }

    /**
     * Get a list containing all parameters identified
     * @return array
     */
    public function getParameters() : array
    {
        if (is_null($this->parameters)) {

            $matches = [];
            $regExp = self::PARAMS_SEARCH_REGEXP;
            preg_match_all($regExp, $this->str, $matches);
            $this->parameters = $matches[2];

        }

        return $this->parameters;
    }

    /**
     * @param string $rawString
     * @return mixed
     */
    public function getValues(string $rawString)
    {
        $regExp = $this->getStrRegExp();
        $params = $this->getParameters();
        $matches = [];

        preg_match_all($regExp ,$rawString, $matches);
        array_shift($matches);

        $map = function($match){
            return isset($match[0]) ? $match[0] : null;
        };

        return array_combine($params, array_map($map, $matches));
    }

    /**
     * Verify if the current string could generate $strCheck
     * @param string $strCheck
     * @return bool
     */
    public function matches(string $strCheck) : bool 
    {
        $regExp = $this->getStrRegExp();
        return (bool) preg_match_all($regExp, $strCheck);
    }

    /**
     * Generates a final string according to $args
     * @param array $args
     * @return string
     */
    public function parse(array $args = []) : string 
    {
        $strAux = $this->str;
        $params = $this->getParameters();

        foreach($args as $argKey => $argValue){
            if(in_array($argKey, $params)){
                $strAux = str_replace("{{$argKey}}", $argValue, $strAux);
            }
        }

        return $strAux;
    }

    /**
     * @return string
     */
    private function getStrRegExp() : string
    {
        if(is_null($this->strRegExp)){
            $regExp = preg_replace(self::PARAMS_SEARCH_REGEXP, $this->uniqueReplacement, $this->str);
            $regExp = preg_quote($regExp);
            $regExp = str_replace($this->uniqueReplacement, '(.+)', $regExp);
            $this->strRegExp = "#^$regExp$#";
        }

        return $this->strRegExp;
    }

    /**
     * ParameterizedString constructor.
     * @param string $str
     */
    public function __construct(string $str)
    {
        $this->str = $str;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->str;
    }

}