<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Controller\Adminhtml\Log\Massaction;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Phrase;
use RJDS\LogViewer\Model\Selection\SelectedLogRowsResolver;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'RJDS_LogViewer::view_logs';

    public function __construct(
        Action\Context $context,
        private SelectedLogRowsResolver $selectedRowsResolver
    ) {
        parent::__construct($context);
    }

    /**
     * @throws FileSystemException
     */
    public function execute(): Redirect
    {
        $requestedIds = $this->selectedRowsResolver->resolveRequestedIds($this->getRequest());
        if ($requestedIds === []) {
            return $this->redirectWithError(__('Select at least one log file to delete.'));
        }

        $selectedRows = $this->selectedRowsResolver->resolveRowsByIds($requestedIds);
        if ($selectedRows === []) {
            return $this->redirectWithError(__('No valid log files were selected.'));
        }

        $deletedCount = 0;
        foreach ($selectedRows as $row) {
            $absolutePath = (string) ($row['absolute_path'] ?? '');
            if ($absolutePath === '' || !@unlink($absolutePath)) {
                continue;
            }

            ++$deletedCount;
        }

        if ($deletedCount === 0) {
            return $this->redirectWithError(__('Failed to delete the selected log files.'));
        }

        if ($deletedCount < count($selectedRows)) {
            $this->messageManager->addWarningMessage(
                __('Some selected log files could not be deleted.')
            );
        }

        $this->messageManager->addSuccessMessage(__('%1 log file(s) have been deleted.', $deletedCount));

        return $this->resultRedirectFactory->create()->setPath('logviewer/log_page/index');
    }

    /**
     * @param Phrase|string $errorMessage
     */
    private function redirectWithError($errorMessage): Redirect
    {
        $this->messageManager->addErrorMessage($errorMessage);

        return $this->resultRedirectFactory->create()->setPath('logviewer/log_page/index');
    }
}

