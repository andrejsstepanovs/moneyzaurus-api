<?php

namespace Api\Service;


/**
 * Class Locale
 *
 * @package Api\Service
 */
class Json
{
    /**
     * @param array $data
     *
     * @return string
     */
    public function encode(array $data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $jsonString
     *
     * @return array
     */
    public function decode($jsonString)
    {
        return json_decode($jsonString);
    }

    /**
     * @return string
     */
    public function getJsonErrorMessage()
    {
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return 'No errors';
                break;
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                return 'Unknown error';
                break;
        }
    }
}
