<?php

namespace Tests;

use App\QuadraticEquationSolver;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class QuadraticEquationSolverTest extends TestCase
{

    private QuadraticEquationSolver $solver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->solver = new QuadraticEquationSolver();
    }

    public function testQuadraticEquationHasNoRoots()
    {
        $this->assertEmpty(
            $this->solver->solve(1, 0, 1),
        );
    }

    public function testQuadraticEquationHasTwoRoots()
    {
        $roots = $this->solver->solve(1, 0, -1);

        $this->assertCount(2, $roots);

        $this->assertEquals(0, $roots[0] * $roots[0] - 1);
        $this->assertEquals(0, $roots[1] * $roots[1] - 1);
    }

    public function testQuadraticEquationHasOneRoot()
    {
        $roots = $this->solver->solve(1, 2, 1);

        $this->assertCount(1, $roots);

        $x = $roots[0];

        $this->assertEquals(0, $x * $x + $x * 2 + 1);
    }

    public function testThrowExceptionWhenAIsZero()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->solver->solve(0, 2, 1);
    }

    public function testThrowExceptionWhenAIsNan()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->solver->solve(NAN, 2, 1);
    }

    public function testThrowExceptionWhenBIsNan()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->solver->solve(1, NAN, 1);
    }

    public function testThrowExceptionWhenCIsNan()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->solver->solve(1, 2, NAN);
    }

    public function testThrowExceptionWhenAIsInf()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->solver->solve(INF, 2, 1);
    }

    public function testThrowExceptionWhenAIsNegativeInf()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->solver->solve(-INF, 2, 1);
    }

    public function testThrowExceptionWhenBIsInf()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->solver->solve(1, INF, 1);
    }

    public function testThrowExceptionWhenBIsNegativeInf()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->solver->solve(1, -INF, 1);
    }

    public function testThrowExceptionWhenCIsInf()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->solver->solve(1, 2, INF);
    }

    public function testThrowExceptionWhenCIsNegativeInf()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->solver->solve(1, 2, -INF);
    }
}