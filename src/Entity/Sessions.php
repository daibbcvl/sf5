<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sessions
 *
 * @ORM\Table(name="sessions", indexes={@ORM\Index(name="sessions_sess_lifetime_idx", columns={"sess_lifetime"})})
 * @ORM\Entity
 */
class Sessions
{
    /**
     * @var binary
     *
     * @ORM\Column(name="sess_id", type="binary", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $sessId;

    /**
     * @var string
     *
     * @ORM\Column(name="sess_data", type="blob", length=65535, nullable=false)
     */
    private $sessData;

    /**
     * @var int
     *
     * @ORM\Column(name="sess_lifetime", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $sessLifetime;

    /**
     * @var int
     *
     * @ORM\Column(name="sess_time", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $sessTime;


}
