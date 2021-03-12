<?php

/**
 * Container
 * PHP version 7.4
 *
 * @category Utils
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Utils;

class Container implements \ArrayAccess {

    /**
     * The container's bindings.
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * Determine if a given exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function exists($key) {
        return isset($this->bindings[$key]);
    }

    /**
     * Set the value at a given bind.
     *
     * @param  string  $key
     * @param  mixed   $data
     * @return void
     */
    public function bind($key, $data) {
        $this->bindings[$key] = $data;
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string  $key
     * @return mixed
     */
    public function make($key) {
        return isset($this->bindings[$key])?$this->bindings[$key]:null;
    }

    /**
     * Determine if a given offset exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key): bool {
        return $this->exists($key);
    }

    /**
     * Get the value at a given offset.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key) {
        return $this->make($key);
    }

    /**
     * Set the value at a given offset.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($key, $value): void {
        $this->bind($key, $value);
    }

    /**
     * Unset the value at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key): void {
        unset($this->bindings[$key]);
    }

    /**
     * Dynamically access container services.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key) {
        return $this[$key];
    }

    /**
     * Dynamically set container services.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value) {
        $this[$key] = $value;
    }
}
