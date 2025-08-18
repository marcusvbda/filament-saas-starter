<?php

namespace App\Filament\Resources\UserResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

/**
 * @property User $resource
 */
class UserTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $result = $this->resource->toArray();
        $result["phones"] = $this->phones()->select(["type", "number"])->get()->toArray();
        $result["addresses"] = $this->addresses()->select([
            'name',
            'zipcode',
            'district',
            'street',
            'number',
            'complement',
            'state',
            'city'
        ])->get()->toArray();
        return $result;
    }
}
