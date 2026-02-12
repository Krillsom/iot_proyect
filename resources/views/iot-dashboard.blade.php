<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('IoT Dashboard - Monitoreo en Tiempo Real') }}
            </h2>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                <span id="live-time">{{ now()->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Tarjetas de Estadísticas --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                {{-- Total Dispositivos --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Dispositivos</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_devices'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Total Beacons --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Beacons</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_beacons'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Total Gateways --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Gateways</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_gateways'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Lecturas Última Hora --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Lecturas (1h)</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['readings_last_hour'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dispositivos por Gateway --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Dispositivos por Gateway</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($devicesByGateway as $gateway)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Gateway</p>
                            <p class="font-mono text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $gateway->gateway_mac }}</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">{{ $gateway->device_count }} <span class="text-sm font-normal text-gray-600 dark:text-gray-400">dispositivos</span></p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            
            {{-- Tabla de Triangulación (G1 + G2 RSSI) --}}
            <div class="mt-6 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg" x-data="triangulationTable()">
                <div class="p-6">
                    {{-- Banner de error --}}
                    <div x-show="errorMessage" x-cloak
                         class="mb-4 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm text-red-700 dark:text-red-300" x-text="errorMessage"></span>
                        </div>
                        <button @click="errorMessage = null" class="text-red-400 hover:text-red-600 dark:hover:text-red-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Dispositivos (<span x-text="filteredDevices.length"></span> de <span x-text="devices.length"></span> beacons)
                        </h3>
                        <div class="flex gap-3">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full" 
                                  :class="g1Active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'">
                                G1 <span x-text="g1Active ? 'Activo' : 'Inactivo'"></span>
                            </span>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full"
                                  :class="g2Active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'">
                                G2 <span x-text="g2Active ? 'Activo' : 'Inactivo'"></span>
                            </span>
                        </div>
                    </div>

                    {{-- Filtros --}}
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar por MAC</label>
                            <input type="text" x-model="searchTerm" 
                                   placeholder="Ej: C30000217BAC"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filtrar por Gateway</label>
                            <select x-model="gatewayFilter" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="all">Todos</option>
                                <option value="g1">Solo G1</option>
                                <option value="g2">Solo G2</option>
                                <option value="both">Ambos gateways</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Items por página</label>
                            <select x-model.number="perPage" @change="currentPage = 1"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        MAC Address
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-blue-50 dark:bg-blue-900/20">
                                        G1 RSSI
                                    </th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-purple-50 dark:bg-purple-900/20">
                                        G2 RSSI
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Última señal
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="device in paginatedDevices" :key="device.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer" @click="openDeviceHistory(device)">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100" x-text="device.mac_address"></td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center bg-blue-50 dark:bg-blue-900/20">
                                            <template x-if="device.g1_rssi !== null">
                                                <div>
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                          :class="getRssiColor(device.g1_rssi)"
                                                          x-text="device.g1_rssi + ' dBm'">
                                                    </span>
                                                    <div class="text-xs text-gray-500 mt-1" x-text="device.g1_last_seen_human"></div>
                                                </div>
                                            </template>
                                            <template x-if="device.g1_rssi === null">
                                                <span class="text-gray-400 text-sm">-</span>
                                            </template>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center bg-purple-50 dark:bg-purple-900/20">
                                            <template x-if="device.g2_rssi !== null">
                                                <div>
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                          :class="getRssiColor(device.g2_rssi)"
                                                          x-text="device.g2_rssi + ' dBm'">
                                                    </span>
                                                    <div class="text-xs text-gray-500 mt-1" x-text="device.g2_last_seen_human"></div>
                                                </div>
                                            </template>
                                            <template x-if="device.g2_rssi === null">
                                                <span class="text-gray-400 text-sm">-</span>
                                            </template>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400" x-text="device.last_seen_human"></td>
                                    </tr>
                                </template>
                                <template x-if="filteredDevices.length === 0">
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-center text-sm text-gray-500">
                                            <span x-show="devices.length === 0">No hay dispositivos detectados en las últimas 24 horas</span>
                                            <span x-show="devices.length > 0">No se encontraron dispositivos con los filtros aplicados</span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación truncada --}}
                    <div class="mt-4 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-4" x-show="filteredDevices.length > 0">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            Mostrando <span class="font-medium" x-text="startIndex + 1"></span> a 
                            <span class="font-medium" x-text="Math.min(endIndex, filteredDevices.length)"></span> de 
                            <span class="font-medium" x-text="filteredDevices.length"></span> resultados
                        </div>
                        <div class="flex gap-1">
                            <button @click="currentPage--" :disabled="currentPage === 1"
                                    :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300">
                                Anterior
                            </button>
                            <template x-for="page in visiblePages" :key="'page-' + page">
                                <template x-if="page === '...'">
                                    <span class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">...</span>
                                </template>
                                <template x-if="page !== '...'">
                                    <button @click="currentPage = page"
                                            :class="currentPage === page ? 'bg-blue-500 text-white border-blue-500' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'"
                                            class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium"
                                            x-text="page">
                                    </button>
                                </template>
                            </template>
                            <button @click="currentPage++" :disabled="currentPage === totalPages"
                                    :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300">
                                Siguiente
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Modal de Historial de Lecturas --}}
                <div x-show="showModal" 
                     x-cloak
                     @keydown.escape.window="showModal = false"
                     class="fixed inset-0 z-50 overflow-y-auto" 
                     role="dialog"
                     aria-modal="true"
                     aria-labelledby="modal-title"
                     style="display: none;">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        {{-- Overlay --}}
                        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-80" 
                             @click="showModal = false"
                             aria-hidden="true"></div>

                        {{-- Modal content --}}
                        <div class="inline-block w-full max-w-4xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-lg"
                             x-ref="modalContent">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 id="modal-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        Historial de Lecturas - Última Hora
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-show="selectedDevice">
                                        Dispositivo: <span class="font-mono font-semibold" x-text="selectedDevice?.mac_address"></span>
                                    </p>
                                </div>
                                <button @click="showModal = false" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300" aria-label="Cerrar modal">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            {{-- Loading --}}
                            <div x-show="loadingHistory" class="text-center py-8">
                                <svg class="animate-spin h-8 w-8 text-blue-500 mx-auto" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <p class="text-gray-500 mt-2">Cargando historial...</p>
                            </div>

                            {{-- Error en historial --}}
                            <div x-show="historyError" x-cloak class="mb-4 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg">
                                <p class="text-sm text-red-700 dark:text-red-300" x-text="historyError"></p>
                            </div>

                            {{-- Tabla de lecturas --}}
                            <div x-show="!loadingHistory && !historyError" class="overflow-x-auto max-h-96">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Timestamp</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Gateway</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Topic</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase" style="min-width: 400px;">Datos del Tópico</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <template x-for="reading in deviceReadings" :key="reading.id">
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    <div x-text="reading.timestamp"></div>
                                                    <div class="text-xs text-gray-500" x-text="reading.timestamp_human"></div>
                                                </td>
                                                <td class="px-3 py-2 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100" x-text="reading.gateway_mac"></td>
                                                <td class="px-3 py-2 whitespace-nowrap text-xs text-gray-600 dark:text-gray-400 font-mono" x-text="reading.topic"></td>
                                                <td class="px-3 py-2 text-sm">
                                                    <div class="bg-gray-50 dark:bg-gray-900 p-2 rounded border border-gray-200 dark:border-gray-600 font-mono text-xs text-gray-900 dark:text-gray-200 overflow-x-auto max-w-md">
                                                        <pre x-text="JSON.stringify(reading.specific_data, null, 2)" class="whitespace-pre-wrap break-words"></pre>
                                                    </div>
                                                    <details class="mt-2">
                                                        <summary class="text-xs text-blue-600 dark:text-blue-400 cursor-pointer hover:text-blue-800 dark:hover:text-blue-300">Ver raw_data</summary>
                                                        <div class="bg-gray-50 dark:bg-gray-900 p-2 rounded border border-gray-200 dark:border-gray-600 font-mono text-xs text-gray-900 dark:text-gray-200 mt-1 overflow-x-auto max-w-md">
                                                            <pre x-text="typeof reading.raw_data === 'object' ? JSON.stringify(reading.raw_data, null, 2) : reading.raw_data" class="whitespace-pre-wrap break-words"></pre>
                                                        </div>
                                                    </details>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="deviceReadings.length === 0 && !loadingHistory">
                                            <tr>
                                                <td colspan="4" class="px-3 py-8 text-center text-sm text-gray-500">
                                                    No hay lecturas en la última hora para este dispositivo
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            {{-- Total de lecturas --}}
                            <div x-show="!loadingHistory && deviceReadings.length > 0" class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                Total de lecturas: <span class="font-semibold" x-text="deviceReadings.length"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Lecturas Recientes --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Lecturas Recientes</h3>
                        <div class="overflow-auto max-h-96">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Dispositivo</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">RSSI</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($recentReadings as $reading)
                                    <tr>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100">
                                            {{ substr($reading->device->mac_address, -8) }}
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm">
                                            @php
                                                $rssi = $reading->specific_data['rssi'] ?? null;
                                            @endphp
                                            @if($rssi)
                                                <span class="font-semibold 
                                                    {{ $rssi > -60 ? 'text-green-600' : ($rssi > -70 ? 'text-yellow-600' : 'text-red-600') }}">
                                                    {{ $rssi }} dBm
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $reading->data_timestamp->format('H:i:s') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            

        </div>
    </div>
</x-app-layout>
