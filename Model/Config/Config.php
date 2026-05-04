<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED = 'rjds_logviewer/general/enabled';
    private const XML_PATH_DELETE_ENABLED = 'rjds_logviewer/general/delete_enabled';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Retrieves if the module is disabled or not from configuration.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Whether deleting log files is allowed from the admin UI.
     *
     * Disabled by default; admins must explicitly opt in via store configuration.
     */
    public function isDeleteEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DELETE_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
}
