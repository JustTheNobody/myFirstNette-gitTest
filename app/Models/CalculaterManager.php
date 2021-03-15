<?php

declare(strict_types=1);

namespace App\Models;

use Nette\SmartObject;

class CalculatorManager
{
    use SmartObject;

    const
        ADD = 1,
        SUBTRACT = 2,
        MULTIPLY = 3,
        DIVIDE = 4;

    public function getOperations()
    {
        return array (
            self::ADD => 'Sčítání',
            self::SUBTRACT => 'Odčítání',
            self::MULTIPLY => 'Násobení',
            self::DIVIDE => 'Dělení'
        );
    }

    public function calculate(int $operation, int $x, int $y)
    {
        switch ($operation) {
            case self::ADD:
                return $this->add($x, $y);
            case self::SUBTRACT:
                return $this->substract($x, $y);
            case self::MULTIPLY:
                return $this->multiply($x, $y);
            case self::DIVIDE:
                return $this->divide($x, $y);
            default:
                return null;
        }
    }

    public function add(int $x, int $y)
    {
        return $x + $y;
    }

    public function substract(int $x, int $y)
    {
        return $x - $y;
    }

    public function multiply(int $x, int $y)
    {
        return $x * $y;
    }

    public function divide(int $x, int $y)
    {
        return $x / $y;
    }
}
