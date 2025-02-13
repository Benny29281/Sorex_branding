<?php

namespace test;

abstract class AbstractClass
{
    public function __construct(
        protected \test\AbstractDependency $dependency
    ) {
    }
}
