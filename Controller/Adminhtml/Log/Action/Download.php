<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Controller\Adminhtml\Log\Action;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use RJDS\LogViewer\Model\Download\LogFileDownloadResponseBuilder;
use RJDS\LogViewer\Model\Listing\LogFileRowsLoader;
use RJDS\LogViewer\Model\Path\LogFilePathResolver;

class Download extends Action
{
    public const ADMIN_RESOURCE = 'RJDS_LogViewer::view_logs';

    public function __construct(
        Action\Context $context,
        private readonly LogFileRowsLoader $rowsLoader,
        private readonly LogFilePathResolver $filePathResolver,
        private readonly LogFileDownloadResponseBuilder $responseBuilder
    ) {
        parent::__construct($context);
    }

    /**
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function execute(): Redirect|ResponseInterface
    {
        $id = (int) $this->getRequest()->getParam('id');
        if ($id <= 0) {
            return $this->redirectWithError(__('Invalid log file ID.'));
        }

        $row = $this->getRowById($id);
        if ($row === null || !isset($row['file_location'], $row['file_name'])) {
            return $this->redirectWithError(__('Log file no longer exists.'));
        }

        $absolutePath = $this->filePathResolver->resolveReadablePath((string) $row['file_location']);
        if ($absolutePath === null) {
            return $this->redirectWithError(__('Unable to read the selected log file.'));
        }

        return $this->responseBuilder->build((string) $row['file_name'], $absolutePath);
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

    private function redirectWithError(Phrase|string $errorMessage): Redirect
    {
        $this->messageManager->addErrorMessage($errorMessage);

        return $this->resultRedirectFactory->create()->setPath('logviewer/log_page/index');
    }
}

