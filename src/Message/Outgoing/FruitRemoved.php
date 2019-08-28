<?php declare(strict_types=1);

namespace App\Message\Outgoing;

use App\Fruit;
use App\Message\Outgoing;

class FruitRemoved extends Outgoing
{
    /** @var Fruit */
    private $fruit;

    public function __construct(Fruit $fruit)
    {
        parent::__construct(FruitRemoved);
        $this->fruit = $fruit;
    }

    function getPayload(): array
    {
        return [
            'fruitId' => $this->fruit->getId(),
            'score' => 0,
        ];
    }
}