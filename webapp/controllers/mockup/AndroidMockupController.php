<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @author Huynh Phat <phat.nguyen@gmail.com>
 * @license http://localhost:8282/api/v1/android [v1]
 */

class AndroidMockupController extends CI_Controller
{
    /**
     * @var array
     */
    protected $accounts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->load->library(['encryption', 'session']);
    }

    /**
     * Android login mockup page
     *
     * @return View
     */
    public function androidLogin()
    {
        $input = $this->input->get();

        if (isset($input['url-fail']) && isset($input['url-ok']) && isset($input['ssoProduct'])) {
            $data = [
                'url-ok'     => $input['url-ok'],
                'url-fail'   => $input['url-fail'],
                'ssoProduct' => $input['ssoProduct'],
            ];

            if ($androidNumber = $this->input->get('android_number')) {
                foreach ($this->getAccounts() as $acc) {
                    if ($acc['android_number'] == $androidNumber) {
                        $ssoData = $this->encrypter->encrypt($acc['ssoData']);
                        return redirect($data['url-ok'] . (strpos($data['url-ok'], '?') === false ? '?' : '&') . http_build_query(['ssoData' => $ssoData]));
                    }
                }
            }
        }

        log_message('Android login', print_r($data, true));
        $this->session->set_userdata('ANDROID_RELATE', $data);

        return $this->load->view('mockup/android-login');
    }

    /**
     * Callback
     *
     * @return void
     */
    public function androidCallback()
    {
        $input = $this->input->get();
        $data = $this->session->userdata('ANDROID_RELATE');
        $ssoData = '';

        foreach ($this->getAccounts() as $acc) {
            if (($acc['android_number'] == $input['android_number']) && ($acc['password'] == $input['password'])) {
                $ssoData = $acc['ssoData'];
                break;
            }
        }

        if (!empty($ssoData)) {
            $ssoData = $this->encrypter->encrypt($ssoData);
            log_message('info', print_r($ssoData, true));
            log_message('info', $data['url-ok']);

            return redirect($data['url-ok'] . (strpos($data['url-ok'], '?') === false ? '?' : '&') . http_build_query(['ssoData' => $ssoData]));
        }
        return redirect($data['url-fail']);
    }

    /**
     * Get all ssoData
     *
     * @return array
     */
    private function getAccounts()
    {
        if ($this->accounts) {
            return $this->accounts;
        }

        $ssoData = preg_split('/\r\n|\r|\n/', file_get_contents(base_path('/files/android_mock.txt')));

        foreach ($ssoData as $sso) {
            $split = explode('|', $sso);
            $this->accounts[] = [
                'android_number' => $split[0],
                'password'       => '1234567890',
                'ssoData'        => $sso,
                'balance'        => 10000000,
            ];
        }

        return $this->accounts;
    }
}
