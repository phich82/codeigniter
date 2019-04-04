<?php

class MyWorker extends \yidas\queue\worker\Controller
{
    // Setting for that a listener could fork up to 10 workers
    public $workerMaxNum = 10;

    // Enable text log writen into specified file for listener and worker
    public $logPath = 'tmp/my-worker.log';

    /**
     * Build Initializer
     *
     * @return void
     */
    protected function init()
    {
        // Optional autoload (Load your own libraries or models)
        $this->load->library('My_jobs', null, 'myJobs');
    }

    /**
     * Build Worker
     *
     * @return bool
     */
    protected function handleWork()
    {
        // Your own method to get a job from your queue in the application
        $job = $this->myJobs->job();

        // return `false` for job not found, which would close the worker itself.
        if (!$job) {
            return false;
        }

        // Your own method to process a job
        $this->myJobs->processJob($job);

        // return `true` for job existing, which would keep handling.
        return true;
    }

    /**
     * Build Listener
     *
     * @return void
     */
    protected function handleListen()
    {
        // Your own method to detect job existence
        // return `true` for job existing, which leads to dispatch worker(s).
        // return `false` for job not found, which would keep detecting new job
        return $this->myJobs->exists();
    }
}
