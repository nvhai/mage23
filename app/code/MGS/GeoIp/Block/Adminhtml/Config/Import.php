<?php


namespace MGS\GeoIp\Block\Adminhtml\Config;

use MGS\GeoIp\Block\Adminhtml\Config\ConfigTemplate;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Import extends Field
{
    /** @var
     * ImportModel $import
     */
    protected $import;

    /**
     * DownloadNImport constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function _getElementHtml(AbstractElement $element)
    {

        $urls = [];

            $startUrl = $this->getUrl('mgs_geoip/geoip/start', array(
                'type' => 'ip_v4',
                'action' => 'import'
            ))
            ;

            $processUrl = $this->getUrl('mgs_geoip/geoip/process', array(
                'type' => 'ip_v4',
                'action' => 'import'
            ))
            ;

            $commitUrl = $this->getUrl('mgs_geoip/geoip/commit', array(
                'type' => 'ip_v4',
                'action' => 'import'
            ))
            ;

            $urls[] = ['start' => $startUrl, 'process' => $processUrl, 'commit' => $commitUrl];

        $block = $this->getLayout()
            ->createBlock('MGS\GeoIp\Block\Adminhtml\Config\ConfigTemplate')
            ->setTemplate('MGS_GeoIp::import.phtml')
            ->setConfig(json_encode($urls))
        ;

//        $this->setImportData($block);

        return $block->toHtml();
    }

    /**
     * @param TemplateBlock $block
     */
    public function setImportData($block)
    {
        $importFilesAvailable = false;

        $fileBlockPath = $block->geoipHelper->getCsvFilePath('block');
        $fileBlockV6Path = $block->geoipHelper->getCsvFilePath('block_v6');
        $fileLocationPath = $block->geoipHelper->getCsvFilePath('location');

        $blockFileExist = false;
        $blockV6FileExist = false;
        $locationFileExist = false;

        if ($block->geoipHelper->isFileExist($fileBlockPath)) {
            $blockFileExist = true;
        }
        if ($block->geoipHelper->isFileExist($fileBlockV6Path)) {
            $blockV6FileExist = true;
        }
        if ($block->geoipHelper->isFileExist($fileLocationPath)) {
            $locationFileExist = true;
        }

        if ($blockFileExist && $locationFileExist && $blockV6FileExist) {
            $importFilesAvailable = true;
        }

        $importDate = '';

        if ($block->geoipHelper->isDone() && $this->import->importTableHasData()) {
            $width = 100;
            $importedClass = 'end_imported';
            if ($block->_scopeConfig->getValue('amgeoip/import/date')) {
                $importDate = __('Last Imported: ') . $block->_scopeConfig->getValue('amgeoip/import/date');
            }
        } else {
            $width = 0;
            $importedClass = 'end_not_imported';
        }
        $block
            ->setWidth($width)
            ->setImportFilesAvailable($importFilesAvailable)
            ->setBlockFileExist($blockFileExist)
            ->setBlockV6FileExist($blockV6FileExist)
            ->setLocationFileExist($locationFileExist)
            ->setImportedClass($importedClass)
            ->setImportDate($importDate);
    }
}
