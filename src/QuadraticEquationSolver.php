<?php

namespace App;

use InvalidArgumentException;

class QuadraticEquationSolver
{

    private const EPSILON = PHP_FLOAT_EPSILON;

    /**
     * @return float[]
     */
    public function solve(float $a, float $b, float $c): array
    {
        if (is_nan($a) || is_nan($b) || is_nan($c)) {
            throw new InvalidArgumentException();
        }

        if (is_infinite($a) || is_infinite($b) || is_infinite($c)) {
            throw new InvalidArgumentException();
        }

        if (abs($a) < self::EPSILON) {
            throw new InvalidArgumentException();
        }

        $discriminant = $b * $b - 4 * $a * $c;

        if ($discriminant < -self::EPSILON) return [];

        if ($discriminant > self::EPSILON) {
            return [
                (-$b + sqrt($discriminant)) / (2 * $a),
                (-$b - sqrt($discriminant)) / (2 * $a),
            ];
        }

        return [(-$b) / (2 * $a)];
    }
}