<?php

namespace App\Dto\Global;

use App\Traits\GlobalTrait;

/**
 * Class HelperResponseDto
 *
 * @package	App\Helpers
 * 
 */
class HelperResponseDto
{
    use GlobalTrait;

    public function __construct(
        public bool $status,
        public int $code,
        public string $message = '',
        public $data = null,
        public $meta = null,
        public string $dev = '',
    ) {}
}
