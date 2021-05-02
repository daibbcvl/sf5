<?php


namespace App\Message;


class CoinIndexNotification
{
    /**
     * @var string
     */
    private $slug;

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * CoinIndexNotification constructor.
     * @param string $slug
     */
    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }


}
