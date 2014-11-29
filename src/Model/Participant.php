<?php

namespace Venyii\WhosePoints\Model;

class Participant
{
    private $id;
    private $name;
    private $alias;

    /**
     * @param int $id
     * @param string $name
     * @param string|null $alias
     */
    public function __construct($id, $name, $alias = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->alias = $alias;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }
}
