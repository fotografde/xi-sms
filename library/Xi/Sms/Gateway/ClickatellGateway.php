<?php

/**
 * This file is part of the Xi SMS package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This class implements Clickatell API
 * @link https://www.clickatell.com/downloads/http/Clickatell_HTTP.pdf
 */

namespace Xi\Sms\Gateway;

use Xi\Sms\SmsMessage;

class ClickatellGateway extends BaseHttpRequestGateway
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $endpoint;

    public function __construct(
        $apiKey,
        $user,
        $password,
        $endpoint = 'https://api.clickatell.com'
    ) {
        $this->apiKey = $apiKey;
        $this->user = $user;
        $this->password = $password;
        $this->endpoint = $endpoint;
    }

	/**
	 * Authentication
	 * @return array
	 * @return bool|string Success|Session ID
	 */
	public function authenticate()
	{
		$params = array(
			'api_id' => $this->apiKey,
			'user' => $this->user,
			'password' => $this->password,
		);

		$response_string = $this->getClient()->get(
			$this->endpoint . '/http/auth?'.http_build_query($params),
			array()
		);

		$response = $this->parseResponse($response_string);
		if ($response === false) {
			return false;
		}
		if (!empty($response['ERR'])) {
			return false;
		}
		if (empty($response['OK'])) {
			return false;
		}
		return $response['OK'];
	}

    /**
     * @see GatewayInterface::send
	 * @param SmsMessage $message
	 * @return bool Success
     */
    public function send(SmsMessage $message)
    {
		// Sending is limited to max 100 addressees
		if (count($message->getTo()) > 100) {
			foreach (array_chunk($message->getTo(), 100) as $tos) {
				$message_alt = new SmsMessage(
					$message->getBody(),
					$message->getFrom(),
					$tos
				);
				$this->send($message_alt);
			}
			return true;
		}

		$params = array(
			'api_id' => $this->apiKey,
			'user' => $this->user,
			'password' => $this->password,
			'to' => implode(',', $message->getTo()),
			'text' => utf8_decode($message->getBody()),
			'from' => $message->getFrom()
		);

		$response_string = $this->getClient()->get(
			$this->endpoint . '/http/sendmsg?'.http_build_query($params),
			array()
		);
		$response = $this->parseResponse($response_string);
		if (!empty($response['ERR'])) {
			return false;
		}
		if (empty($response['ID'])) {
			return false;
		}
		return true;
    }

	/**
	 * Parses a Clickatell HTTP API response
	 * @param string $response
	 * @return array error messages, messages IDs, phone numbers...
	 * @return bool|array Success|Parsed API response
	 */
	public static function parseResponse($response) {
		$return = array(
			'id' => null,
			'error' => null
		);
		if (preg_match_all('/((ERR|ID|OK): ([^\n]*))+/', $response, $matches)) {
			for ($i = 0; $i < count($matches[0]); $i++) {
				$phone_number = null;
				if (preg_match('/(.*)( To: ([0-9]+))$/', $matches[3][$i], $ms)) {
					$message = $ms[1];
					$phone_number = $ms[3];
				} else {
					$message = $matches[3][$i];
				}

				$key = $matches[2][$i];
				if ($phone_number) {
					$return[$key][$phone_number] = $message;
				} else {
					$return[$key] = $message;
				}
			}
			return $return;
		} else {
			return false;
		}
	}
}
