<?php

use Opis\JsonSchema\Validator;
use Opis\JsonSchema\Errors\ErrorFormatter;

/*
A simple RESTful webservices base class
Use this as a template and build upon it
Reference : https://phppot.com/php/php-restful-web-service/
*/

class SimpleRest
{

	private $httpVersion = "HTTP/1.1";

	public function setHttpHeaders($contentType, $statusCode)
	{

		$statusMessage = $this->getHttpStatusMessage($statusCode);

		header($this->httpVersion . " " . $statusCode . " " . $statusMessage);
		header("Content-Type:" . $contentType);
	}

	public function getHttpStatusMessage($statusCode)
	{
		$httpStatus = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => '(Unused)',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported'
		);
		return ($httpStatus[$statusCode]) ? $httpStatus[$statusCode] : $httpStatus[500];
	}

	public function sendPostRequest($url, $data)
	{
		$postData = http_build_query([
			'subjects' => json_encode($data) // still encode array as JSON string
		]);
		$context = stream_context_create([
			'http' => [
				'method'  => 'POST',
				'header'  => implode("\r\n", [
					'Content-Type: application/x-www-form-urlencoded',
					'Content-Length: ' . strlen($postData),
					'Accept: application/json'
				]),
				'content' => $postData,
				'ignore_errors' => true
			]
		]);
		$response = json_decode(file_get_contents(
			$url,
			false,
			$context
		), true);
		return $response;
	}

	public function validateEdit($data)
	{
		$schemaData = json_decode(file_get_contents("schemas/editSchema.json"));

		// Validate the data against the schema
		$validator = new Validator();
		$result = $validator->validate((object) $data, $schemaData);

		if ($result->isValid()) {
			return ["success" => 1, "errors" => []];
		} else {
			return ["success" => 0, "errors" => [...array_values((new ErrorFormatter())->format($result->error()))][0]];
		}
	}
}
