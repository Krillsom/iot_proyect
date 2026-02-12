<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">
                    Monitoreo en Tiempo Real
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Panel de control IoT — datos actualizados cada 5 segundos</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 bg-white/50 dark:bg-gray-800/50 px-3 py-1.5 rounded-lg border border-gray-200/50 dark:border-gray-700/50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="live-time" class="font-mono text-xs">{{ now()->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Tarjetas de Estadísticas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                {{-- Total Dispositivos --}}
                <div class="stat-card stat-card-blue card p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Dispositivos</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $stats['total_devices'] }}</p>
                        </div>
                        <div class="w-11 h-11 bg-gradient-to-br from-indigo-500 to-cyan-400 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-gray-500 dark:text-gray-400">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-indigo-400 mr-1.5"></span>
                        Registrados en el sistema
                    </div>
                </div>

                {{-- Total Beacons --}}
                <div class="stat-card stat-card-green card p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Beacons</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $stats['total_beacons'] }}</p>
                        </div>
                        <div class="w-11 h-11 bg-gradient-to-br from-emerald-500 to-green-400 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-gray-500 dark:text-gray-400">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5"></span>
                        Señalizadores activos
                    </div>
                </div>

                {{-- Total Gateways --}}
                <div class="stat-card stat-card-purple card p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Gateways</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $stats['total_gateways'] }}</p>
                        </div>
                        <div class="w-11 h-11 bg-gradient-to-br from-violet-500 to-purple-400 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/20">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-gray-500 dark:text-gray-400">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-violet-400 mr-1.5"></span>
                        Puntos de acceso
                    </div>
                </div>

                {{-- Lecturas Última Hora --}}
                <div class="stat-card stat-card-amber card p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Lecturas (1h)</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mt-2">{{ $stats['readings_last_hour'] }}</p>
                        </div>
                        <div class="w-11 h-11 bg-gradient-to-br from-amber-500 to-yellow-400 rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center text-xs text-gray-500 dark:text-gray-400">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5"></span>
                        Última hora de actividad
                    </div>
                </div>
            </div>

            {{-- Dispositivos por Gateway --}}
            <div class="card mb-8">
                <div class="card-header flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Dispositivos por Gateway</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($devicesByGateway as $gateway)
                        <div class="relative group bg-gradient-to-br from-slate-50 to-gray-50 dark:from-gray-800 dark:to-gray-800/50 rounded-xl p-5 border border-gray-200/60 dark:border-gray-700/50 hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-200">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gateway</span>
                                <span class="gateway-dot gateway-dot-active"></span>
                            </div>
                            <p class="font-mono text-sm font-semibold text-gray-900 dark:text-gray-100 tracking-wide">{{ $gateway->gateway_mac }}</p>
                            <div class="mt-3 flex items-baseline gap-1">
                                <span class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-cyan-500 bg-clip-text text-transparent">{{ $gateway->device_count }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">dispositivos</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            
            {{-- Tabla de Triangulación (G1 + G2 RSSI) --}}
            <div class="card" x-data="triangulationTable()">
                <div class="card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider">
                            Triangulación de Señal
                        </h3>
                        <span class="text-xs text-gray-400 dark:text-gray-500 font-normal normal-case"
                              x-text="'(' + filteredDevices.length + ' de ' + devices.length + ' beacons)'"></span>
                    </div>
                    <div class="flex gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-full border"
                              :class="g1Active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-800' : 'bg-gray-50 text-gray-500 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700'">
                            <span class="gateway-dot" :class="g1Active ? 'gateway-dot-active' : 'gateway-dot-inactive'"></span>
                            G1
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-full border"
                              :class="g2Active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-800' : 'bg-gray-50 text-gray-500 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700'">
                            <span class="gateway-dot" :class="g2Active ? 'gateway-dot-active' : 'gateway-dot-inactive'"></span>
                            G2
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Banner de error --}}
                    <div x-show="errorMessage" x-cloak
                         class="mb-5 p-3.5 bg-red-50 dark:bg-red-900/20 border border-red-200/60 dark:border-red-800/40 rounded-xl flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-sm text-red-700 dark:text-red-300" x-text="errorMessage"></span>
                        </div>
                        <button @click="errorMessage = null" class="text-red-400 hover:text-red-600 dark:hover:text-red-200 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Filtros --}}
                    <div class="mb-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Buscar MAC</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" x-model="searchTerm" 
                                       placeholder="Ej: C30000217BAC"
                                       class="w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Gateway</label>
                            <select x-model="gatewayFilter" 
                                    class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors">
                                <option value="all">Todos los gateways</option>
                                <option value="g1">Solo G1</option>
                                <option value="g2">Solo G2</option>
                                <option value="both">Ambos gateways</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1.5 uppercase tracking-wider">Por página</label>
                            <select x-model.number="perPage" @change="currentPage = 1"
                                    class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm text-gray-900 dark:text-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-colors">
                                <option value="10">10 items</option>
                                <option value="25">25 items</option>
                                <option value="50">50 items</option>
                                <option value="100">100 items</option>
                            </select>
                        </div>
                    </div>

                    <div class="overflow-x-auto custom-scrollbar rounded-xl border border-gray-200/60 dark:border-gray-700/50">
                        <table class="min-w-full divide-y divide-gray-200/60 dark:divide-gray-700/50">
                            <thead>
                                <tr class="bg-gray-50/80 dark:bg-gray-800/80">
                                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        MAC Address
                                    </th>
                                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider bg-indigo-50/50 dark:bg-indigo-900/10">
                                        G1 RSSI
                                    </th>
                                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-violet-600 dark:text-violet-400 uppercase tracking-wider bg-violet-50/50 dark:bg-violet-900/10">
                                        G2 RSSI
                                    </th>
                                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Última señal
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                <template x-for="device in paginatedDevices" :key="device.id">
                                    <tr class="table-row-hover cursor-pointer group" @click="openDeviceHistory(device)">
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <div class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></div>
                                                <span class="text-sm font-mono font-medium text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" x-text="device.mac_address"></span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-center bg-indigo-50/30 dark:bg-indigo-900/5">
                                            <template x-if="device.g1_rssi !== null">
                                                <div>
                                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-lg"
                                                          :class="getRssiColor(device.g1_rssi)"
                                                          x-text="device.g1_rssi + ' dBm'">
                                                    </span>
                                                    <div class="text-[10px] text-gray-400 mt-1" x-text="device.g1_last_seen_human"></div>
                                                </div>
                                            </template>
                                            <template x-if="device.g1_rssi === null">
                                                <span class="text-gray-300 dark:text-gray-600 text-sm">—</span>
                                            </template>
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-center bg-violet-50/30 dark:bg-violet-900/5">
                                            <template x-if="device.g2_rssi !== null">
                                                <div>
                                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-lg"
                                                          :class="getRssiColor(device.g2_rssi)"
                                                          x-text="device.g2_rssi + ' dBm'">
                                                    </span>
                                                    <div class="text-[10px] text-gray-400 mt-1" x-text="device.g2_last_seen_human"></div>
                                                </div>
                                            </template>
                                            <template x-if="device.g2_rssi === null">
                                                <span class="text-gray-300 dark:text-gray-600 text-sm">—</span>
                                            </template>
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400" x-text="device.last_seen_human"></td>
                                    </tr>
                                </template>
                                <template x-if="filteredDevices.length === 0">
                                    <tr>
                                        <td colspan="4" class="px-5 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <p class="text-sm text-gray-500 dark:text-gray-400" x-show="devices.length === 0">No hay dispositivos detectados en las últimas 24 horas</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400" x-show="devices.length > 0">No se encontraron dispositivos con los filtros aplicados</p>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación truncada --}}
                    <div class="mt-5 flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-gray-100 dark:border-gray-800" x-show="filteredDevices.length > 0">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            <span class="font-medium" x-text="startIndex + 1"></span>–<span class="font-medium" x-text="Math.min(endIndex, filteredDevices.length)"></span> de 
                            <span class="font-medium" x-text="filteredDevices.length"></span> resultados
                        </div>
                        <div class="flex gap-1">
                            <button @click="currentPage--" :disabled="currentPage === 1"
                                    :class="currentPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    class="px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-medium text-gray-600 dark:text-gray-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <template x-for="page in visiblePages" :key="'page-' + page">
                                <template x-if="page === '...'">
                                    <span class="px-2.5 py-1.5 text-xs text-gray-400 dark:text-gray-500">...</span>
                                </template>
                                <template x-if="page !== '...'">
                                    <button @click="currentPage = page"
                                            :class="currentPage === page ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 border-gray-200 dark:border-gray-700'"
                                            class="px-3 py-1.5 border rounded-lg text-xs font-medium transition-all duration-200"
                                            x-text="page">
                                    </button>
                                </template>
                            </template>
                            <button @click="currentPage++" :disabled="currentPage === totalPages"
                                    :class="currentPage === totalPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-gray-100 dark:hover:bg-gray-700'"
                                    class="px-3 py-1.5 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-medium text-gray-600 dark:text-gray-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Modal de Historial de Lecturas (teleported to body to escape card stacking context) --}}
                <template x-teleport="body">
                    <div x-show="showModal" 
                         x-cloak
                         @keydown.escape.window="showModal = false"
                         class="fixed inset-0 z-50 overflow-y-auto" 
                         style="display: none;">
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                            {{-- Overlay --}}
                            <div class="fixed inset-0 bg-gray-900/60 dark:bg-black/70 transition-opacity" 
                                 @click="showModal = false"></div>

                            {{-- Modal content --}}
                            <div class="relative inline-block w-full max-w-4xl p-6 my-8 text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl border border-gray-200/50 dark:border-gray-700/50"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95">
                                
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            Historial de Lecturas - Última Hora
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" x-show="selectedDevice">
                                            Dispositivo: <span class="font-mono font-semibold text-indigo-600 dark:text-indigo-400" x-text="selectedDevice?.mac_address"></span>
                                        </p>
                                    </div>
                                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>

                                {{-- Loading --}}
                                <div x-show="loadingHistory" class="text-center py-8">
                                    <div class="w-10 h-10 mx-auto mb-3 border-3 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Cargando historial...</p>
                                </div>

                                {{-- Error en historial --}}
                                <div x-show="historyError" x-cloak class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/40 rounded-xl">
                                    <p class="text-sm text-red-700 dark:text-red-300" x-text="historyError"></p>
                                </div>

                                {{-- Tabla de lecturas --}}
                                <div x-show="!loadingHistory && !historyError" class="overflow-x-auto max-h-96 custom-scrollbar rounded-xl border border-gray-200/60 dark:border-gray-700/50">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-800/80 sticky top-0">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Timestamp</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Gateway</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Topic</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase" style="min-width: 400px;">Datos del Tópico</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                            <template x-for="reading in deviceReadings" :key="reading.id">
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                        <div x-text="reading.timestamp"></div>
                                                        <div class="text-xs text-gray-400" x-text="reading.timestamp_human"></div>
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-gray-600 dark:text-gray-300" x-text="reading.gateway_mac"></td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400 font-mono" x-text="reading.topic"></td>
                                                    <td class="px-4 py-3 text-sm">
                                                        <div class="bg-gray-50 dark:bg-gray-900/50 p-2.5 rounded-lg border border-gray-200/60 dark:border-gray-700/50 font-mono text-xs text-gray-700 dark:text-gray-300 overflow-x-auto max-w-md">
                                                            <pre x-text="JSON.stringify(reading.specific_data, null, 2)" class="whitespace-pre-wrap break-words"></pre>
                                                        </div>
                                                        <details class="mt-2">
                                                            <summary class="text-xs text-indigo-600 dark:text-indigo-400 cursor-pointer hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">Ver raw_data</summary>
                                                            <div class="bg-gray-50 dark:bg-gray-900/50 p-2.5 rounded-lg border border-gray-200/60 dark:border-gray-700/50 font-mono text-xs text-gray-700 dark:text-gray-300 mt-1.5 overflow-x-auto max-w-md">
                                                                <pre x-text="typeof reading.raw_data === 'object' ? JSON.stringify(reading.raw_data, null, 2) : reading.raw_data" class="whitespace-pre-wrap break-words"></pre>
                                                            </div>
                                                        </details>
                                                    </td>
                                                </tr>
                                            </template>
                                            <template x-if="deviceReadings.length === 0 && !loadingHistory">
                                                <tr>
                                                    <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                        No hay lecturas en la última hora para este dispositivo
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Total de lecturas --}}
                                <div x-show="!loadingHistory && deviceReadings.length > 0" class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                                    Total de lecturas: <span class="font-semibold text-gray-900 dark:text-gray-100" x-text="deviceReadings.length"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Bottom Section --}}
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Lecturas Recientes --}}
                <div class="card">
                    <div class="card-header flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Lecturas Recientes</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="overflow-auto max-h-96 custom-scrollbar">
                            <table class="min-w-full divide-y divide-gray-200/60 dark:divide-gray-700/50">
                                <thead class="bg-gray-50/80 dark:bg-gray-800/80 sticky top-0">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dispositivo</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">RSSI</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hora</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @foreach($recentReadings as $reading)
                                    <tr class="table-row-hover">
                                        <td class="px-5 py-3 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <div class="w-1.5 h-1.5 rounded-full bg-indigo-400"></div>
                                                <span class="text-sm font-mono font-medium text-gray-900 dark:text-gray-100">{{ substr($reading->device->mac_address, -8) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 whitespace-nowrap text-sm">
                                            @php
                                                $rssi = $reading->specific_data['rssi'] ?? null;
                                            @endphp
                                            @if($rssi)
                                                <span class="inline-flex px-2.5 py-0.5 rounded-lg text-xs font-semibold
                                                    {{ $rssi > -60 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : ($rssi > -70 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300') }}">
                                                    {{ $rssi }} dBm
                                                </span>
                                            @else
                                                <span class="text-gray-300 dark:text-gray-600">—</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400 font-mono">
                                            {{ $reading->data_timestamp->format('H:i:s') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- System Info Panel --}}
                <div class="card">
                    <div class="card-header flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wider">Información del Sistema</h3>
                    </div>
                    <div class="card-body space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700/50">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Broker MQTT</p>
                                <p class="text-sm font-mono font-medium text-gray-900 dark:text-gray-100">iot.surbiz.net</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">Puerto 1883</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700/50">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Intervalo de Poll</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">5 segundos</p>
                                <p class="text-[11px] text-gray-400 mt-0.5">Auto-pausa en background</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4 border border-gray-100 dark:border-gray-700/50">
                            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Tópicos MQTT</p>
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                                    <code class="text-xs font-mono text-gray-700 dark:text-gray-300">/sur/g1/status</code>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="w-1.5 h-1.5 rounded-full bg-violet-400"></span>
                                    <code class="text-xs font-mono text-gray-700 dark:text-gray-300">/sur/g2/status</code>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-indigo-50 to-cyan-50 dark:from-indigo-900/10 dark:to-cyan-900/10 rounded-xl p-4 border border-indigo-100/50 dark:border-indigo-800/30">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                <p class="text-xs font-semibold text-indigo-700 dark:text-indigo-300">Tip</p>
                            </div>
                            <p class="text-xs text-indigo-600/80 dark:text-indigo-400/80">Haz clic en cualquier fila de la tabla de triangulación para ver el historial detallado de lecturas del dispositivo.</p>
                        </div>
                    </div>
                </div>
            </div>

            

        </div>
    </div>
</x-app-layout>
