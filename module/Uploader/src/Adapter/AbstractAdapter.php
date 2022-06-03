<?php

declare(strict_types=1);

namespace Uploader\Adapter;

use Laminas\Db\TableGateway\TableGateway;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Filter\BaseName;
use Laminas\Filter\File\RenameUpload;
use Laminas\Log\Logger;
use RuntimeException;
use Throwable;
use Uploader\Adapter\AdapterInterface;

use function array_key_exists;
use function dirname;

abstract class AbstractAdapter implements AdapterInterface, EventManagerAwareInterface
{
    /** @var EventManager $eventManager */
    protected $eventManager;
    /** @var Logger $logger */
    protected $logger;
    /** @var RenameUpload $handler */
    protected $handler;
    /** @var BaseName $filter */
    protected $filter;
    /**
     * Base path to app set in app module.config.php
     *
     * @var string $baseDir
     */
    protected $baseDir;
    /**
     * Upload path to the destination directory
     *
     * @var string $targetPath
     */
    protected $targetPath = '';
    /**
     * Identifer for the db table where this record will be stored
     *
     * @var TableGateway $table
     */
    /**
     * Target file name to be used if randomize is true
     *
     * @var string $targetFileName
     */
    protected $targetFileName = 'image';
    /**
     * Identifer for the module name this image is being uploaded for
     *
     * @var string $module
     */
    protected $module;
    /** @var string $moduleDirPath */
    protected $moduleDirPath = '/public';
    /**
     * this is to set the type of upload we are handling for a module
     * basically maps to the different types of images/files a module
     * supports
     * ie /public/modules/$module/$type
     *
     * @var string $type
     */
    protected $type;
    /**
     * Randomize file names? default true
     *
     * @var bool $randomize
     */
    protected $randomize = true;
    /**
     * The uploaded file after being processed
     * Will contain only the basename and extension of the newly
     * uploaded and filtered file name
     *
     * @var string $uploadedFile
     */
    protected $uploadedFile;
    /**
     * Do we want to keep the original file extension? default true
     *
     * @var bool $useUploadedExt
     */
    protected $useUploadedExt = true;
    public function setEventManager(EventManagerInterface $eventManager): void
    {
        $eventManager->setIdentifiers([self::class, static::class]);
        $this->eventManager = $eventManager;
    }

    public function getEventManager(): EventManager
    {
        if (! $this->eventManager) {
            $this->setEventManager(new EventManager());
        }
        return $this->eventManager;
    }

    public function getConfig(array $config): mixed
    {
        if (! isset($config['upload_manager'])) {
            $this->config['upload_manager'] = [];
        }
        $this->config = $config['upload_manager'];
        return $this;
    }

    protected function loadContext(): void
    {
        try {
            $this->baseDir = dirname(__DIR__, 4);
            $this->initFilters();
            $this->handler->setRandomize($this->randomize);
            if (! isset($this->module)) {
            // by this point the uploader should have already set the module and type
                throw new RuntimeException('You must pass $post[module] from the form!!!');
            }

            /**
             * incoming from the form via post data
             * $this->data['module'] gives us the modules public directory name
             * $this->data['type] must match the $this->config['type'] index for us to match the path for the supported types
             */
            $this->publicPath = '/modules';
            if (isset($this->type) && array_key_exists('upload_path', $this->config[ $this->module ]['type'][$this->type])) {
                $this->publicPath .= '/' . $this->module . $this->config[$this->module]['type'][$this->type]['upload_path'];
            // $this->targetPath .= $this->baseDir . $this->publicPath;
            } elseif (
                ! empty($this->module)
                && ! array_key_exists('upload_path', $this->config[ $this->module ]['type'][$this->type])
                && isset($this->config[$this->module]['image_dir'])
            ) {
                $this->publicPath .= '/' . $this->module . '/' . $this->config[$this->module]['image_dir'];
            //$this->targetPath .= $this->baseDir . $this->publicPath;
            } elseif (empty($this->module) || $this->module === 'app' || $this->module === 'app' || $this->module === 'page') {
            // in this condition we are using /public/images so we just overwrite the path
                $this->publicPath = '/images';
            }
            $this->targetPath .= $this->baseDir . '/public' . $this->publicPath;
            $this->handler->setTarget($this->targetPath . '/' . $this->getTargetFileName());
            $this->handler->setUseUploadExtension($this->useUploadedExt);
        } catch (Throwable $th) {
            $this->logger->log(1, $th->getMessage());
        }
    }

    public function getPublicPath(): string
    {
        return $this->publicPath . '/' . $this->baseName;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
        $this->loadContext();
    }

    protected function initFilters(): void
    {
        $this->filter  = new BaseName();
        $this->handler = new RenameUpload();
    }

    /** @param string $targetFileName */
    public function setTargetFileName($targetFileName): void
    {
        $this->targetFileName = (string) $targetFileName;
    }

    public function getTargetFileName(): string
    {
        return $this->targetFileName;
    }

    /** @param string $module */
    public function setModule($module): void
    {
        $this->module = $module;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /** @param string $type */
    public function setType($type)
    {
        $this->type = $type;
    }
}
