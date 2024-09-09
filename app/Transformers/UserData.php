<?php

namespace App\Transformers;

use App\Models\User;
use Spatie\LaravelData\Data;
use Illuminate\Support\Carbon;

class UserData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public array $roles,
        public ?Carbon $created_at,
        public ?Carbon $updated_at,
    )
    {
    }

    public static function fromModel(User $model)
    {
        return new self(
            $model->id,
            $model->name,
            $model->email,
            $model->getRoleNames()->toArray(),
            $model->created_at,
            $model->updated_at
        );
    }
}
