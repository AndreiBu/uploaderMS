<?php

return [
    ['GET', '/', 'App\Template\Controller@index'],
    ['GET', '/api/docs', 'App\Template\Controller@swager'],

    ['GET', '/api/auth', 'App\API\Controller@createJWT'],
    ['GET', '/api/checkJWT', 'App\API\Controller@checkJWT'],
    ['GET', '/api/cdn/thumbnail/by-url', 'App\API\Controller@thumbnailByUrl'],
    ['GET', '/by-url', 'App\API\Controller@thumbnailByUrl'],
    ['POST', '/api/file', 'App\Uploader\Controller@addFile'],
    ['GET', '/api/file', 'App\Uploader\Controller@getGileInfo'],
    ['POST', '/api/thumbnail', 'App\Uploader\Controller@createThumbnail'],
    ['GET', '/api/thumbnail', 'App\Uploader\Controller@thumbnail'],

    ['GET', '/health-check', 'App\HealthCheck\Controller@healthCheck'],

    ['GET', '/upload', 'App\Template\Controller@getUploadTemplate'],
    ['GET', '/test', 'App\HealthCheck\Controller@test'],
    ['POST', '/upload', 'App\Uploader\Controller@addFile'],
    ['GET', '/storage/{folder}/{file}', 'App\Uploader\Controller@getFile'],
    ['GET', '/storage/{folder}/thumbnail/{file}', 'App\Uploader\Controller@getThumbnail'],
    ['OPTION', '/upload', 'App\Uploader\Controller@getOptionAccept'],
    ['GET', '/cron/tasks', 'App\Cron\Controller@showTasks'],
    ['GET', '/cron/tasks/finish/all', 'App\Cron\Controller@finishAllNewTasks'],
    ['GET', '/getFileByKey/{key}', 'App\Uploader\Controller@getFileByKey'],
    ['GET', '/thumbnail/{key}', 'App\Uploader\Controller@thumbnailByKey'],
];

