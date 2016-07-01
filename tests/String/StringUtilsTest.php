<?php

namespace Tests\Corviz\Framework\String;


use Corviz\String\StringUtils;

class StringUtilsTest extends \PHPUnit_Framework_TestCase
{

    /*
     * Method: endsWith
     *------------------------------
     */

    public function testEndsWithCaseSensitive()
    {
        $this->assertTrue(
            StringUtils::endsWith('abcde', 'e'),
            'String "abcde" should end with "e" (case sensitive)'
        );
    }

    public function testEndsWithCaseInsensitive()
    {
        $this->assertTrue(
            StringUtils::endsWith('abcde', 'E', false),
            'String "abcde" should end with "E" (case insensitive)'
        );
    }

    public function testEndsWithNeedleGreaterThanHaystackShouldFail()
    {
        $this->assertFalse(
            StringUtils::endsWith("a", "abc"),
            '"a" should not end with "abc"'
        );
    }

    /*
     * Method: startsWith
     *------------------------------
     */

    public function testStartsWithCaseSensitive()
    {
        $this->assertTrue(
            StringUtils::startsWith('abcde', 'a'),
            'String "abcde" should start with "a" (case sensitive)'
        );
    }

    public function testStartsWithCaseInsensitive()
    {
        $this->assertTrue(
            StringUtils::startsWith('abcde', 'A', false),
            'String "abcde" should start with "A" (case insensitive)'
        );
    }

    public function testStartsWithNeedleGreaterThanHaystackShouldFail()
    {
        $this->assertFalse(
            StringUtils::startsWith("a", "abc"),
            '"a" should not start with "abc"'
        );
    }

}
