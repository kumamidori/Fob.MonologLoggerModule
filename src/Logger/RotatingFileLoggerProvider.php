<?php
declare(strict_types=1);
namespace Fob\MonologLogger\Logger;

use BEAR\AppMeta\AbstractAppMeta;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Ray\Di\Di\Inject;
use Ray\Di\Di\Named;
use Ray\Di\ProviderInterface;

class RotatingFileLoggerProvider implements ProviderInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var AbstractAppMeta
     */
    private $appMeta;

    /**
     * @var int
     */
    private static $maxFiles;

    /**
     * @var int
     */
    private static $LEVEL;

    /**
     * @var string
     */
    private static $FILENAME;

    /**
     * @var string
     */
    private static $APP_LOG_SUFFIX_DT_FORMAT;

    private static $LINE_FMT = "%datetime% %channel% [%level_name%] %extra.class%::%extra.function%(%extra.line%) %message%  %context%\n";

    /**
     * @Inject
     * @Named(
     *   "logger=fob.app_log.base_logger,appMeta=fob.app_log.app_meta,maxFiles=fob.app_log.max_files,level=fob.app_log.level,suffix=fob.app_log.suffix_format,filename=fob.app_log.filename"
     * )
     */
    public function __construct(
        Logger $logger,
        AbstractAppMeta $appMeta,
        int $maxFiles,
        int $level,
        string $suffix,
        string $filename
    ) {
        $this->logger = $logger;
        $this->appMeta = $appMeta;
        self::$maxFiles = $maxFiles;
        self::$LEVEL = $level;
        self::$APP_LOG_SUFFIX_DT_FORMAT = $suffix;
        self::$FILENAME = $filename;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $path = $this->appMeta->logDir . '/' . self::$FILENAME;

        $rotating = (new RotatingFileHandler($path, self::$maxFiles, self::$LEVEL));
        $rotating->setFilenameFormat('{filename}_{date}', self::$APP_LOG_SUFFIX_DT_FORMAT);
        $rotating->setFormatter(new LineFormatter(self::$LINE_FMT));

        // Injects line/file:class/function where the log message came from
        $ip = new IntrospectionProcessor(
            Logger::DEBUG,
            // ロガーを除く
            [Logger::class]
        );

        return new Logger($this->appMeta->name, [$rotating], [$ip]);
    }
}
