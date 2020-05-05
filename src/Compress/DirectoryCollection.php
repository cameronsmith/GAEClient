<?php namespace UKCASmith\GAEClient\Compress;

use RecursiveDirectoryIterator;
use RecursiveIterator;

class DirectoryCollection implements RecursiveIterator
{
    /**
     * @var array
     */
    protected $arr_data = [];

    /**
     * @var RecursiveIterator[]
     */
    protected $arr_children = [];

    /**
     * @var bool
     */
    protected $bol_valid = true;

    /**
     * @var int
     */
    protected $int_offset = 0;

    /**
     * Add item.
     *
     * @param string $str_index
     * @param array $arr_value
     * @return $this
     */
    public function add($str_index, $arr_value) {
        $this->arr_data[$str_index] = $arr_value;
        return $this;
    }

    /**
     * Add children.
     *
     * @param $str_index
     * @param RecursiveDirectoryIterator $obj_children
     * @return $this
     */
    public function addChildren($str_index, \RecursiveDirectoryIterator $obj_children) {
        $this->arr_children[$str_index] = $obj_children;
        return $this;
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return current($this->arr_data);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $mix_result = next($this->arr_data);
        if ($mix_result === false) {
            $this->bol_valid = false;
        }
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key(current($this->arr_data));
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->bol_valid;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->bol_valid = true;
        reset($this->arr_data);
    }

    /**
     * Returns if an iterator can be created for the current entry.
     * @link http://php.net/manual/en/recursiveiterator.haschildren.php
     * @return bool true if the current entry can be iterated over, otherwise returns false.
     * @since 5.1.0
     */
    public function hasChildren()
    {
        $str_key = key($this->arr_data);
        return (isset($this->arr_children[$str_key]));
    }

    /**
     * Returns an iterator for the current entry.
     * @link http://php.net/manual/en/recursiveiterator.getchildren.php
     * @return RecursiveIterator An iterator for the current entry.
     * @since 5.1.0
     */
    public function getChildren()
    {
        $str_key = key($this->arr_data);
        return $this->arr_children[$str_key];
    }
}