<?php

use App\Busi\Modules\Apply\BackBusi;

$apiVersion = 'v1';

Route::group(['prefix' => $apiVersion], function () {
    Route::group(['middleware' => 'auth:api'], function () {
            //其他接口
            Route::group(['prefix' => 'other'], function () {
                //任务导入导出
                Route::group(['prefix' => 'im_export'], function () {
                    Route::post('/export', UtilsFacade::response(AddExportTaskCommand::class));//创建导出任务
                    Route::post('/import', UtilsFacade::response(AddImportTaskCommand::class));//创建导入任务
                });
            });
        });
    });
});