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
        foreach ($this->fields as $field => $type) {
            $prop = (is_array($document)) ? $document[$field] : $document->$field;

            if (class_exists($type)) {
                $this->$field = new $type($prop);
            } else {
                $this->$field = $prop;
            }
        }
    }
}
