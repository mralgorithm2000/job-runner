<?php

namespace Mralgorithm\JobRunner\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Mralgorithm\JobRunner\Models\BackgroundJobs;
use ReflectionMethod;

class JobsController extends Controller
{
    public function index()
    {
        $Content = BackgroundJobs::paginate(25);
        return view('job-runner::list', [
            'Content' => $Content
        ]);
    }

    public function create()
    {
        return view('job-runner::add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'class' => 'required',
            'method' => 'required',
            'params' => 'array',
            'delay' => 'required|integer|min:0',
            'priority' => 'required|integer',
            'max_retires' => 'required|integer'
        ]);

        try {
            addToJobRunner(
                $request->class,
                $request->method,
                $request->params,
                $request->delay,
                $request->priority,
                $request->max_retires
            );
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Saved!');
    }

    public function show($id)
    {
        $job = BackgroundJobs::findOrFail($id);
        return view('job-runner::show', compact('job'));
    }

    public function edit($id)
    {
        $Edit = BackgroundJobs::where('id', $id)->first();
        return view('job-runner::add', [
            'Edit' => $Edit
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'class' => 'required',
            'method' => 'required',
            'params' => 'array',
            'delay' => 'required|integer|min:0',
            'priority' => 'required|integer',
            'max_retires' => 'required|integer'
        ]);

        try {
            updateJobRunner(
                $request->class,
                $request->method,
                $request->params,
                $request->delay,
                $request->priority,
                $request->max_retires,
                $id
            );
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Saved!');
    }

    public function destroy($id)
    {
        removeJobRunner($id);

        return redirect()->back()->with('success', 'Saved!');
    }

    public function showLog($jobId)
    {
        // Determine the log file path
        $logFile = storage_path("logs/jobs/job_$jobId.log");
        
        $logContent = [];
        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
        }

        // Pass the content to the view
        return view('job-runner::showLog',[
                'Content' => $logContent
            ]);
    }

    public function changeStatus($jobId,$status){
        try {
            changeJobRunnerStatus($jobId,$status);
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', 'Status has been changed!');
    }
}
