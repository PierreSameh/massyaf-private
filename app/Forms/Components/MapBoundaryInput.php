<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class MapBoundaryInput extends Field
{
    protected string $view = 'forms.components.map-boundary-input';

    public function apiKey(string $apiKey): static
    {
        return $this->extraAttributes(['data-api-key' => $apiKey]);
    }

    public function latField(string $statePath): static
    {
        return $this->extraAttributes(array_merge($this->getExtraAttributes(), ['lat-field' => $statePath]));
    }

    public function lngField(string $statePath): static
    {
        return $this->extraAttributes(array_merge($this->getExtraAttributes(), ['lng-field' => $statePath]));
    }

}
