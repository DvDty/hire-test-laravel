<?php

namespace App\Http\Requests;

use App\Enums\TirePosition;
use App\Models\Tire;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class MaintenanceRequestStore extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'car_id' => ['required', 'exists:cars,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'scheduled_date' => ['nullable', 'date', 'after:now'],
            'tire_replacements' => ['required', 'array'],
            'tire_replacements.*.tire_id' => ['required', 'exists:tires,id'],
            'tire_replacements.*.position' => ['required', Rule::enum(TirePosition::class)],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->failed()) {
                    return;
                }

                $data = $validator->getData();
                $replacements = $data['tire_replacements'];

                $tireIds = collect($replacements)->pluck('tire_id')->filter()->unique();

                /** @var Collection<int, Tire> $tires */
                $tires = Tire::whereIn('id', $tireIds)->get()->keyBy('id');

                foreach ($replacements as $index => $replacement) {
                    $tire = $tires->get($replacement['tire_id']);

                    if (!$tire->hasStock()) {
                        $validator->errors()->add(
                            "tire_replacements.$index.tire_id",
                            'Selected tire is out of stock.',
                        );
                    }
                }
            }
        ];
    }
}
