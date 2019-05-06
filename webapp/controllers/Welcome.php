<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
        // $this->load->library('Crypt_rsv');
        // $hashed = $this->crypt_rsv->encrypt('u0019003');
        // var_dump($hashed);
        // var_dump($this->crypt_rsv->decrypt($hashed));
        // var_dump($this->crypt_rsv->verify('u0019003', $hashed));

        $client = new GuzzleHttp\Client([
			'verify' => false,
			//'proxy' => 'tcp://100.66.179.28:80',
			'request.options' => [
				//'proxy' => 'tcp://12.34.56.78:3128',
			]
		]);

		$promises = [];
		for ($i = 0; $i <= 10; $i++) {
			$promises['id_'.$i] = $client->requestAsync('GET', 'https://jsonplaceholder.typicode.com/posts/'.$i);
		}

		// get all promises (included promise rejected (error))
		$results = GuzzleHttp\Promise\settle($promises)->wait();

		$out = [];
		foreach ($results as $k => $result) {
			$id = explode('_', $k)[1];

			if ($result['state'] == 'rejected') { // error
				$out[] = [
					'status_code' => $result['reason']->getResponse()->getStatusCode(),
					'sent_status' => 1, // 1: failed, 2: success
					'id' => $id,
					'response' => [
						'headers'  => (array) $result['reason']->getRequest()->getHeaders(),
						'response' => (array) $result['reason']->getResponse(),
						'body'     => [],
						'status'   => 'failed'
					]
				];
			} else {
				$body = json_decode($result['value']->getBody(), true);
				$out[] = [
					'status_code' => $result['value']->getStatusCode(),
					'sent_status' => 2,
					'id' => $id,
					'response' => [
						'headers'  => (array) $result['value']->getHeaders(),
						'response' => (array) $result['value'],
						'body'     => $body,
						'status'   => count($body) > 0 ? 'success' : 'failed'
					]
				];
			}
		}
		echo json_encode($out);die();

		$this->load->view('welcome_message');
	}
}
