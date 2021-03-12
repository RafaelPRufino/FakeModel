<?php

/**
 * SQLiteDriver
 * PHP version 7.4
 *
 * @category Drivers
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Drivers;

class SQLiteDriver extends Driver {

    /**
     * Get Driver Name
     * @return type String
     * */
    function getName(): string {
        return 'sqlite';
    }

    public function getLanguage(): \Punk\Query\Script\Languages\LanguageInterface {
        
    }

}
