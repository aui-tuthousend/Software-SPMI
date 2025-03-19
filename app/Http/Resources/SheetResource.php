<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SheetResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        // return parent::toArray($request);
        return [
            "id" => $this->id,
            "jurusan" => $this->jurusan,
            "periode" => $this->periode,
            "tipe" => $this->tipe_sheet,
            "note" => $this->note,
        ];
    }
}
