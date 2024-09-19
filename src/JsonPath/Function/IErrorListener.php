<?php

namespace JsonScout\JsonPath\Function;



interface IErrorListener
{
    public function processError(FunctionExtension $extension, string $message) : void;
}
