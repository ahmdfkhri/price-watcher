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

  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

  <title>Price Watcher</title>
</head>
<body>
  <a href="{{ route('store.create') }}" class="absolute top-4 right-4 p-4 bg-cyan-600 hover:bg-cyan-700 rounded-xl shadow-xl text-white underline underline-offset-2" style="z-index: 1000">
    Laman CRUD Toko
  </a>
  <button id="locate-button" class="absolute bottom-4 right-4 p-4 bg-cyan-600 hover:bg-cyan-700 rounded-full shadow-xl text-white" style="z-index: 1000">
    <svg fill="currentColor" class="h-6 w-6" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 297 297" xml:space="preserve">
      <path d="M148.5,0C66.653,0,0.067,66.616,0.067,148.499C0.067,230.383,66.653,297,148.5,297s148.433-66.617,148.433-148.501  C296.933,66.616,230.347,0,148.5,0z M158.597,276.411v-61.274c0-5.575-4.521-10.097-10.097-10.097s-10.097,4.521-10.097,10.097  v61.274c-62.68-4.908-112.845-55.102-117.747-117.814h61.207c5.575,0,10.097-4.521,10.097-10.097s-4.522-10.097-10.097-10.097  H20.656C25.558,75.69,75.723,25.497,138.403,20.589v61.274c0,5.575,4.521,10.097,10.097,10.097s10.097-4.521,10.097-10.097V20.589  c62.681,4.908,112.846,55.102,117.747,117.814h-61.207c-5.575,0-10.097,4.521-10.097,10.097s4.521,10.097,10.097,10.097h61.207  C271.441,221.31,221.276,271.503,158.597,276.411z"/>
    </svg>
  </button>
  <div class="absolute bottom-4 left-4 p-4 bg-white rounded-xl shadow-xl" style="z-index: 1000">
    <label for="radius-range">Radius: <span id="radius-text">1000</span> meter</label>
    <input id="radius-range" type="range" min="100" max="5000" step="100" value="1000" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
  </div>
  {{-- Search Form --}}
  <section class="absolute ml-8 left-96 w-1/3 top-4 h-32 p-4 bg-white rounded-xl shadow-xl overflow-y-auto " style="z-index: 3000">
    <div id="reader_1" class="w-full"></div>
    <label for="product_code_search" class="block mt-2 mb-2 text-sm font-medium text-gray-900">Product Code</label>
    <input type="text" id="product_code_search" name="product_code" class="mt-2 block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500" readonly>
    <label for="product_name" class="block mt-2 mb-2 text-sm font-medium text-gray-900">Product Name</label>
    <input type="text" id="product_name_search" name="product_name" 
        class="mt-2 block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500" 
        readonly>
  </section>
  {{-- List of stores --}}
  <section class="absolute right-4 top-24 h-[calc(100vh-12rem)] p-4 bg-white rounded-xl shadow-xl w-64 overflow-y-auto " style="z-index: 2000">
    <ul id="store-list" class="flex flex-col gap-4">
      
    </ul>
  </section>
  {{-- Contribute --}}
  <section class="absolute left-4 top-4 h-[calc(100vh-10rem)] p-4 bg-white rounded-xl shadow-xl w-96 overflow-y-auto " style="z-index: 2000">
    <h1>Tambah Transaksi</h1>
    <form action="{{ route('transaction.store') }}" method="post">
      @csrf
      <div id="reader" class="w-full"></div>
      <label for="product_code" class="block mt-2 mb-2 text-sm font-medium text-gray-900">Product Code</label>
      <input type="text" id="product_code" name="product_code" class="mt-2 block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500" readonly>

      <label for="product_name" class="block mt-2 mb-2 text-sm font-medium text-gray-900">Product Name</label>
      <input type="text" id="product_name" name="product_name" 
         class="mt-2 block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500" 
         readonly>

      <label for="stores" class="block mt-2 mb-2 text-sm font-medium text-gray-900 dark:text-white">Pilih Toko</label>
      <select id="stores" name="store_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        <option value="" disabled selected>Pilih Toko</option>
      </select>
      <label for="store_id" class="block mt-2 mb-2 text-sm font-medium text-gray-900 dark:text-white">Harga</label>
      <input type="text" id="price" name="price" class="mt-2 block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 text-xs focus:ring-blue-500 focus:border-blue-500" >
      <button type="submit" class="mt-2 text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-auto px-5 py-2 text-center">Submit</button>
    </form>
  </section>
  <div id="map" style="height: 100vh"></div>
  <script src="{{ asset('index.js') }}"></script>
</body>
</html>