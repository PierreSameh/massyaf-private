<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class MapInput extends Field
{
    protected string $view = 'forms.components.map-input';

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateUpdated(function ($state, $livewire) {
            $livewire->dispatch('mapCoordinatesUpdated', $state);
        });
    }
}
