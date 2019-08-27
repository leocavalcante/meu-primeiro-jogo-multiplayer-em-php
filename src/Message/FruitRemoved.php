<?php declare(strict_types=1);

namespace App\Message;

use App\Fruit;

class FruitRemoved extends OutMessage
{
    /** @var Fruit */
    private $fruit;

    public function __construct(Fruit $fruit)
    {
        parent::__construct('fruit-remove');
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