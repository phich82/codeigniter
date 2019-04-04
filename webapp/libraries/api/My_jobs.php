<?php

class My_jobs
{
    private $_jobs;

    public function __construct()
    {
        $this->_jobs = ['job1', 'job2', 'job3'];
    }

    public function job()
    {
        // create a table cron_jobs
        // get jobs from this table
        $job = $this->_jobs[0] ?? null;
        echo $job."\n";
        return $job;
    }

    public function processJob($job)
    {
        echo $job."\n";
        foreach ($this->_jobs as $k => $item) {
            if ($job == $item) {
                unset($this->_jobs[$k]);
            }
        }
    }

    public function exists()
    {
        return !empty($this->_jobs);
    }
}
