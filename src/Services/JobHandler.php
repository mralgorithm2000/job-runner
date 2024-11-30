<?php

namespace Mralgorithm\JobRunner\Services;

use Carbon\Carbon;
use Mralgorithm\JobRunner\Models\BackgroundJobs;

class JobHandler
{
    protected $className;
    protected $methodName;
    protected $params;
    protected $delay;
    protected $priority;
    protected $maxRetries;

    public function __construct() {}

    public function setAttributes($className, $methodName, $params, $delay, $priority, $maxRetries)
    {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->params = $params;
        $this->delay = $delay;
        $this->priority = $priority;
        $this->maxRetries = $maxRetries;
    }

    public function validate()
    {
        $approvedJobs = config('jobrunner');
        if($approvedJobs == null){
            throw new \InvalidArgumentException("The configuration file for approved jobs is missing or empty. Please publish the configuration file.");
        }
        $className = $this->className;
        $methodName = $this->methodName;
        $params = $this->params;

        // Check if the class exists
        if (!class_exists($className)) {
            throw new \InvalidArgumentException("Class $className does not exsist!");
        }

        // Check if the method exists
        if (!method_exists($className, $methodName)) {
            throw new \InvalidArgumentException("Method $methodName does not exsist!");
        }

        // Check if the class is in the approved list
        if (!array_key_exists($className, $approvedJobs)) {
            throw new \InvalidArgumentException("Unauthorized class.");
            return;
        }

        // Check if the method is approved for this class
        if (!in_array($methodName, $approvedJobs[$className])) {
            throw new \InvalidArgumentException("Unauthorized method.");
            return;
        }

        $reflectionMethod = new \ReflectionMethod($className, $methodName);
        $requiredParameters = $reflectionMethod->getNumberOfRequiredParameters();
        $params = $params ?? [];
        if (count($params) < $requiredParameters) {
            throw new \InvalidArgumentException("Insufficient parameters for method $methodName.");
            return;
        }
    }

    public function add()
    {
        $this->validate();
        $payload = $this->createPayload();

        $BackgroundJobs = BackgroundJobs::create([
            'priority' => $this->priority,
            'payload' => json_encode($payload),
            'attempts' => $this->maxRetries,
            'available_at' => Carbon::now()->addSeconds($this->delay)
        ]);

        return $BackgroundJobs->id;
    }

    public function update($id)
    {
        if ($id == 0 || $id == '') {
            return 0;
        }
        $this->validate();
        $payload = $this->createPayload();

        BackgroundJobs::where('id', $id)->update([
            'priority' => $this->priority,
            'payload' => json_encode($payload),
            'attempts' => $this->maxRetries,
            'available_at' => Carbon::now()->addSeconds($this->delay),
            'status' => 'queued'
        ]);
    }

    public function destroy($job_id)
    {
        BackgroundJobs::where('id', $job_id)->delete();
    }

    public function changeStatus($job_id,$status){
        // check if status value is valid
        if(!in_array($status,['paused','queued'])){
            throw new \InvalidArgumentException("Status is invalid");
            return;
        }

        // checkif it is possible tchnage the status
        $job = BackgroundJobs::where('id',$job_id)->first();
        if($status == 'paused' && !in_array($job->status, ['queued', 'retrying'])){
            throw new \InvalidArgumentException("Invalid job status: When the job is 'paused', its status must be either 'queued' or 'retrying'. Current status: '{$job->status}'.");
            return;
        }

        if ($status == 'queued' && !in_array($job->status, ['paused'])) {
            throw new \InvalidArgumentException("Invalid job status: When the status is 'queued', the job must be 'paused'. Current status: '{$job->status}'.");
            return;
        }

        BackgroundJobs::where('id', $job_id)->update([
            'status' => $status
        ]);

        return true;
    }

    private function createPayload()
    {
        $Content = [];

        $Content['class'] = $this->className;
        $Content['method'] = $this->methodName;
        $Content['max_retires'] = $this->maxRetries;

        if (isset($this->params)) {
            foreach ($this->params as $p) {
                $Content['params'][] = $p;
            }
        }


        return $Content;
    }
}
