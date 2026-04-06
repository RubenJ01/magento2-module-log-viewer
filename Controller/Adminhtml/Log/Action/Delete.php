<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Controller\Adminhtml\Log\Action;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Phrase;
use RJDS\LogViewer\Model\Listing\LogFileRowsLoader;
use RJDS\LogViewer\Model\Path\LogFilePathResolver;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'RJDS_LogViewer::view_logs';

    public function __construct(
        Action\Context $context,
        private LogFileRowsLoader $rowsLoader,
        private LogFilePathResolver $filePathResolver
    ) {
        parent::__construct($context);
    }

    /**
     * @throws FileSystemException
     */
    public function execute(): Redirect
    {
        $id = (int) $this->getRequest()->getParam('id');
        if ($id <= 0) {
            return $this->redirectWithError(__('Invalid log file ID.'));
        }

        $row = $this->getRowById($id);
        if ($row === null || !isset($row['file_location'])) {
            return $this->redirectWithError(__('Log file no longer exists.'));
        }

        $absolutePath = $this->filePathResolver->resolveReadablePath((string) $row['file_location']);
        if ($absolutePath === null) {
            return $this->redirectWithError(__('Unable to access the selected log file.'));
        }

        if (!@unlink($absolutePath)) {
            return $this->redirectWithError(__('Failed to delete the selected log file.'));
        }

        $this->messageManager->addSuccessMessage(__('The log file has been deleted.'));

        return $this->resultRedirectFactory->create()->setPath('logviewer/log_page/index');
    }

    /**
     * @return array<string, mixed>|null
     * @throws FileSystemException
     */
    private function getRowById(int $id): ?array
    {
        foreach ($this->rowsLoader->load() as $row) {
            if ((int) ($row['id'] ?? 0) === $id) {
                return $row;
            }
        }

        return null;
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


