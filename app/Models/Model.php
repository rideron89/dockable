<?php

namespace App\Models;

class Model
{
    protected $fields = [];

    /**
    * Create the class properties on the fly during instance creation.
    *
    * @param object
    */
    public function __construct($document = [])
    {
        foreach ($this->fields as $field) {
            $this->$field = (is_array($document)) ? $document[$field] : $document->$field;
        }
    }
}
