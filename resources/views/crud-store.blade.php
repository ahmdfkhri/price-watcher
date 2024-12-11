<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  @endif

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Price Watcher</title>
</head>
<body>
  <a href="/" class="absolute top-4 right-4 p-4 bg-cyan-600 hover:bg-cyan-700 rounded-xl shadow-xl text-white underline underline-offset-2" style="z-index: 1000">
    Laman Cari Produk
  </a>
  <div class="absolute top-4 left-4 p-4 bg-white rounded-xl" style="z-index: 5000">
    <form action="{{ route('store.store') }}" method="post">
      @csrf
      <div class="flex gap-2">
        <div>
          <label for="name-new" class="block mb-2 text-sm font-medium text-gray-900">Nama Toko</label>
          <input type="text" id="name-new" name="name" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="w-20">
          <label for="latitude-new" class="block mb-2 text-sm font-medium text-gray-900">Latitude</label>
          <input type="text" id="latitude-new" name="latitude" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="w-20">
          <label for="longitude-new" class="block mb-2 text-sm font-medium text-gray-900">Longitude</label>
          <input type="text" id="longitude-new" name="longitude" class="block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500">
        </div>
      </div>
      <div>
        <label for="address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Alamat</label>
        <textarea id="address-new" name="address" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Alamat toko..."></textarea>
      </div>
      <button type="submit" class="mt-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-auto px-5 py-2 text-center">Tambahkan</button>
    </form>
  </div>
  <button id="locate-button" class="absolute bottom-4 right-4 p-4 bg-cyan-600 hover:bg-cyan-700 rounded-full shadow-xl text-white" style="z-index: 1000">
    <svg fill="currentColor" class="h-6 w-6" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 297 297" xml:space="preserve">
      <path d="M148.5,0C66.653,0,0.067,66.616,0.067,148.499C0.067,230.383,66.653,297,148.5,297s148.433-66.617,148.433-148.501  C296.933,66.616,230.347,0,148.5,0z M158.597,276.411v-61.274c0-5.575-4.521-10.097-10.097-10.097s-10.097,4.521-10.097,10.097  v61.274c-62.68-4.908-112.845-55.102-117.747-117.814h61.207c5.575,0,10.097-4.521,10.097-10.097s-4.522-10.097-10.097-10.097  H20.656C25.558,75.69,75.723,25.497,138.403,20.589v61.274c0,5.575,4.521,10.097,10.097,10.097s10.097-4.521,10.097-10.097V20.589  c62.681,4.908,112.846,55.102,117.747,117.814h-61.207c-5.575,0-10.097,4.521-10.097,10.097s4.521,10.097,10.097,10.097h61.207  C271.441,221.31,221.276,271.503,158.597,276.411z"/>
    </svg>
  </button>
  <div id="map" style="height: 100vh"></div>
  <script src="{{ asset('store.js') }}"></script>
</body>
</html>