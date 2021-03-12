<?php

/**
 * Collection
 * PHP version 7.4
 *
 * @category Utils
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Utils\Traits;

trait Collection {

    private $collection_current = null;
    private int $collection_count = 0;
    private $collection_statement = null;

    /**
     * Reset collection
     *
     * Example:
     * echo object->reset();
     *
     * @return mixed collection
     */
    public function resetCollection() {
        $this->collection_current = null;
        $this->collection_statement = null;
        $this->collection_count = 0;
        return $this;
    }

    /**
     * Return the first element collection object
     *
     * Example:
     * echo object->first();
     *
     * @return mixed object
     */
    function first() {
        $data = $this->next();
        $this->resetCollection();
        return $data;
    }

    /**
     * Return the next collection object
     *
     * Example:
     * echo object->next();
     *
     * @return mixed object
     */
    public function next() {
        $data = $this->current_data();
        if (!$data) {
            $this->collection_current = null;
            return $this->newModel(array());
        } else {
            ++$this->collection_count;
            $this->collection_current = $data;
            return $this->newModel($this->collection_current);
        }
    }

    /**
     * Rewind collection
     *
     * @return null
     */
    public function rewind() {
        $this->collection_current = 0;
    }

    /**
     * Return current value
     *
     * @return mixed value
     */
    public function current() {
        if ($this->collection_current == null) {
            return $this->next();
        }
        return $this->newModel($this->collection_current);
    }

    /**
     * Return current key
     *
     * @return mixed key
     */
    public function key() {
        return $this->collection_count;
    }

    /**
     * Return if its valid
     *
     * @return boolean valid
     */
    public function valid() {
        return $this->current() != null && $this->collection_current != null;
    }

    /**
     * Get the next result from collection
     *
     * @return mixed result
     */
    protected function current_data() {
        if (!$this->collection_statement) {
            $this->collection_statement = $this->statement();
        }
        return $this->collection_statement->fetch(\PDO::FETCH_ASSOC);
    }
}
