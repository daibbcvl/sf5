<?php

namespace App\Entity;

interface SoftDeletableInterface
{
    /**
     * @return bool
     */
    public function isDeleted();

    public function setDeleted(bool $deleted);
}
