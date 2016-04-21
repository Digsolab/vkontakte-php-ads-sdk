<?php

namespace DSL\Client;

/**
 * @author Lexey Felde <a.felde@digsolab.com>
 */
interface ResponseInterface
{
    /**
     * Return API error code
     *
     * @return int
     */
    public function getErrorCode();

    /**
     * Return API error message
     *
     * @return mixed
     */
    public function getError();

    /**
     * Is response error?
     *
     * @return bool
     */
    public function isError();

    /**
     * Return API HTTP code
     *
     * @return int
     */
    public function getCode();

    /**
     * Return API content
     *
     * @return mixed
     */
    public function getContent();
}