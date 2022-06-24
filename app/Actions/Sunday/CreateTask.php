<?php

namespace App\Actions\Sunday;

use Illuminate\Support\Facades\Validator;

class CreateTask
{
    /**
     * Validate and create a new team for the given user.
     *
     * @param  mixed  $item
     * @param  array  $input
     * @return mixed
     */
    public function create($item, array $input)
    {
        Validator::make($input, [
            'name' => 'required|string|max:255',
        ]);

        return $item->create([
            'name' => $input['name'],
        ]);
    }
}
