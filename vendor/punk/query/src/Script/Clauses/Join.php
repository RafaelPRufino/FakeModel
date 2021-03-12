<?php

/**
 * Join Clause
 * PHP version 7.4
 *
 * @category Clauses
 * @package  Punk\Query\Script
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Script\Clauses;

use \Punk\Query\Script\Builder as BuilderInterface;
use \Punk\Query\Utils\Arr;
use \Punk\Query\Utils\Str;
use \Punk\Query\Utils\Container;
use \Punk\Query\Script\Builder;

class Join extends Builder {

    private BuilderInterface $parentBuilder;

    public function getParent(): BuilderInterface {
        return $this->parentBuilder;
    }

    /**
     * variable stringType
     * type String
     * */
    Private $stringType;

    /**
     * Get and Set variable stringType
     * @return and @param type String
     * */
    public function getType() {
        return $this->stringType;
    }

    Public function setType($newStringType) {
        $this->stringType = $newStringType;
    }

    public function __construct(BuilderInterface $parentBuilder, string $type, $table) {
        $this->parentBuilder = $parentBuilder;
        $this->where = array();
        $this->setType($type);
        $this->from($table);
        parent::__construct($parentBuilder->getConnection(), $parentBuilder->getLang());
    }

    public function on(Array $options = []) {
        return $this->whereColumns($options);
    }

    public function orOn(Array $options = []) {
        return $this->orWhereColumns($options);
    }
}
