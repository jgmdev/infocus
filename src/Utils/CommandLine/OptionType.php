<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license MIT
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace Utils\CommandLine;

/**
 * Enumeration used to declare a \Utils\CommandLine\Option type
 */
class OptionType
{

    /**
     * Accepts any type of string.
     */
    const STRING = 1;

    /**
     * Only accept numbers.
     */
    const INTEGER = 2;

    /**
     * Doesn't needs a value, just to be present.
     */
    const FLAG = 3;

}