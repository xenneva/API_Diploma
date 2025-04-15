<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class TestPassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'test_id' => $this->id,
            'test_name' => $this->name,
            'score' => round($this->pivot->score),
            'pass_time' => Carbon::createFromTimeString($this->pivot->created_at ?? '')->format('d.m.y H:i:s'),
        ];
    }
}
