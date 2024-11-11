// KidzKlinika coordinates
const CLINIC_COORDS = {
    lat: 10.675897992389598,
    lng: 124.79931121478386
};

function getLocation() {
    const distanceInfo = document.getElementById('distance-info');
    const locationBtn = document.getElementById('get-location-btn');
    
    // Show loading state
    locationBtn.disabled = true;
    locationBtn.innerHTML = '<span class="animate-pulse">Getting location...</span>';
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;
                
                // Update map with directions
                const directionsUrl = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${CLINIC_COORDS.lat},${CLINIC_COORDS.lng}&travelmode=driving`;
                
                // Open directions in new tab
                window.open(directionsUrl, '_blank');
                
                // Calculate approximate distance
                const distance = calculateDistance(userLat, userLng, CLINIC_COORDS.lat, CLINIC_COORDS.lng);
                distanceInfo.textContent = `Approximate distance: ${distance.toFixed(1)} km`;
                distanceInfo.classList.remove('hidden');
                
                // Reset button
                locationBtn.disabled = false;
                locationBtn.innerHTML = `
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    <span class="text-gray-700">Get Directions</span>
                `;
            },
            (error) => {
                console.error('Error getting location:', error);
                distanceInfo.textContent = 'Could not get your location. Please enable location services.';
                distanceInfo.classList.remove('hidden');
                
                // Reset button
                locationBtn.disabled = false;
                locationBtn.innerHTML = `
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    <span class="text-gray-700">Get Directions</span>
                `;
            }
        );
    } else {
        distanceInfo.textContent = 'Geolocation is not supported by your browser.';
        distanceInfo.classList.remove('hidden');
    }
}

// Calculate distance between two points using Haversine formula
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Earth's radius in kilometers
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function toRad(deg) {
    return deg * Math.PI / 180;
} 