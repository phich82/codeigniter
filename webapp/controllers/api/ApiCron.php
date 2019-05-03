<?php

class ApiCron extends CI_Controller
{
    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // only be called from the command line
        if (!$this->input->is_cli_request()) {
            die('Sorry. This page does not found.');
        }
        $this->_showMessage = true;
        $this->load->library('api/Aws_connect_cloud_service', null, 'connectCloudService');
    }

    /**
     * Run => php /vagrant/index.php apis/apicron/run{/params}
     * Crontab: run script for every 10 seconds
     *  star/1 star star star star root sleep 10; php /vagrant/index.php apis/apicron/run
     *  star/1 star star star star root sleep 20; php /vagrant/index.php apis/apicron/run
     *  star/1 star star star star root sleep 30; php /vagrant/index.php apis/apicron/run
     *  star/1 star star star star root sleep 40; php /vagrant/index.php apis/apicron/run
     *  star/1 star star star star root sleep 50; php /vagrant/index.php apis/apicron/run
     *  star/1 star star star star root sleep 60; php /vagrant/index.php apis/apicron/run
     *
     * @param  string $service
     *
     * @return void
     */
    public function run($service = 'push')
    {
        $this->_execute($service);
    }

    /**
     * Run cronjob by the specified service
     *
     * @param  string $service
     *
     * @return void
     */
    private function _execute($service = 'push')
    {
        switch ($service) {
            case 'push':
                $this->_showStart($service);
                $this->connectCloudService->pushNotificationCron();
                $this->_showEnd($service);
                break;
        }
    }

    /**
     * Show message in cli when executation is starting
     *
     * @param  string $type
     * @param  string $msg
     *
     * @return void
     */
    private function _showStart($type = 'push', $msg = '')
    {
        $this->_show($type, 'Starting', $msg);
    }

    /**
     * Show message in cli when executation finished
     *
     * @param  string $type
     * @param  string $msg
     *
     * @return void
     */
    private function _showEnd($type = 'push', $msg = '')
    {
        $this->_show($type, 'Finshed', $msg);
    }

    /**
     * Show message in cli by service type
     *
     * @param  string $type
     * @param  string $startOrEnd
     * @param  string $msg
     *
     * @return void
     */
    private function _show($type, $startOrEnd, $msg)
    {
        if ($this->_showMessage) {
            echo $this->_message($type, $startOrEnd, $msg);
        }
    }

    /**
     * Get message by service type
     *
     * @param  string $type
     * @param  string $startOrEnd
     * @param  string $msg
     *
     * @return string
     */
    private function _message($type = 'push', $startOrEnd = 'Starting', $msg = '')
    {
        switch ($type) {
            case 'push':
                $message = $msg ?: "-------------------- ".$startOrEnd." push notification --------------------\n";
                break;
            default:
                $message = $msg ?: "-------------------- ".$startOrEnd." --------------------\n";
        }
        return $message;
    }
}
