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
            $path = $this->_directoryList->getPath('pub') . '/MGS/GeoIp';
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


                $this->saveData($pathFileIPv4,'ipv4');
                $this->saveData($pathFileIPv6,'ipv6');
                $this->saveData($pathFileLocation,'locations');
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

                        list( $long_startIp , $long_endIp) = $this->getIpRang($dataRow[0]);
                        $model->setData('start_ip_v4', $long_startIp)
                            ->setData('end_ip_v4', $long_endIp)
                            ->setData('geoname_id', $dataRow[1])
                            ->setData('postal_code', $dataRow[6])
                            ->setData('latitude', (float)$dataRow[7])
                            ->setData('longitude', (float)$dataRow[8])
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


                        list( $long_startIp , $long_endIp) = $this->ip2Longv6($dataRow[0]);

                        $model = $this->_blockIpv6Factory->create();

                        $model->setData('start_ip_v6', $this->_helperData->getLongIpV6($long_startIp))
                            ->setData('end_ip_v6', $this->_helperData->getLongIpV6($long_endIp))
                            ->setData('geoname_id', $dataRow[1])
                            ->setData('postal_code', $dataRow[6])
                            ->setData('latitude', $dataRow[7])
                            ->setData('longitude', $dataRow[8])
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
                        $model = $this->_locationsFactory->create();

                        $model->setData('geoip_loc_id', $dataRow[0])
                            ->setData('country', $dataRow[4])
                            ->setData('city', $dataRow[10])
                            ->save();
                    }
                }
            break;
        }

    }
    public function getIpRang($cidr) {
        list($ip, $mask) = explode('/', $cidr);

        $maskBinStr =str_repeat("1", $mask ) . str_repeat("0", 32-$mask );
        $inverseMaskBinStr = str_repeat("0", $mask ) . str_repeat("1",  32-$mask );

        $ipLong = ip2long( $ip );
        $ipMaskLong = bindec( $maskBinStr );
        $inverseIpMaskLong = bindec( $inverseMaskBinStr );
        $netWork = $ipLong & $ipMaskLong;

        $start = $netWork+1;

        $end = ($netWork | $inverseIpMaskLong) -1 ;
        return array( $start, $end );
    }

    public function ip2Longv6($data) {

        // Split in address and prefix length
        list($firstaddrstr, $prefixlen) = explode('/', $data);

        // Parse the address into a binary string
        $firstaddrbin = inet_pton($firstaddrstr);

        // Convert the binary string to a string with hexadecimal characters
        # unpack() can be replaced with bin2hex()
        # unpack() is used for symmetry with pack() below
        $elem = unpack('H*', $firstaddrbin);
        $firstaddrhex = reset($elem);

        // Overwriting first address string to make sure notation is optimal
        $firstaddrstr = inet_ntop($firstaddrbin);

        // Calculate the number of 'flexible' bits
        $flexbits = 128 - $prefixlen;

        // Build the hexadecimal string of the last address
        $lastaddrhex = $firstaddrhex;

        // We start at the end of the string (which is always 32 characters long)
        $pos = 31;
        while ($flexbits > 0) {
            // Get the character at this position
            $orig = substr($lastaddrhex, $pos, 1);

            // Convert it to an integer
            $origval = hexdec($orig);

            // OR it with (2^flexbits)-1, with flexbits limited to 4 at a time
            $newval = $origval | (pow(2, min(4, $flexbits)) - 1);

            // Convert it back to a hexadecimal character
            $new = dechex($newval);

            // And put that character back in the string
            $lastaddrhex = substr_replace($lastaddrhex, $new, $pos, 1);

            // We processed one nibble, move to previous position
            $flexbits -= 4;
            $pos -= 1;
        }

        // Convert the hexadecimal string to a binary string
        # Using pack() here
        # Newer PHP version can use hex2bin()
        $lastaddrbin = pack('H*', $lastaddrhex);

        // And create an IPv6 address from the binary string
        $lastaddrstr = inet_ntop($lastaddrbin);
        return array($firstaddrstr,$lastaddrstr);
    }


}
