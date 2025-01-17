<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class MapInput extends Field
{
    protected string $view = 'forms.components.map-input';

    protected function setUp(): void
    {
        parent::setUp();

        $this->afterStateHydrated(function (Field $component, $state) {
            // If we're editing and have existing coordinates, set them up
            if (is_array($state) && isset($state['lat_top_right'])) {
                $coordinates = [
                    [
                        'lat' => (float)$state['lat_top_right'],
                        'lng' => (float)$state['lng_top_right']
                    ],
                    [
                        'lat' => (float)$state['lat_top_left'],
                        'lng' => (float)$state['lng_top_left']
                    ],
                    [
                        'lat' => (float)$state['lat_bottom_right'],
                        'lng' => (float)$state['lng_bottom_right']
                    ],
                    [
                        'lat' => (float)$state['lat_bottom_left'],
                        'lng' => (float)$state['lng_bottom_left']
                    ]
                ];
                $component->state(json_encode($coordinates));
            }
        });

        $this->afterStateUpdated(function ($state, $livewire) {
            $livewire->dispatch('mapCoordinatesUpdated', $state);
        });
    }
}
