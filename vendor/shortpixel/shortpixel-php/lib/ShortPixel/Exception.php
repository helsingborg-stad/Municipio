<?php

namespace ShortPixel;

class Exception extends \Exception {
    public static function create($message, $type, $status) {
        if ($status == 401 || $status == 429) {
            $klass = "ShortPixel\AccountException";
        } else if($status >= 400 && $status <= 499) {
            $klass = "ShortPixel\ClientException";
        } else if($status >= 500 && $status <= 599) {
            $klass = "ShortPixel\ServerException";
        } else {
            $klass = "ShortPixel\Exception";
        }

        if (empty($message)) $message = "No message was provided";
        return new $klass($type . ": " . $message, $status);
    }

    function __construct($message, $code = 0, $parent = NULL, $type = NULL, $status = NULL) {
        if ($status) {
            parent::__construct($message . " (HTTP " . $status . "/" . $type . ")", $code, $parent);
        } else {
            parent::__construct($message, $code, $parent);
        }
    }
}

class AccountException extends Exception {}
class ClientException extends Exception {
    const NO_FILE_FOUND = -1;
}
class ServerException extends Exception {}
class ConnectionException extends Exception {}
class PersistException extends Exception {}

