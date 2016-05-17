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
    private $value;

    /**
     * @var array
     */
    private $parameters;

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
            preg_match_all($regExp, $this->value, $matches);
            $this->parameters = $matches[2];

        }

        return $this->parameters;

    }

    /**
     * Verify if the current string could generate $strCheck
     * @param string $strCheck
     * @return bool
     */
    public function matches(string $strCheck) : bool 
    {

        $replacement = '@ªuniqueª@'; // Just a unique token

        $regExp = preg_replace(self::PARAMS_SEARCH_REGEXP, $replacement, $this->value);
        $regExp = preg_quote($regExp);
        $regExp = str_replace($replacement, '(.+)', $regExp);
        $regExp = "#^$regExp$#";

        return (bool) preg_match_all($regExp, $strCheck);
        
    }

    /**
     * Generates a final string according to $args
     * @param array $args
     * @return string
     */
    public function parse(array $args = []) : string 
    {

        $strAux = $this->value;
        $params = $this->getParameters();

        foreach($args as $argKey => $argValue){
            if(in_array($argKey, $params)){
                $strAux = str_replace("{{$argKey}}", $argValue, $strAux);
            }
        }

        return $strAux;
        
    }

    /**
     * ParameterizedString constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

}