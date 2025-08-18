<?php

namespace App\Filament\Resources\UserResource\Api\Handlers;

use App\Filament\Resources\UserResource;
use Rupadana\ApiService\Http\Handlers;
use Illuminate\Http\Request;
use App\Filament\Resources\UserResource\Api\Transformers\UserTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/detail';
    public static string | null $resource = UserResource::class;


    /**
     * Show User Details
     *
     * @param Request $request
     * @return UserTransformer
     */
    public function handler()
    {
        return new UserTransformer(auth()->user());
    }
}
