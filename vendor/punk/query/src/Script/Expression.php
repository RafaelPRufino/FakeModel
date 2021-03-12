<?php

/**
 * Expression
 * PHP version 7.4
 *
 * @category Script
 * @package  Punk\Query\Script
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Script;

class Expression {

    /**
     * variable objectValue
     * type Object
     * */
    private $objectValue;

    /**
     * Get and Set variable objectValue
     * @return and @param type Object
     * */
    public function getValue() {
        return $this->objectValue;
    }

    public function __construct($value) {
        $this->objectValue = $value;
    }

    /**
     * Get the value of the expression.
     *
     * @return string
     */
    public function __toString() {
        return (string) $this->getValue();
    }
}
