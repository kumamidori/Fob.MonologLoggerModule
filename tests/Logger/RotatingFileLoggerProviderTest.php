<?php
declare(strict_types=1);
namespace Fob\MonologLogger\Logger;

use BEAR\AppMeta\Meta;
use Fob\MonologLogger\MonologAppLoggerModule;
use PHPUnit\Framework\TestCase;
use Ray\Di\Injector;

class RotatingFileLoggerProviderTest extends TestCase
{
    public function testGet()
    {
        $dummy = new Meta('Fob\\MonologLogger\\Fake', 'app');
        $injector = new Injector(new MonologAppLoggerModule($dummy));
        $this->assertInstanceOf( RotatingFileLoggerProvider::class, $injector->getInstance( RotatingFileLoggerProvider::class));
    }
}
