<?php

namespace DMTQ\CurrencyFlags\Setup\Patch\Data;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use Psr\Log\LoggerInterface;

/**
 * Class Initialize
 */
class Initialize implements DataPatchInterface
{

    /**
     * @var Filesystem
     */
    protected Filesystem $fileSystem;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var ModuleDirReader
     */
    private ModuleDirReader $moduleDirReader;

    /**
     * Initialize constructor.
     *
     * @param Filesystem $filesystem
     * @param LoggerInterface $logger
     * @param ModuleDirReader $moduleDirReader
     */
    public function __construct(
        Filesystem      $filesystem,
        LoggerInterface $logger,
        ModuleDirReader $moduleDirReader,
    )
    {
        $this->fileSystem = $filesystem;
        $this->logger = $logger;
        $this->moduleDirReader = $moduleDirReader;
    }


    /**
     * Apply
     *
     * @return void
     */
    public function apply(): void
    {
        $this->logger->info('Currency Flags Initialize - Start');
        $this->copyImages();
    }

    /**
     * Copy images
     */
    private function copyImages(): void
    {
        try {
            $mediaDirectory = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
            $mediaDirectory->create('dmtq' . DIRECTORY_SEPARATOR .'currency-flags' . DIRECTORY_SEPARATOR . '4x3');
            $targetPath = $mediaDirectory->getAbsolutePath('dmtq' . DIRECTORY_SEPARATOR .'currency-flags' . DIRECTORY_SEPARATOR . '4x3');
            $modulePath = $this->moduleDirReader->getModuleDir('', 'DMTQ_CurrencyFlags');
            $originPath = $modulePath . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'flags' . DIRECTORY_SEPARATOR . '4x3';
            $this->copyDirectory($originPath, $targetPath);
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @param $source
     * @param $destination
     * @return void
     * @throws FileSystemException
     */
    private function copyDirectory($source, $destination)
    {
        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                if (!is_dir($source . DIRECTORY_SEPARATOR . $file)) {
                    $mediaDirectory = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA);
                    $mediaDirectory->getDriver()->copy($source . DIRECTORY_SEPARATOR . $file, $destination . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        closedir($dir);
    }


    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '1.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
