<?php

namespace Calendar\SharedKernel\Library;


abstract class Collection implements CollectionInterface, \ArrayAccess, \Iterator, \Countable
{
    /** @var array */
    protected $items;

    /** @var int */
    protected $position;


    /**
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = array_values($items);
        $this->position = 0;
    }


    /**
     * {@inheritdoc}
     */
    public function addItem($item)
    {
        $this->items[] = $item;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }


    /**
     * {@inheritdoc}
     */
    public function getCount()
    {
        if ($this->items === null) {
            return 0;
        }

        return \count($this->items);
    }


    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->getCount();
    }


    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->items[] = $value;
        } elseif ($this->offsetExists($offset)) {
            $this->items[$offset] = $value;
        } else {
            throw new \OutOfBoundsException(sprintf('Value [%s] is not a valid key', $offset));
        }
    }


    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->items);
    }


    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }


    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset] ?? null;
    }


    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;
    }


    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->items[$this->position];
    }


    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->position;
    }


    /**
     * {@inheritdoc}
     */
    public function next()
    {
        ++$this->position;
    }


    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this->items[$this->position]);
    }


    /**
     * @param int  $offset
     * @param null $length
     */
    public function slice($offset, $length = null)
    {
        $this->rewind();
        $this->items = \array_slice($this->items, $offset, $length);
    }


    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->items);
    }
}
