<?php

namespace DSL\Client;

/**
 * @author Lexey Felde <a.felde@digsolab.com>
 */
class Response implements ResponseInterface
{
    /** @var int */
    protected $errorCode;
    /** @var mixed */
    protected $errorMessage;
    /** @var int */
    protected $code;
    /** @var mixed */
    protected $content;

    /**
     * Response constructor.
     *
     * @param int   $code
     * @param mixed $content
     * @param int   $errorCode
     * @param mixed $errorMessage
     */
    public function __construct($code, $content, $errorCode = 0, $errorMessage = '')
    {
        $this->code         = $code;
        $this->content      = $content;
        $this->errorCode    = $errorCode;
        $this->errorMessage = $errorMessage;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->errorMessage;
    }

    /**
     * {@inheritdoc}
     */
    public function isError()
    {
        return (int) $this->errorCode !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }
}