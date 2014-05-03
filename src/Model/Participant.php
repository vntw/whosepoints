<?php

namespace Venyii\WhosePoints\Model;

class Participant
{
    private $id;
    private $name;
    private $alias;

    public function __construct($id, $name, $alias = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->alias = $alias;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAlias()
    {
        return $this->alias;
    }
}
