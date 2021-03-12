<?php

/**
 * Script Language
 * PHP version 7.4
 *
 * @category Language
 * @package  Punk\Query\Script
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Script\Languages;

use \Punk\Query\Script\Builder; 
use \Punk\Query\Script\Expression;

class MySqlLanguage extends Language {

    public function __construct() {
        $this->escape_char = "`";
        $this->end_char = '';
    }

    /**
     * Extrai a Paginação
     * @param  string $abstract Expressão
     * @return string
     */
    public function extractPagination(Builder $query,int $limitForPage, int $numberPage) {        
        $currentpage = ($numberPage - 1) * $limitForPage;
        return new Expression(" limit " . $currentpage . " , " . $limitForPage);
    }
}
