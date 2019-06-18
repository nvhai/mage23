<?php
namespace MGS\GeoIp\Helper;

use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Backend\App\Config
     */
    protected $backendConfig;

    /**
     * @var Address
     */
    protected $_addressHelper;

    /**
     * @var array
     */
    protected $isArea = [];

    /**
     * Data constructor.
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager
    ){
        $this->_objectManager = $objectManager;
        $this->_storeManager  = $storeManager;
        $this->_isArea  = $storeManager;
        parent::__construct($context);
    }

    /**
     * @param $field
     * @param null $scopeValue
     * @param string $scopeType
     * @return array|mixed
     */
    public function getConfigValue($field, $scopeValue = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        if (!$this->isArea() && is_null($scopeValue)) {
            /** @var \Magento\Backend\App\Config $backendConfig */
            if (!$this->backendConfig) {
                $this->backendConfig = $this->_objectManager->get('Magento\Backend\App\ConfigInterface');
            }

            return $this->backendConfig->getValue($field);
        }

        return $this->scopeConfig->getValue($field, $scopeType, $scopeValue);
    }

    /**
     * @param null $store
     * @return mixed
     */
    public function getDownloadPath($store = null)
    {
        return $this->getConfigValue('mgs_geoip_data/general/download_path', $store);
    }
    /**
     * @return Address
     */
    public function getAddressHelper()
    {
        if (!$this->_addressHelper) {
            $this->_addressHelper = $this->_objectManager->get(Address::class);
        }

        return $this->_addressHelper;
    }

    /**
     * @param string $area
     * @return mixed
     */
    public function isArea($area = Area::AREA_FRONTEND)
    {
        if (!isset($this->isArea[$area])) {
            /** @var \Magento\Framework\App\State $state */
            $state = $this->_objectManager->get('Magento\Framework\App\State');

            try {
                $this->isArea[$area] = ($state->getAreaCode() == $area);
            } catch (\Exception $e) {
                $this->isArea[$area] = false;
            }
        }

        return $this->isArea[$area];
    }
    public function getLongIpV6($ip){
        $ip_n = inet_pton($ip);
        $bin = '';
        for ($bit = strlen($ip_n) - 1; $bit >= 0; $bit--) {
            $bin = sprintf('%08b', ord($ip_n[$bit])) . $bin;
        }

        if (function_exists('gmp_init')) {
            return gmp_strval(gmp_init($bin, 2), 10);
        } elseif (function_exists('bcadd')) {
            $dec = '0';
            for ($i = 0; $i < strlen($bin); $i++) {
                $dec = bcmul($dec, '2', 0);
                $dec = bcadd($dec, $bin[$i], 0);
            }
            return $dec;
        } else {
            trigger_error('GMP or BCMATH extension not installed!', E_USER_ERROR);
        }
    }
}