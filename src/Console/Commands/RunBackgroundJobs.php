<?php

namespace Mralgorithm\JobRunner\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mralgorithm\JobRunner\Models\BackgroundJobs;

class RunBackgroundJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process background jobs from the database queue';

    private $logger;
    /**
     * Execute the console command.
     */
    public function handle()
    {
        while (true) {
            // Fetch the highest priority job that is available
            $job = BackgroundJobs::where('status', 'queued')
                ->where('available_at', '<=', now())
                ->orderByDesc('priority')
                ->orderBy('available_at')
                ->first();

            if (!$job) {
                sleep(5);
                continue;
            }
            $this->logger = $this->getJobLogger($job->id); // Create a logger for this job
            $this->processJob($job);
        }
    }

    /**
     * Process a single job.
     *
     * @param object $job
     */
    protected function processJob($job, $jobStatus = 'processing')
    {
        $this->info("Processing job ID: {$job->id}");
        $this->logger->info("Processing the job");

        // Decode the payload
        $payload = json_decode($job->payload, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Invalid JSON in payload for job ID {$job->id}.");
            $this->logger->info("Invalid JSON in payload for the job");
            $this->markJobAsFailed($job->id, "Invalid payload.");
            return;
        }

        $class = $payload['class'] ?? null;
        $method = $payload['method'] ?? null;

        if (!$class || !$method) {
            $this->error("Payload missing required fields for job ID {$job->id}.");
            $this->logger->info("Payload missing required fields for the job");
            $this->markJobAsFailed($job->id, "Missing class or method.");
            return;
        }

        if (!class_exists($class)) {
            $this->error("Class $class does not exist for job ID {$job->id}.");
            $this->logger->info("Class $class does not exist for the job");
            $this->markJobAsFailed($job->id, "Class not found.");
            return;
        }

        if (!method_exists($class, $method)) {
            $this->error("Method $method does not exist in class $class for job ID {$job->id}.");
            $this->logger->info("Method $method does not exist in class $class for the job");
            $this->markJobAsFailed($job->id, "Method not found.");
            return;
        }

        $params = $payload['params'] ?? [];
        if (!is_array($params)) {
            $this->error("Invalid parameters format for job ID {$job->id}.");
            $this->logger->info("Invalid parameters format for the job.");
            $this->markJobAsFailed($job->id, "Invalid parameters format.");
            return;
        }

        try {

            // Mark the job as completed
            BackgroundJobs::where('id', $job->id)
                ->update(['status' => $jobStatus]);
            // Reflect on the method to check the required number of arguments
            $reflectionMethod = new \ReflectionMethod($class, $method);
            $requiredParameters = $reflectionMethod->getNumberOfRequiredParameters();

            if (count($params) < $requiredParameters) {
                $this->error("Not enough parameters for method $method in class $class for job ID {$job->id}.");
                $this->logger->info("Not enough parameters for method $method in class $class for the job.");
                $this->markJobAsFailed($job->id, "Insufficient parameters for method $method.");
                return;
            }


            $this->info("Calling method {$method} on {$class} with parameters: " . json_encode($params));
            $this->logger->info("Calling method {$method} on {$class} with parameters: " . json_encode($params));

            // Instantiate the class and call the method
            $instance = app($class);
            $start = microtime(true);

            call_user_func_array([$instance, $method], $params);
            
            $executionTime = microtime(true) - $start;
            if ($executionTime > 50) { 
                $this->markJobAsFailed($job->id, "Job execution time exceeded.");
                return;
            }

            // Mark the job as completed
            BackgroundJobs::where('id', $job->id)
                ->update(['status' => 'completed']);

            $this->info("Job ID {$job->id} completed successfully.");
            $this->logger->info("Job completed successfully.");
        } catch (\Exception $e) {
            $this->error("Error processing job ID {$job->id}: " . $e->getMessage());
            $this->logger->info("Error processing the job: " . $e->getMessage());
            $this->markJobAsFailed($job->id, $e->getMessage());
        }
    }

    /**
     * Mark a job as failed.
     *
     * @param int $jobId
     * @param string $reason
     */
    protected function markJobAsFailed($jobId, $reason)
    {
        BackgroundJobs::where('id', $jobId)
            ->update([
                'status' => 'failed',
                'attempts' => DB::raw('attempts + 1'),
            ]);

        //check max retires
        $job = BackgroundJobs::where('id', $jobId)->first();
        $payload = json_decode($job->payload, true);
        if (@$payload->max_retires > 0 && $job->attempts < @$payload->max_retires) {
            $this->processJob($job, 'retrying');
        }
        $this->error("Job ID $jobId marked as failed. Reason: $reason");
        $this->logger->info("Job marked as failed. Reason: $reason");
    }

    /**
     * Get a custom logger for a specific job.
     *
     * This method dynamically creates a logger instance for a given job ID.
     * Each job's logs are written to a separate file in the "storage/logs" directory,
     * named "job_{jobId}.log". This allows for better traceability and debugging
     * of individual job executions.
     *
     * @param int $jobId The ID of the job.
     * @return \Psr\Log\LoggerInterface The logger instance for the job.
     */
    protected function getJobLogger($jobId)
    {
        $logFile = storage_path("logs/jobs/job_$jobId.log");

        return Log::build([
            'driver' => 'single',
            'path' => $logFile,
            'level' => 'info',
        ]);
    }
}
