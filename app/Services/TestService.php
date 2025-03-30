<?php

namespace App\Services;

use App\Models\Test;
use Illuminate\Database\Eloquent\Collection;

class TestService
{
    public function index(): Collection
    {
        return Test::query()
            ->orderBy('id')
            ->get();
    }

    public function create(array $data): ?Test
    {
        /** @var Test $test */
        $test = Test::query()->create($data);

        return $test;
    }

    public function update(Test $test, array $data): bool
    {
        return $test->update($data);
    }

    public function delete(Test $test): bool
    {
        return $test->delete();
    }
}