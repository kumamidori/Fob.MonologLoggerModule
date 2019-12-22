<?php
namespace Fob\MonologLogger;

use BEAR\AppMeta\Meta;
use PHPUnit\Framework\TestCase;

class AppLoggerModuleTest extends TestCase
{
    public function test__construct()
    {
        $dummy = new Meta('Fob\\MonologLogger\\Fake', 'app');
        $SUT = new MonologAppLoggerModule($dummy);
        $this->assertInstanceOf(MonologAppLoggerModule::class, $SUT);
    }
}
