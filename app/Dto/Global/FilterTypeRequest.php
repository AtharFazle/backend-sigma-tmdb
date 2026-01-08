<?php

namespace App\Dto\Global;

use App\Traits\GlobalTrait;
use DateTime;

/**
 * Class FilterTypeRequest
 *
 * @package	App\Dto
 * 
 */
class FilterTypeRequest
{
    use GlobalTrait;

    public function __construct(
        public ?string $start_date,
        public ?string $end_date,
        public ?string $search,
        public ?string $genres = null,
        public ?bool $adult = false,
        public ?int $page = 1,
        public ?int $per_page = 10
    ) {}

    public static function fromRequest(array $request)
    {
        $adult = $request['adult'] ?? null;

        if (!empty($adult)) {
            $adult = $adult === 'true' ? true : false;
        };
        return new self(
            start_date: $request['start_date'] ?? null,
            end_date: $request['end_date'] ?? null,
            search: $request['search'] ?? null,
            genres: $request['genres'] ?? null,
            adult: $adult,
            page: $request['page'] ?? null,
            per_page: $request['per_page'] ?? null
        );
    }
}
