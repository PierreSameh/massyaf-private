<div wire:ignore x-data="mapInput()" x-init="initMap">
    <div id="map" style="height: 400px; width: 100%;"></div>
    <input type="hidden" x-model="coordinates" {{ $attributes }} />
</div>

<script>
    function mapInput() {
        return {
            coordinates: @entangle('coordinates'), // Bind to the Livewire property
            map: null,
            markers: [],
            polygon: null,

            initMap() {
                window.initMapCallback = () => {
                    console.log('Google Maps API loaded successfully!');
                    this.map = new google.maps.Map(document.getElementById('map'), {
                        center: { lat: 26.8206, lng: 30.8025 }, // Center on Egypt
                        zoom: 6, // Adjust zoom level for Egypt
                    });

                    // Debug: Log the coordinates
                    console.log('Initial Coordinates:', this.coordinates);

                    // Load existing markers if coordinates are provided
                    if (this.coordinates) {
                        try {
                            const coords = JSON.parse(this.coordinates);
                            console.log('Parsed Coordinates:', coords); // Debugging

                            // Clear existing markers
                            this.markers.forEach(marker => marker.setMap(null));
                            this.markers = [];

                            // Add markers to the map
                            coords.forEach(coord => {
                                if (coord.lat && coord.lng) {
                                    console.log('Adding marker:', coord); // Debugging
                                    this.addMarker({ lat: parseFloat(coord.lat), lng: parseFloat(coord.lng) });
                                }
                            });

                            // Update the polygon
                            this.updateCoordinates();
                        } catch (e) {
                            console.error('Invalid coordinates format:', this.coordinates);
                        }
                    }

                    // Add a click event listener to the map
                    this.map.addListener('click', (event) => {
                        const latLng = event.latLng;
                        this.addMarker(latLng);
                        this.updateCoordinates();
                    });
                };
            },

            addMarker(latLng) {
                // Clear existing markers if there are already 4
                if (this.markers.length >= 4) {
                    alert('You can only place 4 markers.'); // Notify the user
                    return;
                }

                // Add a new marker
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(latLng.lat), lng: parseFloat(latLng.lng) },
                    map: this.map,
                    draggable: true, // Allow markers to be moved
                    zIndex: 1000, // Ensure markers are on top of the polygon
                });

                // Add a right-click event to remove the marker
                marker.addListener('rightclick', () => {
                    marker.setMap(null); // Remove the marker from the map
                    this.markers = this.markers.filter(m => m !== marker); // Remove from the array
                    this.updateCoordinates(); // Update the coordinates
                });

                // Add a dragend event to update coordinates when the marker is moved
                marker.addListener('dragend', () => {
                    this.updateCoordinates();
                });

                this.markers.push(marker);
            },

            updateCoordinates() {
                if (this.markers.length === 4) {
                    const coords = this.markers.map(marker => ({
                        lat: marker.getPosition().lat(),
                        lng: marker.getPosition().lng(),
                    }));

                    console.log('Updated Coordinates:', coords); // Debugging
                    this.coordinates = JSON.stringify(coords);

                    // Draw a polygon connecting the markers
                    if (this.polygon) {
                        this.polygon.setMap(null); // Remove the existing polygon
                    }

                    this.polygon = new google.maps.Polygon({
                        paths: this.markers.map(marker => marker.getPosition()),
                        strokeColor: '#FF0000',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '#FF0000',
                        fillOpacity: 0.35,
                        map: this.map,
                        zIndex: 1, // Ensure the polygon is below the markers
                    });
                } else {
                    // Remove the polygon if there are not exactly 4 markers
                    if (this.polygon) {
                        this.polygon.setMap(null);
                        this.polygon = null;
                    }
                }
            },
        };
    }

    // Load the Google Maps API script
    function loadGoogleMapsScript() {
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?&callback=initMapCallback`;
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }

    // Load the script when the component is initialized
    loadGoogleMapsScript();
</script>