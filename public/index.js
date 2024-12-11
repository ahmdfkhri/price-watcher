const map = L.map('map').setView([1.17325, 108.97977], 16);

const myMarker = L.marker();
const myCircle = L.circle().setRadius(1000);

const radiusRange = document.getElementById('radius-range');
const radiusText = document.getElementById('radius-text');
const storeList = document.getElementById('store-list');

const allStores = [];

function initStores() {
  const url = "/store";
  
  fetch(url)
    .then(response => response.json())
    .then(data => {
      data.forEach((store) => {
        const popupContent = `
          <div class="p-4 bg-white rounded-lg shadow-md">
            <h3 class="text-lg font-bold text-gray-800">${store.name}</h3>
            <p class="text-sm text-gray-600">Address: ${store.address}</p>
            <p class="text-sm text-gray-500">ID: ${store.id}</p>
          </div>
        `;
        let s = {
          id: store.id,
          name: store.name,
          address: store.address,
          marker: L.marker([store.latitude, store.longitude]).addTo(map).bindPopup(popupContent),
        }

        allStores.push(s);
      })
    })
}

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
  maxZoom: 19,
  attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

document.getElementById('locate-button').addEventListener('click', () => {
  myMarker.remove();
  myCircle.remove();
  map.locate({setView: true, maxZoom: 16});
});

map.on('locationfound', (e) => {
  myMarker.setLatLng(e.latlng).addTo(map);
  myCircle.setLatLng(e.latlng).addTo(map);
})

radiusRange.addEventListener('input', function () {
  radiusText.textContent = this.value;
  myCircle.setRadius(this.value);

  if (myMarker.getLatLng()) {
    filterMarkersByRadius(myMarker.getLatLng(), this.value);
    fetchPrices(productCodeInput.value);
  }
});

function getAddress(lat, lng) {
  const url = `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`;

  fetch(url)
    .then(response => response.json())
    .then(data => {
      console.log(data);
      const address = data.display_name;
      document.getElementById('address').textContent = `Address: ${address}`;
    })
    .catch(error => {
      console.error('Error fetching the address:', error);
    });
}
function filterMarkersByRadius(center, radius) {
  const filteredStoreIds = []; // Store IDs within the radius

  allStores.forEach((store) => {
    const distance = map.distance(center, store.marker.getLatLng());

    if (distance <= radius) {
      if (!store.inRadius) {
        store.marker.addTo(map); // Add marker to map
        addStoreToList(store); // Update list
        store.inRadius = true;
      }
      filteredStoreIds.push(store.id); // Collect store ID
    } else {
      if (store.inRadius) {
        map.removeLayer(store.marker); // Remove marker
        removeStoreFromList(store.id); // Remove from list
        store.inRadius = false;
      }
    }
  });

  const productCode = document.getElementById('product_code_search').value;
  if (productCode) {
    fetchPrices(productCode); // Fetch prices for the filtered stores
  }
}


function fetchPrices(productCode) {
  const { lat, lng } = myMarker.getLatLng();
  const radius = myCircle.getRadius();

  fetch(`/store-prices?product_code=${productCode}&latitude=${lat}&longitude=${lng}&radius=${radius}`)
    .then(response => response.json())
    .then(data => {
      // Sort stores by price
      const sortedStores = data.sort((a, b) => a.price - b.price);
      updatePriceList(sortedStores); // Update UI with sorted stores
    })
    .catch(error => {
      console.error('Error fetching prices:', error);
    });
}


function updatePriceList(stores) {
  storeList.innerHTML = ''; // Clear the store list

  if (stores.length === 0) {
    storeList.innerHTML = '<li class="text-gray-500">No stores found in this radius.</li>';
    return;
  }

  stores.forEach(store => {
    const li = document.createElement('li');
    li.className = "border p-4 rounded-lg shadow";
    li.innerHTML = `
      <h3 class="font-bold text-gray-800">${store.name}</h3>
      <p class="text-sm text-gray-600">${store.address}</p>
      <p class="text-green-600 font-semibold">Price: Rp ${store.price}</p>
      <p class="text-xs text-gray-400">Updated: ${new Date(store.updated_at).toLocaleString()}</p>
    `;
    storeList.appendChild(li);
  });
}


function addStoreToSelect(storeSelect, store) {
  const option = document.createElement('option');
  option.id = `store-select-${store.id}`;
  option.value = store.id;
  option.textContent = store.name;
  storeSelect.appendChild(option);
}

function removeStoreFromSelect(storeId) {
  const option = document.getElementById(`store-select-${storeId}`);
  if (option) {
    option.remove();
  }
}

function addStoreToList(store) {
  const li = document.createElement('li');
  li.className = "border-2 border-gray-200 p-2 rounded-md";
  li.id = `store-${store.id}`;
  li.innerHTML = `
    <button class="text-left" onclick="flyTo(${store.id})">
      <h1 class="font-semibold">${store.name}</h1>
      <p class="text-sm text-gray-600">${store.address}</p>
    </button>
  `;
  storeList.appendChild(li);
}

function removeStoreFromList(storeId) {
  const li = document.getElementById(`store-${storeId}`);
  if (li) {
    li.remove();
  }
}

function flyTo(storeId) {
  const store = allStores.find(s => s.id === storeId);
  if (store) {
    map.flyTo(store.marker.getLatLng(), 16);
    store.marker.openPopup();
  }
}

document.getElementById('product_code_search').addEventListener('input', function () {
  const radius = radiusRange.value;
  const center = myMarker.getLatLng();
  filterMarkersByRadius(center, radius); // Re-filter stores
});


document.addEventListener('DOMContentLoaded', () => {

  const productCodeSearchInput = document.getElementById('product_code_search');
  const productNameSearchInput = document.getElementById('product_name_search');

  const productCodeInput = document.getElementById('product_code');
  const productNameInput = document.getElementById('product_name');

  function handleBarcodeScan(productCode, productCodeInput, productNameInput) {
    productCodeInput.value = productCode;

    // Fetch product details from the backend
    fetch(`/product/${productCode}`)
      .then(response => {
        if (!response.ok) throw new Error('Product not found');
        return response.json();
      })
      .then(data => {
        if (data.name) {
            productNameInput.value = data.name;
            productNameInput.readOnly = true; // Make name field read-only
        } else {
            productNameInput.value = ''; // Clear name input
            productNameInput.readOnly = false; // Allow user to input a new name
        }
      })
      .catch(() => {
        productNameInput.value = '';
        productNameInput.readOnly = false;
      });
  }

  function onScanSuccess(decodedText, decodedResult) {
    handleBarcodeScan(decodedText, productCodeInput, productNameInput);
  }

  function onScanSuccessSearch(decodedText, decodedResult) {
    handleBarcodeScan(decodedText, productCodeSearchInput, productNameSearchInput);
  }

  function onScanFailure(error) {
    console.warn(`Code scan error = ${error}`);
  }

  const html5QrcodeScanner = new Html5QrcodeScanner(
    "reader",
    { fps: 10, qrbox: { width: 250, height: 250 } },
    false
  );
  html5QrcodeScanner.render(onScanSuccess, onScanFailure);

  const html5QrcodeScannerSearch = new Html5QrcodeScanner(
    "reader_1",
    { fps: 10, qrbox: { width: 250, height: 250 } },
    false
  );
  html5QrcodeScannerSearch.render(onScanSuccessSearch, onScanFailure);

  // Initialize stores and apply radius filter
  initStores();
});
