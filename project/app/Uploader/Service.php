<?php

namespace App\Uploader;

use Imagick;

class Service
{
    /** @var string */
    public static $cipher = "aes-128-cbc";

    /** @var string */
    public static $secretS3 = "";

    /** @var string */
    public static $keyS3 = "";

    /** @var UploaderGateway */
    public $gateway;

    public function __construct()
    {
        $this->gateway = new UploaderGateway();
    }

    /**
     * @param string $string
     * @return string
     */
    public function transliterate($string)
    {
        $string = explode('.', $string);
        $extension = $string[count($string) - 1];
        unset($string[count($string) - 1]);
        $string = implode('.', $string);
        $string = preg_replace('/[^a-zA-Z0-9_]/', '_', $string);
        $string = preg_replace('/[-\s]+/', '-', $string);
        return trim($string, '-') . '.' . $extension;
    }

    /**
     * @param $key
     */
    public function getFileFromS3($key): void
    {
        $this->gateway->getFileFromS3($key);
    }

    /**
     * @param string $folder
     * @return array
     */
    public function getUploadOption($folder = ''): array
    {
        $extensions = [
            'jpg',
            'jpeg',
            'png',
            'tiff',
            'tif',
            'gif',
            'pdf',
            'doc',
            'xls',
            'indd',
            'ai',
            'psd',
            'xlsx',
            'docx',
            'zip',
            'rar'
        ];

        if (empty($folder)) {
            $folder = date('Y.m') . '/';
        }
        $path = STORAGE . $folder;

        if (!file_exists($path)) {
            if (!mkdir($path, DIR_MODE, true) && !is_dir($path)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
        }
        $thumbnail = $path . '/thumbnail';
        if (!file_exists($thumbnail)) {
            if (!mkdir($thumbnail, DIR_MODE, true) && !is_dir($thumbnail)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $thumbnail));
            }
        }

        return [
            'upload_dir' => $path,
            'upload_url' => '',
            'mkdir_mode' => DIR_MODE,
            'access_extension' => $extensions,
            'correct_image_extensions' => true,
            'print_response' => false,
            'image_versions' => [
                'thumbnail' => array(
                    'max_width' => 200,
                    'max_height' => 200
                )
            ]
        ];
    }

    /**
     * @param array $data
     * @param string $path
     * @return string
     */
    public static function createDownloading($data = [], $path = 'getFileByKey'): string
    {
        return  'https://img.api-service-provider.com/' . $path . '/' . base64_encode(self::encryptKey($data));


    }

    /**
     * @param $item
     * @param int $start
     * @param string $source
     * @param bool $returnNumber
     * @return int
     * @throws \ImagickException
     */
    public static function howMuchPages($item, $start = 0, $source = '', $returnNumber = false): int
    {
        $path_parts = pathinfo($item->path);
        $pages = 1;

        if ($path_parts['extension'] === 'pdf') {
            $pdfHead = file_get_contents(STORAGE . $item->path, null, null, 0, 3000);
            $num = preg_match_all("/\/Page\W/", $pdfHead, $dummy);
            if (preg_match("~Linearized.*?\/N ([0-9]+)~s", $pdfHead, $matches)) {
                $numbers[1] = $matches[1];
            }
            if (preg_match("/\/N\s+([0-9]+)/", $pdfHead, $matches)) {
                $numbers[2] = $matches[1];
            }
            if (preg_match_all("/\/Count\s+([0-9]+)/s", $pdfHead, $matches)) {
                $numbers[3] = max($matches[1]);
            }

            $pages = $numbers[1] ?? $numbers[2] ?? $numbers[3] ?? 0;

            if (empty($pages)) {
                $document = new Imagick(STORAGE . $item->path);
                $pages = $document->getNumberImages();
            }
        }

        $gateway = new UploaderGateway();
        $gateway->setPages($item->task_id, $pages);

        if ($returnNumber) {
            return $pages;
        }

        //sleep(1);
        $duration = microtime(true) - $start;
        ws()->send([
            'taskId' => $item->task_id,
            'source' => $source,
            'task' => 'pages',
            'pages' => $pages,
            'duration' => $duration
        ], $item->channel);

        return $pages;
    }

    /**
     * @param array $data
     * @param string $domain
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function callWebHook($data = [], $domain = '', $webhook = '')
    {
        $domain = !empty($domain) ? $domain : DOMAIN_URL;
        if ((APP_MODE !== 'dev') && $domain !== 'localhost' && !empty($webhook)) {
            $client = new \GuzzleHttp\Client([
                'verify' => false,
                'future' => true,
            ]);
            $response = $client->request('POST', $domain .$webhook, [
                'form_params' => [
                    $data
                ]
            ]);

            return $response->getBody()->getContents() ? true : false;
        }

        return false;
    }

    /**
     * @param string $data
     * @return array
     */
    public function decryptKey($data): array
    {
        $decrypt = [];
        $decryptData = explode('.==', $data);
        if (!empty($decryptData[1])) {
            $decrypt = openssl_decrypt(
                $decryptData[0],
                self::$cipher,
                CORE_DOT_NET_PRIVATE_KEY,
                0,
                base64_decode($decryptData[1])
            );
            $decrypt = json_decode($decrypt, true) ?? [];
        }

        return $decrypt;
    }

    /**
     * @param array $data
     * @return string
     */
    public static function encryptKey(array $data): string
    {
        $data = json_encode($data);
        if (empty($data)) {
            return '';
        }

        if (in_array(self::$cipher, openssl_get_cipher_methods())) {
            $iv = openssl_random_pseudo_bytes(16);
            if (empty($iv)) {
                try {
                    $iv = random_bytes(16);
                } catch (\Exception $e) {
                    $iv = hex2bin('aed2dfb1f7a18b52945b35189b027465');
                }
            }

            $encryptData = openssl_encrypt($data, self::$cipher, CORE_DOT_NET_PRIVATE_KEY, $options = 0, $iv);

            return $encryptData . '.==' . base64_encode($iv);
        }

        return '';
    }

    public static function getFileByHeader($file)
    {
        if (!empty($file) && file_exists($file)) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT');
            header('Content-Type: ' . mime_content_type($file));
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
    }
}
