<?php

use Illuminate\Support\Facades\Route;
use Mralgorithm\JobRunner\Controllers\JobsController;

Route::prefix('mralgorithm')->middleware('web')->group(function(){
    Route::redirect('/', 'mralgorithm/jobs', 302);

    Route::resource('jobs', JobsController::class);
    Route::get('jobs/{jobId}/log', [JobsController::class,'showLog']);
    Route::get('jobs/{jobId}/status/{status}', [JobsController::class,'changeStatus'])
    ->where('status','paused|queued');

});