<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Test\Integration\Controller\Adminhtml\Log\Action;

use Magento\Framework\Message\MessageInterface;
use Magento\TestFramework\TestCase\AbstractBackendController;

/**
 * Delete action behaviour for the per-row admin endpoint.
 *
 * @magentoAppArea adminhtml
 */
class DeleteTest extends AbstractBackendController
{
    protected $resource = 'RJDS_LogViewer::view_logs';

    protected $uri = 'backend/logviewer/log_action/delete/id/0';

    /**
     * @magentoConfigFixture current_store rjds_logviewer/general/delete_enabled 1
     */
    public function testSingleDeleteWithInvalidIdRedirectsToListing(): void
    {
        $this->dispatch($this->uri);
        $this->assertTrue($this->getResponse()->isRedirect());
    }

    /**
     * The delete feature must be off by default and the controller must refuse
     * to act regardless of the request payload.
     */
    public function testSingleDeleteIsBlockedWhenFeatureDisabled(): void
    {
        $this->dispatch($this->uri);

        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertSessionMessages(
            $this->equalTo(['Deleting log files is disabled.']),
            MessageInterface::TYPE_ERROR
        );
    }
}
