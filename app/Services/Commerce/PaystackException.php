<?php

namespace App\Services\Commerce;

use RuntimeException;

/** Paystack could not be reached or refused the request. The message is safe to show. */
class PaystackException extends RuntimeException {}
