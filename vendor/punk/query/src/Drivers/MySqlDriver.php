<?php

/**
 * MySqlDriver
 * PHP version 7.4
 *
 * @category Drivers
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Drivers;

use Punk\Query\Script\Languages\MySqlLanguage;
use Punk\Query\Script\Languages\Language;

class MySqlDriver extends Driver {

    /**
     * Get Driver Name
     * @return type String
     * */
    function getName(): string {
        return 'mysql';
    }

    public function getLanguage(): Language {
        return new MySqlLanguage();
    }
}
