<?php

use Mralgorithm\JobRunner\Services\JobHandler;

if (!function_exists('addToJobRunner')) {
    function addToJobRunner($className,$methodName,$params = [],$delay = 0,$priority = 0,$max_retries = 0)
    {
        $jobHandler = new JobHandler();
        $jobHandler->setAttributes($className,
        $methodName,
        $params,
        $delay,
        $priority,
        $max_retries);
        return $jobHandler->add();
    }
}
if (!function_exists('updateJobRunner')) {
    function updateJobRunner($className,$methodName,$params = [],$delay = 0,$priority = 0,$max_retries = 0,$job_id = 0)
    {
        $jobHandler = new JobHandler();
        $jobHandler->setAttributes($className,
        $methodName,
        $params,
        $delay,
        $priority,
        $max_retries);

        $jobHandler->update($job_id);
    }
}
if (!function_exists('removeJobRunner')) {
    function removeJobRunner($job_id)
    {
        $jobHandler = new JobHandler();

        $jobHandler->destroy($job_id);
    }
}

if (!function_exists('changeJobRunnerStatus')) {
    function changeJobRunnerStatus($job_id,$status)
    {
        $jobHandler = new JobHandler();

        $jobHandler->changeStatus($job_id,$status);
    }
}