<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Controller\Adminhtml\Log\Massaction;

use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Phrase;
use Psr\Log\LoggerInterface;
use RJDS\LogViewer\Model\Download\LogFileDownloadResponseBuilder;
use RJDS\LogViewer\Model\Selection\SelectedLogRowsResolver;
use RuntimeException;
use ZipArchive;

class Download extends Action
{
	public const ADMIN_RESOURCE = 'RJDS_LogViewer::view_logs';

	public function __construct(
		Action\Context $context,
		private readonly SelectedLogRowsResolver $selectedRowsResolver,
		private readonly LogFileDownloadResponseBuilder $responseBuilder,
		private readonly FileFactory $fileFactory,
		private readonly DirectoryList $directoryList,
		private readonly File $fileIo,
		private readonly LoggerInterface $logger
	) {
		parent::__construct($context);
	}

	/**
	 * @throws FileSystemException
	 * @throws Exception
	 */
	public function execute(): Redirect|ResponseInterface
	{
		$requestedIds = $this->selectedRowsResolver->resolveRequestedIds($this->getRequest());
		if ($requestedIds === []) {
			return $this->redirectWithError(__('Select at least one log file to download.'));
		}

		$selectedRows = $this->selectedRowsResolver->resolveRowsByIds($requestedIds);
		if ($selectedRows === []) {
			return $this->redirectWithError(__('No valid log files were selected.'));
		}

		if (count($selectedRows) === 1) {
			$singleRow = reset($selectedRows);
			if (!is_array($singleRow) || !isset($singleRow['file_name'], $singleRow['absolute_path'])) {
				return $this->redirectWithError(__('No valid log files were selected.'));
			}

			return $this->responseBuilder->build((string) $singleRow['file_name'], (string) $singleRow['absolute_path']);
		}

		try {
			$zipRelativePath = $this->buildZipFromRows($selectedRows);
		} catch (\Throwable $exception) {
			$this->logger->error('Failed to prepare log ZIP download.', ['exception' => $exception]);

			return $this->redirectWithError(__('Failed to prepare the ZIP download.'));
		}

		return $this->fileFactory->create(
			sprintf('log-files-%s.zip', gmdate('Ymd-His')),
			[
				'type' => 'filename',
				'value' => $zipRelativePath,
				'rm' => true,
			],
			DirectoryList::VAR_DIR,
			'application/zip'
		);
	}

	/**
	 * @param array<int, array<string, mixed>> $rows
	 * @throws LocalizedException|Exception
	 */
	private function buildZipFromRows(array $rows): string
	{
		$tmpDirectory = $this->directoryList->getPath(DirectoryList::TMP);
		$this->fileIo->checkAndCreateFolder($tmpDirectory);

		$zipFileName = sprintf('logviewer-%s-%s.zip', gmdate('Ymd-His'), bin2hex(random_bytes(4)));
		$zipAbsolutePath = $tmpDirectory . DIRECTORY_SEPARATOR . $zipFileName;

		$zipArchive = new ZipArchive();
		$openResult = $zipArchive->open($zipAbsolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
		if ($openResult !== true) {
			throw new RuntimeException('Failed to create ZIP archive.');
		}

		foreach ($rows as $row) {
			$archivePath = ltrim((string) ($row['file_location'] ?? ''), DIRECTORY_SEPARATOR);
			$archivePath = preg_replace('#^var/log/#', '', $archivePath) ?: (string) ($row['file_name'] ?? 'log-file.log');
			$zipArchive->addFile((string) $row['absolute_path'], $archivePath);
		}

		$zipArchive->close();

		return 'tmp/' . $zipFileName;
	}

	private function redirectWithError(Phrase|string $errorMessage): Redirect
	{
		$this->messageManager->addErrorMessage($errorMessage);

		return $this->resultRedirectFactory->create()->setPath('logviewer/log_page/index');
	}
}

