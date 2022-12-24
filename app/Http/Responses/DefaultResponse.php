<?php

namespace App\Http\Responses;

class DefaultResponse
{
    /**
     * Status of the response.
     *
     * @var "success" | "failed"
     */
    private $status;

    /**
     * Message to be returned in response.
     *
     * @var string
     */
    private $message;

    /**
     * Result of the response
     *
     * @var array
     */
    private $result;

    /**
     * Create a new Default Response instance.
     *
     * @param "success" | "failed"  $status
     * @param string  $message
     * @param array  $result
     * @return DefaultResponse
     */
    public function __construct($status, $message, $result)
    {
        $this->status = $status;
        $this->message = $message;
        $this->result = $result;

        return $this;
    }

    /**
     * Get formatted response.
     *
     * @return array
     */
    public function get()
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'result' => $this->result,
        ];
    }

    /**
     * Create a new Default Response.
     *
     * @param "success" | "failed"  $status
     * @param string  $message
     * @param array  $result
     * @return array
     */
    public static function parse($status, $message, $result)
    {
        return [
            'status' => $status,
            'message' => $message,
            'result' => $result,
        ];
    }

    /**
     * Modify current status of Default Response instance.
     *
     * @param "success" | "failed"  $status
     * @return DefaultResponse
     */
    public function status($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Modify current message of Default Response instance.
     *
     * @param string  $message
     * @return DefaultResponse
     */
    public function message($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Modify current result of Default Response instance.
     *
     * @param array  $result
     * @return DefaultResponse
     */
    public function result($result)
    {
        $this->result = $result;
        return $this;
    }
}
