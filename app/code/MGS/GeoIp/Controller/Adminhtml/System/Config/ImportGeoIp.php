<?php


namespace MGS\GeoIp\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\JsonFactory;
use MGS\GeoIp\Helper\Data as HelperData;

/**
 * Class Geoip
 * @package MGS\GeoIp\Controller\Adminhtml\System\Config
 */
class ImportGeoIp extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directoryList;

    /**
     * @var MGS\GeoIp\Helper\Data
     */
    protected $_helperData;

    /**
     * Geoip constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param DirectoryList $directoryList
     * @param HelperData $helperData
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        DirectoryList $directoryList,
        HelperData $helperData
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_directoryList    = $directoryList;
        $this->_helperData       = $helperData;

        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $status = false;
        try {
            $path = $this->_directoryList->getPath('var') . '/MGS/GeoIp';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $folder   = scandir($path, true);
            $pathFileIPv4 = $path . '/' . $folder[0] . '/GeoLite2-City-Blocks-IPv4.csv';
            $pathFileIPv6 = $path . '/' . $folder[0] . '/GeoLite2-City-Blocks-IPv6.csv';
            if (file_exists($pathFileIPv4) && file_exists($pathFileIPv6)) {
                foreach (scandir($path . '/' . $folder[0], true) as $filename) {
                    if ($filename == '..' || $filename == '.') {
                        continue;
                    }
                    @unlink($path . '/' . $folder[0] . '/' . $filename);
                }
                @rmdir($path . '/' . $folder[0]);
            }

            file_put_contents($path . '/GeoLite2-City-CSV.zip', fopen($this->_helperData->getDownloadPath(), 'r'));
            $phar = new \PharData($path . '/GeoLite2-City-CSV.zip');
            $phar->extractTo($path);
            $status  = true;
            $message = __("Download library success!");
        } catch (\Exception $e) {
            $message = __("Can't download file. Please try again! %1", $e->getMessage());
        }

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();

        return $result->setData(['success' => $status, 'message' => $message]);
    }
}
