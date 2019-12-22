<?php
declare(strict_types=1);
namespace Fob\MonologLogger;

use BEAR\AppMeta\AbstractAppMeta;
use Fob\MonologLogger\Config\Exception\InvalidConfigException;
use Fob\MonologLogger\Logger\RotatingFileLoggerProvider;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;

class MonologAppLoggerModule extends AbstractModule
{
    /**
     * @var AbstractAppMeta
     */
    private $appMeta;

    private static $APP_LOG_CONF = 'fob.app_log';

    /**
     * AppLoggerModule constructor.
     *
     * @param AbstractAppMeta $meta
     */
    public function __construct(AbstractAppMeta $meta, self $module = null)
    {
        $this->appMeta = $meta;
        parent::__construct($module);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $confPath = $this->appMeta->appDir . '/var/conf/log/app_log/app_log.php';
        $this->guardInvalidConfFile($confPath);

        $config = require $confPath;
        $this->guardInvalidConfig($config);
        $logConf = $config[self::$APP_LOG_CONF]['app'];

        $this->bind(AbstractAppMeta::class)->annotatedWith('fob.app_log.app_meta')->toInstance($this->appMeta);
        $this->bind(Logger::class)->annotatedWith('fob.app_log.base_logger')->toConstructor(Logger::class, [
            'name' => 'fob.app_log.name',
        ]);
        $this->bind()->annotatedWith('fob.app_log.name')->toInstance($logConf['name']);
        $this->bind()->annotatedWith('fob.app_log.max_files')->toInstance($logConf['max_files']);
        $this->bind()->annotatedWith('fob.app_log.level')->toInstance($logConf['level']);

        $this->bind()->annotatedWith('fob.app_log.suffix_format')->toInstance($logConf['app_logger']['suffix_format']);
        $this->bind()->annotatedWith('fob.app_log.filename')->toInstance($logConf['app_logger']['filename']);

        $this->bind(LoggerInterface::class)->annotatedWith('fob.app_log.app_logger')->toProvider(RotatingFileLoggerProvider::class)->in(Scope::SINGLETON);
    }

    private function guardInvalidConfFile(string $path)
    {
        if (! is_file($path)) {
            throw new \LogicException($path);
        }
    }

    private function guardInvalidConfig($config)
    {
        if (! isset($config[self::$APP_LOG_CONF]['app']['name'])) {
            throw new InvalidConfigException();
        }
        if (! isset($config[self::$APP_LOG_CONF]['app']['max_files'])) {
            throw new InvalidConfigException();
        }
        if (! isset($config[self::$APP_LOG_CONF]['app']['app_logger']['suffix_format'])) {
            throw new InvalidConfigException();
        }
        if (! isset($config[self::$APP_LOG_CONF]['app']['app_logger']['filename'])) {
            throw new InvalidConfigException();
        }
    }
}
