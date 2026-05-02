<?php
/**
 * Custom Exception for Coupon Validation Errors
 */

namespace App\Exceptions;

use Exception;

class InvalidCouponException extends Exception
{
    /**
     * Create a new InvalidCouponException instance.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = "Invalid or expired coupon code.", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
