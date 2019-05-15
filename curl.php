<?php
function native_curl($new_name, $new_email)
{
    $username = 'admin';
    $password = '1234';
     
    // Alternative JSON version
    // $url = 'http://twitter.com/statuses/update.json';
    // Set up and execute the curl process
    $curl_handle = curl_init();
    curl_setopt($curl_handle, CURLOPT_URL, 'http://localhost/restserver/index.php/example_api/user/id/1/format/json');
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_handle, CURLOPT_POST, 1);
    curl_setopt($curl_handle, CURLOPT_POSTFIELDS, array(
        'name' => $new_name,
        'email' => $new_email
    ));
     
    // Optional, delete this line if your API is open
    curl_setopt($curl_handle, CURLOPT_USERPWD, $username . ':' . $password);
     
    $buffer = curl_exec($curl_handle);
    curl_close($curl_handle);
     
    $result = json_decode($buffer);
 
    if(isset($result->status) && $result->status == 'success')
    {
        echo 'User has been updated.';
    }
     
    else
    {
        echo 'Something has gone wrong';
    }
}

function ci_curl($new_name, $new_email)
{
    $username = 'admin';
    $password = '1234';
     
    $this->load->library('curl');
     
    $this->curl->create('http://localhost/restserver/index.php/example_api/user/id/1/format/json');
     
    // Optional, delete this line if your API is open
    $this->curl->http_login($username, $password);
 
    $this->curl->post(array(
        'name' => $new_name,
        'email' => $new_email
    ));
     
    $result = json_decode($this->curl->execute());
 
    if(isset($result->status) && $result->status == 'success')
    {
        echo 'User has been updated.';
    }
     
    else
    {
        echo 'Something has gone wrong';
    }
}

function curl_get()
{
	$url = 'https://jsonplaceholder.typicode.com/posts/1';
	//$proxy = '127.0.0.1:8888';
	//$proxyauth = 'user:password';

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$url);
	//curl_setopt($ch, CURLOPT_PROXY, $proxy);
	//curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyauth);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);

	$result = curl_exec($ch);

	curl_close($ch);

	return $result;
}

**
* This function is in use for calculate the authorization signature
*/
function build_header_from_aws_signature_v4_2(
	$host, $uri, $requestUrl, 
	$accessKey, $secretKey, $region, $service, 
	$httpRequestMethod, $data, $debug = TRUE)
{
	$terminationString	= 'aws4_request'; // must be lowercase for region, service name, and special termination string
	$algorithm 		= 'AWS4-HMAC-SHA256';
	$phpAlgorithm 		= 'sha256';
	$canonicalURI		= $uri;
	$canonicalQueryString	= '';
	$signedHeaders		= 'content-type;host;x-amz-date';
	$currentDateTime = new DateTime('UTC');
	$reqDate = $currentDateTime->format('Ymd');
	$reqDateTime = $currentDateTime->format('Ymd\THis\Z');

	// Task 3.1 - Create signing key
	$kSecret  = $secretKey;
	$kDate    = hash_hmac($phpAlgorithm, $reqDate, 'AWS4' . $kSecret, true);
	$kRegion  = hash_hmac($phpAlgorithm, $region, $kDate, true);
	$kService = hash_hmac($phpAlgorithm, $service, $kRegion, true);
	$kSigning = hash_hmac($phpAlgorithm, $terminationString, $kService, true);

	// Create canonical headers
	$canonicalHeaders = array();
	$canonicalHeaders[] = 'content-type:application/x-www-form-urlencoded';
	$canonicalHeaders[] = 'host:' . $host;
	$canonicalHeaders[] = 'x-amz-date:' . $reqDateTime;
	$canonicalHeadersStr = implode("\n", $canonicalHeaders);

	// Create request payload
	$requestHasedPayload = hash($phpAlgorithm, $data);

	// Task 1 - Create canonical request
	$canonicalRequest = array();
	$canonicalRequest[] = $httpRequestMethod;
	$canonicalRequest[] = $canonicalURI;
	$canonicalRequest[] = $canonicalQueryString;
	$canonicalRequest[] = $canonicalHeadersStr . "\n";
	$canonicalRequest[] = $signedHeaders;
	$canonicalRequest[] = $requestHasedPayload;
	$requestCanonicalRequest = implode("\n", $canonicalRequest);
	$requestHasedCanonicalRequest = hash($phpAlgorithm, utf8_encode($requestCanonicalRequest));

	if ($debug) {
		echo "<h5>Canonical to string</h5>";
		echo "<pre>";
		echo $requestCanonicalRequest;
		echo "</pre>";
	}

	// Create scope
	$credentialScope = array();
	$credentialScope[] = $reqDate;
	$credentialScope[] = $region;
	$credentialScope[] = $service;
	$credentialScope[] = $terminationString;
	$credentialScopeStr = implode('/', $credentialScope);

	// Task 2 - Create string to signing
	$stringToSign = array();
	$stringToSign[] = $algorithm;
	$stringToSign[] = $reqDateTime;
	$stringToSign[] = $credentialScopeStr;
	$stringToSign[] = $requestHasedCanonicalRequest;
	$stringToSignStr = implode("\n", $stringToSign);

	if ($debug) {
		echo "<h5>String to Sign</h5>";
		echo "<pre>";
		echo $stringToSignStr;
		echo "</pre>";
	}

	// Task 3.2 - Create signature
	$signature = hash_hmac($phpAlgorithm, $stringToSignStr, $kSigning);

	// Task 4 - Create authorization header
	$authorizationHeader = array();
	$authorizationHeader[] = 'Credential=' . $accessKey . '/' . $credentialScopeStr;
	$authorizationHeader[] = 'SignedHeaders=' . $signedHeaders;
	$authorizationHeader[] = 'Signature=' . ($signature);
	$authorizationHeaderStr = $algorithm . ' ' . implode(', ', $authorizationHeader);

	// Request headers
	$headers = array();
	$headers[] = 'authorization:'.$authorizationHeaderStr;
	$headers[] = 'content-length:'.strlen($data);
	$headers[] = 'content-type: application/x-www-form-urlencoded';
	$headers[] = 'host: ' . $host;
	$headers[] = 'x-amz-date: ' . $reqDateTime;

	return $headers;
}

function build_header_from_aws_signature_v4(
	$host, $uri, $requestUrl, 
	$accessKey, $secretKey, $region, $service, 
	$httpRequestMethod, $data, $debug = TRUE
)
{
	// notes: must be lowercase for region, service name, and special termination string
	$terminationString	  = 'aws4_request'; 
	$algorithm 		      = 'AWS4-HMAC-SHA256';
	$phpAlgorithm 		  = 'sha256';
	$canonicalURI		  = $uri;
	$canonicalQueryString = '';
	$signedHeaders		  = 'content-type;host;x-amz-date';
	$currentDateTime      = new DateTime('UTC');
	$reqDate              = $currentDateTime->format('Ymd');
	$reqDateTime          = $currentDateTime->format('Ymd\THis\Z');


	/*
		Task 1 - Create canonical request for Signature Version 4

		Structure:
			CanonicalRequest =
				HTTPRequestMethod + '\n' +
				CanonicalURI + '\n' +
				CanonicalQueryString + '\n' +
				CanonicalHeaders + '\n' +
				SignedHeaders + '\n' +
				HexEncode(Hash(RequestPayload))
			Ex:
				GET
				/
				Action=ListUsers&Version=2010-05-08
				content-type:application/x-www-form-urlencoded; charset=utf-8
				host:iam.amazonaws.com
				x-amz-date:20150830T123600Z

				content-type;host;x-amz-date
				e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855
	*/
	// 1.1 Create request payload
	$requestHasedPayload = hash($phpAlgorithm, $data);

	// 1.2 Create canonical headers
	$canonicalHeaders = [
		'content-type:application/x-www-form-urlencoded; charset=utf-8',
		'host:' . $host,
		'x-amz-date:' . $reqDateTime,
	];
	$canonicalHeadersStr = implode("\n", $canonicalHeaders);

	// 1.3 Create canonical request
	$canonicalRequest = [
		$httpRequestMethod,
		$canonicalURI,
		$canonicalQueryString,
		$canonicalHeadersStr . "\n",
		$signedHeaders,
		$requestHasedPayload,
	];
	$requestCanonicalRequest = implode("\n", $canonicalRequest);
	$requestHasedCanonicalRequest = hash($phpAlgorithm, utf8_encode($requestCanonicalRequest));

	if ($debug) {
		echo "<h5>Canonical to string</h5>";
		echo "<pre>";
		echo $requestCanonicalRequest;
		echo "</pre>";
	}
	////////// ====== End Task 1 ===== //////////

	/*
		Task 2 - Create string to sign for Signature Version 4

		Structure:
			StringToSign =
			    Algorithm + \n +
			    RequestDateTime + \n +
			    CredentialScope + \n +
			    HashedCanonicalRequest
			Ex:
				AWS4-HMAC-SHA256
				20150830T123600Z
				20150830/us-east-1/iam/aws4_request
				f536975d06c0309214f805bb90ccff089219ecd68b2577efef23edd43b7e1a59
	*/
	// 1.1 Create scope
	$credentialScope = [
		$reqDate,
		$region,
		$service,
		$terminationString,
	];
	$credentialScopeStr = implode('/', $credentialScope);

	// 2.2 Create string to sign
	$stringToSign = [
		$algorithm,
		$reqDateTime,
		$credentialScopeStr,
		$requestHasedCanonicalRequest,
	];
	$stringToSignStr = implode("\n", $stringToSign);

	if ($debug) {
		echo "<h5>String to Sign</h5>";
		echo "<pre>";
		echo $stringToSignStr;
		echo "</pre>";
	}
	////////// ====== End Task 2 ===== //////////

	/*
		Task 3 - Calculate the Signature for Signature Version 4

		Structure:
			HMAC(HMAC(HMAC(HMAC("AWS4" + kSecret, Date), Region), Service), TerminationString)
		Ex:
			HMAC(HMAC(HMAC(HMAC("AWS4" + kSecret,"20150830"),"us-east-1"),"iam"),"aws4_request")
			=> c4afb1cc5771d871763a393e44b703571b55cc28424d1a5e86da6ed3c154a4b9
	*/
	// Task 3.1 - Create signing key
	$kSecret  = $secretKey;
	$kDate    = hash_hmac($phpAlgorithm, $reqDate, 'AWS4' . $kSecret, true);
	$kRegion  = hash_hmac($phpAlgorithm, $region, $kDate, true);
	$kService = hash_hmac($phpAlgorithm, $service, $kRegion, true);
	$kSigning = hash_hmac($phpAlgorithm, $terminationString, $kService, true);

	// Task 3.2 - Create signature
	$signature = hash_hmac($phpAlgorithm, $stringToSignStr, $kSigning);

	/*
		Task 4 - Add the Signature to the HTTP Request

		We can add the signature to a request in one of two ways:
			> An HTTP header named Authorization (Authorization: {Signature})
			> The query string

		Structure:
			algorithm Credential=access key ID/credential scope, SignedHeaders=SignedHeaders, Signature=signature
		Ex:
			Authorization: AWS4-HMAC-SHA256 Credential=AKIDEXAMPLE/20150830/us-east-1/iam/aws4_request, SignedHeaders=content-type;host;x-amz-date, Signature=5d672d79c15b13162d9279b0855cfba6789a8edb4c82c400e06b5924a6f2b5d7
	*/
	// 4.1 - Create authorization header
	$authorizationHeader = [
		'Credential=' . $accessKey . '/' . $credentialScopeStr,
		'SignedHeaders=' . $signedHeaders,
		'Signature=' . ($signature)
	];
	$authorizationHeaderStr = $algorithm . ' ' . implode(', ', $authorizationHeader);

	// Request headers
	$headers = [
		'authorization:'.$authorizationHeaderStr, // Adding Signing Information to the Authorization Header
		'content-length:'.strlen($data),
		'content-type: application/x-www-form-urlencoded',
		'host: ' . $host,
		'x-amz-date: ' . $reqDateTime,
	];

	return $headers;
}

/**
* This function is in use
* for send request with authorization header
*/
function exec_api_aws_secure_sign($requestUrl, $httpRequestMethod, $headers, $data, $debug = TRUE)
{
	$ch = curl_init();

	curl_setopt_array($ch, [
		CURLOPT_URL 		   => $requestUrl,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_TIMEOUT 	   => 30,
		CURLOPT_POST 		   => true,
		CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST  => $httpRequestMethod,
		CURLOPT_POSTFIELDS 	   => $data,
		CURLOPT_VERBOSE 	   => 0,
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_HEADER 		   => false,
		CURLINFO_HEADER_OUT	   => true,
		CURLOPT_HTTPHEADER     => $headers,
	]);

	$response = curl_exec($ch);

	$err = curl_error($ch);

	$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if ($debug) {
		$headers = curl_getinfo($ch, CURLINFO_HEADER_OUT);
		echo "<h5>Request</h5>";
		echo "<pre>";
		echo $headers;
		echo "</pre>";
	}

	curl_close($ch);

	if ($err) {
		if ($debug) {
			echo "<h5>Error:" . $responseCode . "</h5>";
			echo "<pre>".$err."</pre>";
		}
	} else {
		if ($debug) {
			echo "<h5>Response:" . $responseCode . "</h5>";
			echo "<pre>".$response."</pre>";
		}
	}
	
	return [
		"responseCode" => $responseCode,
		"response"     => $response,
		"error" 	   => $err
	];
}

function test_api_secure_sign_aws()
{
	// Configuration values
	$host		= '<host name>';
	$accessKey	= '<access key>';
	$secretKey 	= '<secret key>';
	//$region 	= '<region>';
	//$service 	= '<service>';

	//$requestUrl	       = '<full url>';
	//$uri               = '<method path>';
	//$httpRequestMethod = '<http verb>';
	//$data              = json_encode([]);


	$region     = 'ap-southeast-1';
	$service    = 'execute-api';
	$requestUrl = 'https://host-domain/object/identifier';
	$uri        = '/object/identifier';
	$httpRequestMethod = 'POST';
	$data = json_encode([
	    "username"      => "sample-demo",
	    "firstName"     => "Sample",
	    "lastName"      => "Demo",
	    "date_of_birth" => "1999-05-08"
	]);

	$headers = build_header_from_aws_signature_v4($host, $uri, $requestUrl, $accessKey, $secretKey, $region, $service, $httpRequestMethod, $data, TRUE);
	$result  = exec_api_aws_secure_sign($requestUrl, $httpRequestMethod, $headers, $data, TRUE);

	print_r($result);
}

function credentials_aws()
{

}

function post_secure_sign($path, $data, $accessKey = null, $serectKey = null)
{
	if (empty($accessKey) || empty($serectKey)) {
		$credentials = credentials_aws();
		$accessKey = $credentials['AWSAccessKeyId'] ?: '';
		$serectKey = $credentials['AWSSecretAccessKey'] ?: '';
	}
	

	// Configuration values
	$httpRequestMethod = 'POST';
	$host		= '<host name>';
	$region     = 'ap-southeast-1';
	$service    = 'execute-api';
	$requestUrl = $path ?: 'https://host-domain/object/identifier';
	$uri        = '/object/identifier';
	
	if (is_array($data)) {
		$data = json_encode($data);
	}
	// $data = json_encode([
	//     "username"      => "sample-demo",
	//     "firstName"     => "Sample",
	//     "lastName"      => "Demo",
	//     "date_of_birth" => "1999-05-08"
	// ]);

	$headers = build_header_from_aws_signature_v4($host, $uri, $requestUrl, $accessKey, $secretKey, $region, $service, $httpRequestMethod, $data, TRUE);
	$result  = exec_api_aws_secure_sign($requestUrl, $httpRequestMethod, $headers, $data, TRUE);

	print_r($result);
}