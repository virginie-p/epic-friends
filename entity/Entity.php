<?php

namespace App\Entity;

class Entity {
    
    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }
    
    public function hydrate($data)
    {
      foreach ($data as $key => $value)
      {
        $explodedProperty = explode("_", $key);
        
        for ($i = 0 ; count($explodedProperty) > $i; $i++) {
            $explodedProperty[$i] = ucfirst($explodedProperty[$i]);
        }

        $camelCaseProperty = implode('', $explodedProperty);

        $method = 'set'. $camelCaseProperty;
        
        if (method_exists($this, $method))
        {
          $this->$method($value);
        }
      }
    }
}