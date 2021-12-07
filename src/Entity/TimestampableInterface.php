<?php

namespace App\Entity;

interface TimestampableInterface
{
    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): ?\DateTimeInterface;

    /**
     * @return $this
     */
    public function setCreatedAt(?\DateTimeInterface $createdAt);

    /**
     * @return \DateTimeInterface
     */
    public function getUpdatedAt(): ?\DateTimeInterface;

    /**
     * @return $this
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt);
}
