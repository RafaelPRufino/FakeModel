<?php

/**
 * Script Builder
 * PHP version 7.4
 *
 * @category Script
 * @package  Punk\Query\Script
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Script;

class MySqlBuilder extends Builder {

    public function fullOuterJoin($table, $first, $operator = null, $second = null, $boolean = 'and'): self {
        $left = $this->forQuery();
        $right = $this->forQuery();

        $left->leftJoin($table, $first, $operator, $second, $boolean);
        $right->rightJoin($table, $first, $operator, $second, $boolean);
        
        $left->select([$this->getTable().'.*', $table.'.*' ]);
        $right->select([$this->getTable().'.*', $table.'.*' ]);
        
        $left->union($right, false);
        $this->fromSub($left, $this->getTable());
      
        return $this;
    }
}
