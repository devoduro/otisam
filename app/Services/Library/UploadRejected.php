<?php

namespace App\Services\Library;

use RuntimeException;

/** Thrown with a message that is safe to show to the person who uploaded the file. */
class UploadRejected extends RuntimeException {}
