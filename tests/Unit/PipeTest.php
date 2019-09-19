<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Pipeline\Pipeline;

class PipeTest extends TestCase
{
    /**
     * pipe
     *
     * @param mix $poster
     * @param \Closure ...$closure
     * @return mix
     */
    protected function pipe($poster, \Closure ...$closure)
    {
        return (new Pipeline)->send($poster)->through($closure)->then(function ($poster) {
            return $poster;
        });
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testPipe()
    {
        $increment = function ($poster, \Closure $next) {
            $poster += 1;
            return $next($poster);
        };

        $this->assertEquals(6, $this->pipe(5, $increment));
        $this->assertEquals(8, $this->pipe(5, $increment, $increment, $increment));
    }
}
