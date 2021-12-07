<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

trait SoftDeletableTrait
{
    /**
     * @var bool
     *
     * @ORM\Column(name ="deleted", type="boolean")
     */
    private $deleted = false;

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted)
    {
        $this->deleted = $deleted;
    }
}
