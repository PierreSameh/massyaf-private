<div wire:ignore x-data="mapInput()" x-init="initMap">
    <div id="map" style="height: 400px; width: 100%;"></div>
    <input type="hidden" x-model="coordinates" {{ $attributes }} />
</div>

<script>
    function mapInput() {
        return {
            coordinates: @json($getRecord() ? $getRecord()->coordinates : []),
            map: null,
            markers: [],
            polygon: null,

            initMap() {
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key={{ $getExtraAttributes()['data-api-key'] }}&callback=initMapCallback`;
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);

                window.initMapCallback = () => {
                    this.map = new google.maps.Map(document.getElementById('map'), {
                        center: { lat: 26.8206, lng: 30.8025 },
                        zoom: 6,
                    });

                    console.log('Initial coordinates:', this.coordinates);

                    // Add markers if initial coordinates exist
                    if (this.coordinates && this.coordinates.length === 4) {
                        this.coordinates.forEach(coord => {
                            this.addMarker({ lat: parseFloat(coord.lat), lng: parseFloat(coord.lng) });
                        });

                        // Draw the polygon
                        this.updatePolygon();
                    }

                    // Add click listener to add markers
                    this.map.addListener('click', (event) => {
                        if (this.markers.length >= 4) {
                            alert("You can only have 4 markers.");
                            return;
                        }
                        this.addMarker(event.latLng);
                        this.updateCoordinates();
                    });
                };
            },

            addMarker(position) {
                const marker = new google.maps.Marker({
                    position,
                    map: this.map,
                    draggable: true,
                });

                marker.addListener('dragend', () => {
                    this.updateCoordinates();
                });

                marker.addListener('rightclick', () => {
                    marker.setMap(null);
                    this.markers = this.markers.filter(m => m !== marker);
                    this.updateCoordinates();
                });

                this.markers.push(marker);
            },

            updateCoordinates() {
                const coords = this.markers.map(marker => ({
                    lat: marker.getPosition().lat(),
                    lng: marker.getPosition().lng(),
                }));

                // if (coords.length !== 4) {
                //     alert("Please set exactly 4 markers to form a rectangle.");
                //     return;
                // }

                // Emit the updated coordinates to Livewire
                this.$wire.set('coordinates', coords, true);

                // Update the polygon
                this.updatePolygon();

                console.log('Updated coordinates:', coords);
            },

            updatePolygon() {
                const coords = this.markers.map(marker => ({
                    lat: marker.getPosition().lat(),
                    lng: marker.getPosition().lng(),
                }));

                if (coords.length !== 4) {
                    return;
                }

                // Clear existing polygon
                if (this.polygon) {
                    this.polygon.setMap(null);
                }

                // Draw new polygon
                this.polygon = new google.maps.Polygon({
                    paths: coords,
                    strokeColor: "#FF0000",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: "#FF0000",
                    fillOpacity: 0.35,
                    map: this.map,
                });

                console.log('Updated polygon with coordinates:', coords);
            }
        };
    }
</script>