const map = L.map('map').setView([1.17325, 108.97977], 16);

const myMarker = L.marker();

const newStoreMarker = L.marker([], {draggable: true});

const newStoreLatitudeInput = document.getElementById('latitude-new');
const newStoreLongitudeInput = document.getElementById('longitude-new');
const newStoreAddressInput = document.getElementById('address-new');

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

const allStores = [];

function initStores() {
  const url = "/store";

  fetch(url)
    .then(response => response.json())
    .then(data => {
      const popupContent = (store) => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        return `
          <div>
            <form action="/store/${store.id}" method="POST">
              <input type="hidden" name="_method" value="PUT">
              <input type="hidden" name="_token" value="${csrfToken}">
              <div class="flex gap-2">
                <div>
                  <label for="name-${store.id}" class="block mb-2 text-sm font-medium text-gray-900">Nama Toko</label>
                  <input type="text" id="name-${store.id}" name="name" value="${store.name}" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="w-20">
                  <label for="latitude-${store.id}" class="block mb-2 text-sm font-medium text-gray-900">Latitude</label>
                  <input type="text" id="latitude-${store.id}" name="latitude" value="${store.marker.getLatLng().lat}" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="w-20">
                  <label for="longitude-${store.id}" class="block mb-2 text-sm font-medium text-gray-900">Longitude</label>
                  <input type="text" id="longitude-${store.id}" name="longitude" value="${store.marker.getLatLng().lng}" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500">
                </div>
              </div>
              <div>
                <label for="address-${store.id}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat</label>
                <textarea id="address-${store.id}" name="address" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500" placeholder="Alamat toko...">${store.address}</textarea>
              </div>
              <button type="submit" class="mt-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-auto px-5 py-2 text-center">Update</button>
            </form>
            <form action="/store/${store.id}" method="POST">
              <input type="hidden" name="_method" value="DELETE">
              <input type="hidden" name="_token" value="${csrfToken}">
              <button type="submit" class="mt-2 text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-auto px-5 py-2 text-center">Hapus</button>
            </form>
          </div>
        `
      }

      data.forEach((store) => {
        let s = {
          id: store.id,
          name: store.name,
          address: store.address,
          marker: L.marker([store.latitude, store.longitude], {draggable: true}).addTo(map),
        }

        allStores.push(s);

        s.marker.on('click', () => {
          newStoreMarker.remove();
        });

        s.marker.on('dragstart', () => {
          map.dragging.disable();
        });

        s.marker.on('drag', () => {
          s.marker.setPopupContent(popupContent(s));
          s.marker.openPopup();
        });

        s.marker.on('dragend', (e) => {
          map.dragging.enable(); // Re-enable map dragging
          const newLatLng = e.target.getLatLng();

          getAddress(document.getElementById(`address-${store.id}`), newLatLng.lat, newLatLng.lng);
          s.address = document.getElementById(`address-${store.id}`).textContent
      
          // Update the marker's popup content with new coordinates
          store.marker.setPopupContent(popupContent(store));
      
          // Reopen the popup
          store.marker.openPopup();
        });

        
      });

      allStores.forEach(store => {
        store.marker.bindPopup(popupContent(store));

        store.marker.on('dragstart', () => {
          map.dragging.disable(); // Disable map dragging during marker drag
          store.marker.closePopup(); // Close the popup before dragging starts
        });



        store.marker.on('dragend', (e) => {
          
        });

        

      })

    })
}

document.getElementById('locate-button').addEventListener('click', () => {
  myMarker.remove();
  map.locate({setView: true, maxZoom: 16});
});

map.on('locationfound', (e) => {
  myMarker.setLatLng(e.latlng).addTo(map);
})

function getAddress(textarea, lat, lng) {
  const url = `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`;

  fetch(url)
    .then(response => response.json())
    .then(data => {
      const address = data.display_name;
      textarea.textContent = address;
      return address;
    })
    .catch(error => {
      console.error('Error fetching the address:', error);
    });
}

map.on('click', (e) => {
  newStoreMarker.remove();
  newStoreMarker.setLatLng(e.latlng).addTo(map);

  newStoreLatitudeInput.value = e.latlng.lat;
  newStoreLongitudeInput.value = e.latlng.lng;

  getAddress(newStoreAddressInput, e.latlng.lat, e.latlng.lng);
})

newStoreMarker.on('move', (e) => {
  newStoreLatitudeInput.value = e.latlng.lat;
  newStoreLongitudeInput.value = e.latlng.lng;
})

newStoreMarker.on('dragend', (e) => {
  getAddress(newStoreAddressInput, e.latlng.lat, e.latlng.lng);
})

document.addEventListener('DOMContentLoaded', () => {
  initStores();
})