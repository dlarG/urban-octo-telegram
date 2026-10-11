import L from "leaflet";
import "leaflet/dist/leaflet.css";
import markerIcon2x from "leaflet/dist/images/marker-icon-2x.png";
import markerIcon from "leaflet/dist/images/marker-icon.png";
import markerShadow from "leaflet/dist/images/marker-shadow.png";

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

// --- Picker (landlord: click to place pin) ---
document.addEventListener("DOMContentLoaded", () => {
    const el = document.getElementById("map-picker");
    if (el) {
        const startLat = parseFloat(el.dataset.lat);
        const startLng = parseFloat(el.dataset.lng);
        const map = L.map(el).setView([startLat, startLng], 15);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors",
            maxZoom: 19,
        }).addTo(map);
        const marker = L.marker([startLat, startLng], {
            draggable: true,
        }).addTo(map);
        const latInput = document.getElementById("lat");
        const lngInput = document.getElementById("lng");
        const setPos = (lat, lng) => {
            latInput.value = lat.toFixed(7);
            lngInput.value = lng.toFixed(7);
        };
        setPos(startLat, startLng);
        marker.on("dragend", (e) => {
            const { lat, lng } = e.target.getLatLng();
            setPos(lat, lng);
        });
        map.on("click", (e) => {
            marker.setLatLng(e.latlng);
            setPos(e.latlng.lat, e.latlng.lng);
        });
    }
});

// --- Viewer (public + renter: show pin, no interaction) ---
function initMapViewer(el) {
    const lat = parseFloat(el.dataset.lat);
    const lng = parseFloat(el.dataset.lng);

    const map = L.map(el, { scrollWheelZoom: false }).setView([lat, lng], 16);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap contributors",
        maxZoom: 19,
    }).addTo(map);

    L.marker([lat, lng]).addTo(map);
}

window.rsInitMapViewer = initMapViewer;

document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("[data-map-viewer]").forEach(initMapViewer);
});
