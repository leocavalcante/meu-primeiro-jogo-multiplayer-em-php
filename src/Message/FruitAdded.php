<?php declare(strict_types=1);

namespace App\Message;

use App\Fruit;

class FruitAdded extends OutMessage
{
    /** @var Fruit */
    private $fruit;

    public function __construct(Fruit $fruit)
    {
        parent::__construct('fruit-add');
        $this->fruit = $fruit;
    }

    function getPayload(): array
    {
        return $this->fruit->jsonSerialize();
    }
}