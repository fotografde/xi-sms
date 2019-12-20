<?php

/**
 * This file is part of the Xi SMS package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Xi\Sms;

/**
 * SMS message
 */
class SmsMessage
{
    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $from;

    /**
     * @var array
     */
    private $to = array();

    /**
     * @var array
     */
    private $mediaObjects = array();

    /**
     * @param string $body
     * @param string $from
     * @param array|string $to
     * @param string[]|string $mediaObjects
     */
    public function __construct($body = null, $from = null, $to = array(), $mediaObjects = array())
    {
        $this->body = $body;
        $this->from = $from;

        if ($to) {
            $this->setTo($to);
        }

        if ($mediaObjects) {
            $this->setMediaObjects($mediaObjects);
        }
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return null|string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Sets receiver or an array of receivers
     *
     * @param string|array $to
     */
    public function setTo($to)
    {
        if (!is_array($to)) {
            $to = array($to);
        }
        $this->to = $to;
    }

    /**
     * @return array
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param string $to
     */
    public function addTo($to)
    {
        $this->to[] = $to;
    }

    /**
     * @param string $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string|string[] $mediaObjects
     */
    public function setMediaObjects($mediaObjects)
    {
        if (!is_array($mediaObjects)) {
            $mediaObjects = array($mediaObjects);
        }
        $this->mediaObjects = $mediaObjects;
    }

    /**
     * @return string[]
     */
    public function getMediaObjects()
    {
        return $this->mediaObjects;
    }
}
