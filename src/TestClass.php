<?php

namespace Mralgorithm\JobRunner;

class TestClass
{
    public function test()
    {
        echo "Test method has been launched!";
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function testWithParam(string $param1)
    {
        echo "Test method has been launched!, Param1 : $param1";
    }

      /**
     * Bootstrap services.
     *
     * @return void
     */
    public function NotAllowedFunction()
    {
        echo "Do not RUN this function! IT IS NOT ALLOWED!";
    }
}
