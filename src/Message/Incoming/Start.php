<?php declare(strict_types=1);

namespace App\Message\Incoming;

class Start
{
    /** @var int */
    private $interval;

    public function __construct(int $interval)
    {
        $this->interval = $interval;
    }

    public function getInterval(): int
    {
        return $this->interval;
    }
}