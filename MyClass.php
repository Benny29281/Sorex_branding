<?php

namespace test;

class MyClass extends AbstractClass
{
    public function __construct(
        \test\MyDependency $dependency
    ) {
        parent::__construct($dependency);
    }

    public function run()
    {
        //callMe marked as nonexistent
        //Call to unknown method: test\AbstractDependency::callMe()PHP(PHP0418)
        $this->dependency->callMe();
    }
}
