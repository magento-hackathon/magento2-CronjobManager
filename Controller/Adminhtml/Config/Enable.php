<?php

namespace EthanYehuda\CronjobManager\Controller\Adminhtml\Config;

use EthanYehuda\CronjobManager\Model\Manager;
use Magento\Framework\App\CacheInterface;
use EthanYehuda\CronjobManager\Helper\JobConfig;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;

class Enable extends Action
{
    const ADMIN_RESOURCE = "EthanYehuda_CronjobManager::cronjobmanager";

    const SYSTEM_DEFAULT_IDENTIFIER = 'system_default';
    
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var Manager
     */
    private $cronJobManager;

    /**
     * @var JobConfig
     */
    private $helper;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Context $context,
        Manager $cronJobManager,
        JobConfig $helper,
        CacheInterface $cache
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->cronJobManager = $cronJobManager;
        $this->helper = $helper;
        $this->cache = $cache;
    }

    /**
     * Disable cronjob
     *
     * @return Void
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $jobCode = $params['job_code'] ? $params['job_code'] : null;
        if (!$jobCode) {
            $this->getMessageManager()->addErrorMessage("Something went wrong when recieving the request");
            $this->_redirect('*/config/index/');
            return;
        }
        $group = isset($params['group']) && $params['group'] ? $params['group'] : null;
        $frequency = isset($params['frequency']) && $params['frequency'] ? $params['frequency'] : null;
        try {
            $path = $this->helper->constructFrequencyPath($jobCode, $group);
            $this->helper->restoreSystemDefault($path);
            $this->cache->remove(self::SYSTEM_DEFAULT_IDENTIFIER);
        }  catch (\Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
            unset($params['key'], $params['form_key']);
            $this->_redirect('*/config/index/', $params);
            return;
        }

        $this->getMessageManager()->addSuccessMessage("Cron Job Enabled");
        $this->_redirect('*/config/index/');
    }
}
