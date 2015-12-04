<?php


abstract class TestBase extends PHPUnit_Framework_TestCase
{
    const ExceptionName_UnexpectedValue = "UnexpectedValueException";
    const ExceptionName_InvalidArgument = "InvalidArgumentException";
    const ExceptionName_OutOfRange = "OutOfRangeException";
    const ExceptionName_Runtime = "RuntimeException";

    protected function assertException($closure, $expected = self::ExceptionName_Runtime)
    {
        try {
            $closure();
        } catch (Exception $ex) {
            $exName = get_class($ex);

            if ($exName != $expected) {
                $this->fail("Wrong exception raised. Expected: '" . $expected . "' Actual: '" . get_class($ex) . "'. Message: " . $ex->getMessage());
            }
            return;
        }

        $this->fail($expected . ' has not been raised.');
    }
}