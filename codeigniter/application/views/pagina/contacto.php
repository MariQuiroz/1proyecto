<div class="location-section py-5">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="font-weight-bold">Nuestra Ubicación</h2>
            <p class="text-muted">Encuentra la Hemeroteca José Antonio Arze en la Biblioteca Central</p>
        </div>

        <div class="row">
            <div class="col-md-5">
                <div class="location-info bg-light p-4 rounded shadow-sm mb-4">
                    <h4 class="font-weight-bold mb-3">¿Cómo Llegar?</h4>
                    
                    <div class="mb-3">
                        <h5 class="font-weight-bold"><i class="la la-map-marker la-lg text-primary mr-2"></i>Dirección</h5>
                        <p class="text-muted ml-4 mb-2">
                            Biblioteca Central<br>
                            Campus Central UMSS<br>
                            Av. Ballivián esq. Oquendo<br>
                            Cochabamba, Bolivia
                        </p>
                    </div>

                    <div class="mb-3">
                        <h5 class="font-weight-bold"><i class="la la-clock la-lg text-primary mr-2"></i>Horario</h5>
                        <p class="text-muted ml-4 mb-2">
                            Lunes a Viernes: 8:00 AM - 6:00 PM<br>
                            Sábados: 8:00 AM - 12:00 PM
                        </p>
                    </div>

                    <div>
                        <h5 class="font-weight-bold"><i class="la la-bus la-lg text-primary mr-2"></i>Transporte</h5>
                        <ul class="text-muted ml-4 list-unstyled mb-0">
                            <li><i class="la la-check-circle mr-2"></i>Línea Micro L: Av. Oquendo</li>
                            <li><i class="la la-check-circle mr-2"></i>Línea 200: Circuito UMSS</li>
                            <li><i class="la la-check-circle mr-2"></i>Trufi 230: Av. Ballivián</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="map-container bg-white p-3 rounded shadow-sm">
                    <div id="map" class="map-responsive"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incluir CSS de Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Incluir JavaScript de Leaflet -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lat = -17.395292955350623;
        const lng = -66.14790072771176;

        const map = L.map('map').setView([lat, lng], 19);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const marker = L.marker([lat, lng]).addTo(map);
        marker.bindPopup("<b>Hemeroteca José Antonio Arze</b><br>Biblioteca Central UMSS").openPopup();
    });
</script>

<style>
    .location-section {
        background-color: #f8f9fa;
        position: relative;
        z-index: 1; 

    .location-info i {
        width: 30px;
    }

    .location-info ul li {
        margin-bottom: 8px;
    }

    .location-info .la-check-circle {
        color: var(--success);
    }

    .map-container {
        position: relative;
        z-index: 1; /* Mantiene el mapa dentro de su contenedor */
    }

    .map-responsive {
        position: relative;
        height: 0;
        padding-bottom: 56.25%; /* Ratio 16:9 */
        overflow: hidden;
    }

    .map-responsive #map {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 4px;
    }

    /* Asegura que los controles del mapa no se sobrepongan */
    .leaflet-control-container {
        position: relative;
        z-index: 2;
    }

    /* Ajuste para el popup */
    .leaflet-popup {
        position: absolute;
        z-index: 3;
    }

    @media (max-width: 768px) {
        .map-responsive {
            padding-bottom: 75%;
        }
    }
</style>