<?php


namespace MGS\GeoIp\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\File\Csv;
use MGS\GeoIp\Helper\Data as HelperData;
use MGS\GeoIp\Model\BlockIpv4Factory;
use MGS\GeoIp\Model\BlockIpv6Factory;
use MGS\GeoIp\Model\LocationsFactory;

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
     * @var Csv
     */
    protected $csvProcessor;

    /**
     * @var MGS\GeoIp\Helper\Data
     */
    protected $_helperData;
    /**
     * @var BlockIpv4Factory
     */
    protected $_blockIpv4Factory;

    /**
     * @var BlockIpv6Factory
     */
    protected $_blockIpv6Factory;

    /**
     * @var LocationsFactory
     */
    protected $_locationsFactory;

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
        Csv $csvProcessor,
        HelperData $helperData,
        BlockIpv4Factory $blockIpv4Factory,
        BlockIpv6Factory $blockIpv6Factory,
        LocationsFactory $locationsFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_directoryList    = $directoryList;
        $this->_helperData       = $helperData;
        $this->csvProcessor = $csvProcessor;
        $this->_blockIpv4Factory = $blockIpv4Factory;
        $this->_blockIpv6Factory = $blockIpv6Factory;
        $this->_locationsFactory = $locationsFactory;

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
            $pathFileLocation = $path . '/' . $folder[0] . '/GeoLite2-City-Locations-en.csv';
            if (file_exists($pathFileIPv4) && file_exists($pathFileIPv6) && file_exists($pathFileLocation)) {
                $importProductRawData = $this->csvProcessor->getData($pathFileIPv6);
//                var_dump($importProductRawData);exit;


                /*$this->saveData($pathFileIPv4,'ipv4');
                $this->saveData($pathFileIPv6,'ipv6');
                $this->saveData($pathFileLocation,'locations');*/
                $status  = true;
                $message = __("Download library success!");
            }

        } catch (\Exception $e) {
            $message = __("Can't download file. Please try again! %1", $e->getMessage());
        }

        /** @var \Magento\Framework\Controller\Result\Json $result */
        $result = $this->resultJsonFactory->create();

        return $result->setData(['success' => $status, 'message' => $message]);
    }
    public function saveData($filePath,$type){
        $importProductRawData = $this->csvProcessor->getData($filePath);
        switch ($type){
            case 'ipv4':
                $model = $this->_blockIpv4Factory->create();

                $connection = $model->getResource()->getConnection();
                $tableName = $model->getResource()->getMainTable();
                $connection->beginTransaction();
                $connection->delete($tableName);
                $connection->commit();

                foreach ($importProductRawData as $rowIndex => $dataRow)
                {

                    if($rowIndex > 0)
                    {
                        $model = $this->_blockIpv4Factory->create();

                        $model->setData('start_ip_v4', $dataRow[1])
                            ->setData('end_ip_v4', $dataRow[2])
                            ->setData('geoname_id', $dataRow[0])
                            ->setData('postal_code', $dataRow[0])
                            ->setData('latitude', $dataRow[0])
                            ->setData('longitude', $dataRow[0])
                            ->save();
                    }
                }

            break;
            case 'ipv6':
                $model = $this->_blockIpv6Factory->create();

                $connection = $model->getResource()->getConnection();
                $tableName = $model->getResource()->getMainTable();
                $connection->beginTransaction();
                $connection->delete($tableName);
                $connection->commit();

                foreach ($importProductRawData as $rowIndex => $dataRow)
                {

                    if($rowIndex > 0)
                    {
                        $model = $this->_blockIpv6Factory->create();

                        $model->setData('start_ip_v6', $dataRow[1])
                            ->setData('end_ip_v6', $dataRow[2])
                            ->setData('geoname_id', $dataRow[0])
                            ->setData('postal_code', $dataRow[0])
                            ->setData('latitude', $dataRow[0])
                            ->setData('longitude', $dataRow[0])
                            ->save();
                    }
                }
            break;
            default:
                $model = $this->_locationsFactory->create();

                $connection = $model->getResource()->getConnection();
                $tableName = $model->getResource()->getMainTable();
                $connection->beginTransaction();
                $connection->delete($tableName);
                $connection->commit();

                foreach ($importProductRawData as $rowIndex => $dataRow)
                {

                    if($rowIndex > 0)
                    {
                        $model = $this->_city->create();

                        $model->setData('country_code', $dataRow[1])
                            ->setData('sub_city', $dataRow[2])
                            ->setData('city', $dataRow[0])
                            ->save();
                    }
                }
            break;
        }

    }
}
