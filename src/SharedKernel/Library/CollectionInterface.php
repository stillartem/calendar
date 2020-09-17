<?php

namespace Calendar\SharedKernel\Library;


interface CollectionInterface
{
    /**
     * @param mixed $item
     *
     * @return Collection
     */
    public function addItem($item);


    /**
     * @return array
     */
    public function getItems();


    /**
     * @return int
     */
    public function getCount();
}
