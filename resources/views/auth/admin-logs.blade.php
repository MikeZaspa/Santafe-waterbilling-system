<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login Logs | Santa Fe Water Billing System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .avatar-sm {
            width: 32px;
            height: 32px;
            font-size: 14px;
            font-weight: bold;
        }
        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
        }
        .badge-active {
            background-color: #28a745;
        }
        .badge-inactive {
            background-color: #6c757d;
        }
        #map {
            height: 400px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .location-pin {
            cursor: pointer;
            color: #1a73e8;
        }
        .location-pin:hover {
            color: #0d5bba;
        }
        .map-popup {
            font-family: Arial, sans-serif;
        }
        .map-popup h6 {
            margin-bottom: 5px;
            color: #1a73e8;
        }
        .map-popup p {
            margin: 2px 0;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-history me-2"></i>Admin Login Logs
                        </h4>
                        <button class="btn btn-light btn-sm" onclick="toggleMap()">
                            <i class="fas fa-map me-1"></i> Toggle Map
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <form action="{{ route('admin.logs.filter') }}" method="GET" class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Admin</label>
                                        <select name="admin_id" class="form-select">
                                            <option value="">All Admins</option>
                                            @foreach($admins as $admin)
                                                <option value="{{ $admin->id }}" {{ request('admin_id') == $admin->id ? 'selected' : '' }}>
                                                    {{ $admin->email }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Activity</label>
                                        <input type="text" name="activity" class="form-control" placeholder="Search activity..." value="{{ request('activity') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Date From</label>
                                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Date To</label>
                                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                                        <a href="{{ route('admin.logs') }}" class="btn btn-secondary">Reset</a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Geolocation Map -->
                        <div id="mapContainer" class="d-none">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">
                                    <i class="fas fa-map-marker-alt me-2"></i>Login Locations Map
                                </h6>
                                <small class="text-muted">Click on location pins to see details</small>
                            </div>
                            <div id="map"></div>
                        </div>

                        <!-- Logs Table -->
                        <div class="table-responsive mt-4">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Admin</th>
                                        <th>IP Address</th>
                                        <th>Location</th>
                                        <th>Device</th>
                                        <th>Activity</th>
                                        <th>Login Time</th>
                                        <th>Logout Time</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        <tr data-log-id="{{ $log->id }}" 
                                            data-ip="{{ $log->ip_address }}"
                                            data-country="{{ $log->country }}"
                                            data-city="{{ $log->city }}"
                                            data-region="{{ $log->region }}"
                                            data-email="{{ $log->email }}"
                                            data-activity="{{ $log->activity }}"
                                            data-login-time="{{ $log->login_at->format('M j, Y g:i A') }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-2">
                                                        {{ strtoupper(substr($log->email, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $log->email }}</div>
                                                        <small class="text-muted">
                                                            @if($log->admin)
                                                                {{ $log->admin->first_name }} {{ $log->admin->last_name }}
                                                            @else
                                                                Admin Deleted
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <code>{{ $log->ip_address }}</code>
                                            </td>
                                            <td>
                                                @if($log->city && $log->country)
                                                    <div class="d-flex align-items-center">
                                                        <span>{{ $log->city }}, {{ $log->country }}</span>
                                                        <i class="fas fa-map-marker-alt location-pin ms-2" 
                                                           onclick="showLocationOnMap('{{ $log->ip_address }}', '{{ $log->city }}', '{{ $log->country }}', '{{ $log->region }}', '{{ $log->email }}', '{{ $log->activity }}', '{{ $log->login_at->format('M j, Y g:i A') }}')"
                                                           title="Show on map"></i>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Unknown</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>
                                                    <i class="fas fa-desktop me-1 text-muted"></i> {{ $log->browser }}<br>
                                                    <i class="fas fa-laptop me-1 text-muted"></i> {{ $log->platform }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    @if(strpos($log->activity, 'successful')) bg-success
                                                    @elseif(strpos($log->activity, 'failed')) bg-danger
                                                    @else bg-primary @endif">
                                                    {{ $log->activity }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $log->login_at->format('M j, Y') }}<br>
                                                    {{ $log->login_at->format('g:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                @if($log->logout_at)
                                                    <small>
                                                        {{ $log->logout_at->format('M j, Y') }}<br>
                                                        {{ $log->logout_at->format('g:i A') }}
                                                    </small>
                                                @else
                                                    <span class="badge bg-warning">Active</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->logout_at)
                                                    @php
                                                        $seconds = $log->session_duration;
                                                        $hours = floor($seconds / 3600);
                                                        $minutes = floor(($seconds % 3600) / 60);
                                                        $seconds = $seconds % 60;
                                                    @endphp
                                                    <span class="badge bg-info">
                                                        {{ sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->logout_at)
                                                    <span class="badge bg-secondary">Completed</span>
                                                @else
                                                    <span class="badge bg-success">Active</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                <i class="fas fa-search fa-2x text-muted mb-3"></i>
                                                <p class="text-muted">No logs found</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $logs->links() }}
                        </div>

                        <!-- Statistics -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Quick Statistics</h6>
                                        <div class="row text-center">
                                            <div class="col-md-3">
                                                <div class="border rounded p-3">
                                                    <h4 class="text-primary">{{ $logs->total() }}</h4>
                                                    <small class="text-muted">Total Logs</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="border rounded p-3">
                                                    <h4 class="text-success">{{ $logs->where('activity', 'like', '%successful%')->count() }}</h4>
                                                    <small class="text-muted">Successful Logins</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="border rounded p-3">
                                                    <h4 class="text-danger">{{ $logs->where('activity', 'like', '%failed%')->count() }}</h4>
                                                    <small class="text-muted">Failed Attempts</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="border rounded p-3">
                                                    <h4 class="text-warning">{{ $logs->whereNull('logout_at')->count() }}</h4>
                                                    <small class="text-muted">Active Sessions</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map;
        let markers = [];
        let mapVisible = false;

        // Initialize map when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            loadAllLocations();
        });

        function initMap() {
            // Default center (Philippines)
            map = L.map('map').setView([12.8797, 121.7740], 5);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
        }

        function toggleMap() {
            const mapContainer = document.getElementById('mapContainer');
            mapVisible = !mapVisible;
            
            if (mapVisible) {
                mapContainer.classList.remove('d-none');
                // Refresh map bounds to show all markers
                setTimeout(() => {
                    map.invalidateSize();
                    if (markers.length > 0) {
                        const group = new L.featureGroup(markers);
                        map.fitBounds(group.getBounds().pad(0.1));
                    }
                }, 100);
            } else {
                mapContainer.classList.add('d-none');
            }
        }

        function loadAllLocations() {
            // Clear existing markers
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            // Get all log rows with location data
            const logRows = document.querySelectorAll('tbody tr[data-country]');
            
            logRows.forEach(row => {
                const country = row.dataset.country;
                const city = row.dataset.city;
                const region = row.dataset.region;
                const ip = row.dataset.ip;
                const email = row.dataset.email;
                const activity = row.dataset.activity;
                const loginTime = row.dataset.loginTime;

                if (country && country !== 'Local') {
                    // Geocode the location
                    geocodeLocation(city, country, region, ip, email, activity, loginTime);
                }
            });
        }

        function geocodeLocation(city, country, region, ip, email, activity, loginTime) {
            const query = `${city}, ${region}, ${country}`;
            
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lon = parseFloat(data[0].lon);
                        
                        addMarker(lat, lon, city, country, region, ip, email, activity, loginTime);
                    }
                })
                .catch(error => {
                    console.error('Geocoding error:', error);
                });
        }

        function addMarker(lat, lon, city, country, region, ip, email, activity, loginTime) {
            // Determine marker color based on activity
            let markerColor = 'blue';
            if (activity.includes('failed')) {
                markerColor = 'red';
            } else if (activity.includes('successful')) {
                markerColor = 'green';
            }

            // Create custom icon
            const customIcon = L.divIcon({
                html: `<div style="background-color: ${markerColor}; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
                className: 'custom-marker',
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });

            const marker = L.marker([lat, lon], { icon: customIcon }).addTo(map);
            
            // Create popup content
            const popupContent = `
                <div class="map-popup">
                    <h6><i class="fas fa-user"></i> ${email}</h6>
                    <p><strong>Location:</strong> ${city}, ${country}</p>
                    <p><strong>IP Address:</strong> ${ip}</p>
                    <p><strong>Activity:</strong> <span class="badge ${activity.includes('successful') ? 'bg-success' : activity.includes('failed') ? 'bg-danger' : 'bg-primary'}">${activity}</span></p>
                    <p><strong>Login Time:</strong> ${loginTime}</p>
                    <p><strong>Region:</strong> ${region || 'N/A'}</p>
                </div>
            `;
            
            marker.bindPopup(popupContent);
            markers.push(marker);
        }

        function showLocationOnMap(ip, city, country, region, email, activity, loginTime) {
            // Show map if hidden
            if (!mapVisible) {
                toggleMap();
            }

            // Clear existing markers and focus on this one
            markers.forEach(marker => map.removeLayer(marker));
            markers = [];

            if (city && country && country !== 'Local') {
                geocodeLocation(city, country, region, ip, email, activity, loginTime);
                
                // Wait a bit for geocoding to complete, then fit bounds
                setTimeout(() => {
                    if (markers.length > 0) {
                        const group = new L.featureGroup(markers);
                        map.fitBounds(group.getBounds().pad(0.1));
                        
                        // Open the popup
                        markers[0].openPopup();
                    }
                }, 500);
            } else {
                alert('No geographic location data available for this IP address.');
            }
        }

        // Add click event to all location pins
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('location-pin')) {
                    const row = e.target.closest('tr');
                    if (row) {
                        const ip = row.dataset.ip;
                        const city = row.dataset.city;
                        const country = row.dataset.country;
                        const region = row.dataset.region;
                        const email = row.dataset.email;
                        const activity = row.dataset.activity;
                        const loginTime = row.dataset.loginTime;
                        
                        showLocationOnMap(ip, city, country, region, email, activity, loginTime);
                    }
                }
            });
        });
    </script>
</body>
</html>