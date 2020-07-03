<?php

namespace App\MonoFilter\DigitalOcean;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Client
{
    /** @var string $bucketName */
    private $bucketName;
    /** @var S3Client $s3Client */
    private $s3Client;
    /** @var LoggerInterface $logger */
    private $logger;

    public function __construct(LoggerInterface $logger, string $bucketName = '')
    {
        $this->bucketName = $bucketName;
        $this->logger = $logger;
        $this->s3Client = $this->createClient();
    }

    public function createClient()
    {
        if (!$this->s3Client) {
            $s3Client = new S3Client([
                'credentials' => [
                    'key' => $_ENV['SPACES_KEY'],
                    'secret' => $_ENV['SPACES_SECRET'],
                ],
                'region' => $_ENV['SPACES_REGION'],
                'endpoint' => $_ENV['SPACES_ENDPOINT'],
                'version' => $_ENV['SPACES_VERSION'],
            ]);

            $this->s3Client = $s3Client;
        }
        return $this->s3Client;
    }

    public function uploadFileToS3(UploadedFile $file, string $directory = 'mono-filter')
    {
        try {
            return $this->s3Client->putObject([
                'Bucket' => $this->bucketName,
                'SourceFile' => $file->getPathname(),
                'Key' => $directory . '/' . $file->getFilename(),
                'ContentType' => $file->getMimeType(),
                'ACL' => 'public-read'
            ]);
        } catch (S3Exception $exception) {
            $this->logger->error('Spaces Upload Media Error: ' . $exception->getMessage());
            return false;
        }
    }
}