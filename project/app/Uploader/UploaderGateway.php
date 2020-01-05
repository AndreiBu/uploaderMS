<?php

namespace App\Uploader;

use Aws\S3\Exception\S3Exception;

class UploaderGateway
{
    /** @var \Illuminate\Database\DatabaseManager */
    private $db;

    public function __construct()
    {
        $this->db = db();
    }

    /**
     * @param $taskId
     * @param int $pages
     * @return bool
     */
    public function setPages($taskId, $pages = 1): bool
    {
        return $this->db->table('task')
            ->where('id', $taskId)
            ->update(['pages' => $pages]) ? true : false;
    }

    /**
     * @param string $key
     * @return void
     */
    public function getFileFromS3($key): void
    {
        $s3 = new \Aws\S3\S3Client([
            'region' => 'eu-central-1',
            'version' => 'latest',
            'credentials' => [
                'key' => Service::$keyS3,
                'secret' => Service::$secretS3,
            ]
        ]);

        try {
            $result = $s3->getObject([
                'Bucket' => S3_BUCKET,
                'Key' => $key
            ]);

            header("Content-Type: {$result['ContentType']}");
            echo $result['Body'];
            die();
        } catch (S3Exception $e) {
        }
        \App\Route\Service::get404();
    }
}
