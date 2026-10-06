import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

import L from "leaflet";
import "leaflet/dist/leaflet.css";

// Fix leaflet default icon path issue with Vite
import markerIcon2x from "leaflet/dist/images/marker-icon-2x.png";
import markerIcon from "leaflet/dist/images/marker-icon.png";
import markerShadow from "leaflet/dist/images/marker-shadow.png";

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: markerIcon2x,
    iconUrl: markerIcon,
    shadowUrl: markerShadow,
});

document.addEventListener("DOMContentLoaded", () => {
    const el = document.getElementById("map-picker");
    if (!el) return;

    const startLat = parseFloat(el.dataset.lat);
    const startLng = parseFloat(el.dataset.lng);

    const map = L.map(el).setView([startLat, startLng], 15);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap contributors",
        maxZoom: 19,
    }).addTo(map);

    const marker = L.marker([startLat, startLng], { draggable: true }).addTo(
        map
    );

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
});
