<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Test\Integration\Controller\Adminhtml\Log\Massaction;

use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * Delete mass action behaviour for the admin endpoint.
 *
 * @magentoAppArea adminhtml
 */
class DeleteTest extends AbstractBackendController
{
    protected $resource = 'RJDS_LogViewer::view_logs';

    /**
     * @magentoConfigFixture current_store rjds_logviewer/general/delete_enabled 1
     */
    public function testMassDeleteWithoutSelectionRedirectsToListing(): void
    {
        $this->dispatch('backend/logviewer/log_massaction/delete');
        $this->assertTrue($this->getResponse()->isRedirect());
    }

    /**
     * Mass delete must be a no-op when the feature flag is off (the default).
     */
    public function testMassDeleteIsBlockedWhenFeatureDisabled(): void
    {
        $this->dispatch('backend/logviewer/log_massaction/delete');

        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertSessionMessages(
            $this->equalTo(['Deleting log files is disabled.']),
            MessageInterface::TYPE_ERROR
        );
    }
}
