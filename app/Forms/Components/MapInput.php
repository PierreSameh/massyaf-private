<?php

namespace App\Forms\Components;

use App\Models\City;
use Filament\Forms\Components\Field;

class MapInput extends Field
{
    protected string $view = 'forms.components.map-input';

    public function apiKey(string $apiKey): static
    {
        return $this->extraAttributes(['data-api-key' => $apiKey]);
    }
    

}
