<?php declare(strict_types=1);

namespace App\Message\Outgoing;

use App\Fruit;
use App\Message\Outgoing;
use const App\Message\FruitAdded;

class FruitAdded extends Outgoing
{
    /** @var Fruit */
    private $fruit;

    public function __construct(Fruit $fruit)
    {
        parent::__construct(FruitAdded);
        $this->fruit = $fruit;
    }

    function getPayload(): array
    {
        return $this->fruit->jsonSerialize();
    }
}