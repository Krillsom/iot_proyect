// Alpine.js component para tabla de triangulación
document.addEventListener('alpine:init', () => {
    // Detectar base path automáticamente (para subdirectorios como /demo/)
    const getBasePath = () => {
        const path = window.location.pathname;
        // Buscar si estamos en un subdirectorio (ej: /demo/dashboard -> /demo)
        const match = path.match(/^(\/[^\/]+)\/(?:dashboard|api|login|register)/);
        return match ? match[1] : '';
    };
    const basePath = getBasePath();

    Alpine.data('triangulationTable', () => ({
        devices: [],
        g1Active: false,
        g2Active: false,
        loading: true,
        
        // Estado de error (#4)
        errorMessage: null,
        historyError: null,
        
        // Filtros
        searchTerm: '',
        gatewayFilter: 'all',
        
        // Paginación
        currentPage: 1,
        perPage: 10,

        // Modal de historial
        showModal: false,
        selectedDevice: null,
        deviceReadings: [],
        loadingHistory: false,

        // Polling con visibility API (#6)
        _pollingInterval: null,
        _isTabVisible: true,

        init() {
            this.fetchData();
            this._startPolling();
            
            // Pausar/reanudar polling según visibilidad de la pestaña (#6)
            document.addEventListener('visibilitychange', () => {
                this._isTabVisible = !document.hidden;
                if (this._isTabVisible) {
                    this.fetchData(); // Refrescar datos al volver
                    this._startPolling();
                } else {
                    this._stopPolling();
                }
            });
        },

        _startPolling() {
            this._stopPolling();
            this._pollingInterval = setInterval(() => {
                if (this._isTabVisible) {
                    this.fetchData();
                }
            }, 5000); // 5 segundos (más razonable que 3)
        },

        _stopPolling() {
            if (this._pollingInterval) {
                clearInterval(this._pollingInterval);
                this._pollingInterval = null;
            }
        },

        destroy() {
            this._stopPolling();
        },

        // Dispositivos filtrados
        get filteredDevices() {
            let filtered = this.devices;
            
            if (this.searchTerm) {
                filtered = filtered.filter(d => 
                    d.mac_address.toLowerCase().includes(this.searchTerm.toLowerCase())
                );
            }
            
            if (this.gatewayFilter === 'g1') {
                filtered = filtered.filter(d => d.g1_rssi !== null);
            } else if (this.gatewayFilter === 'g2') {
                filtered = filtered.filter(d => d.g2_rssi !== null);
            } else if (this.gatewayFilter === 'both') {
                filtered = filtered.filter(d => d.g1_rssi !== null && d.g2_rssi !== null);
            }
            
            return filtered;
        },
        
        get totalPages() {
            return Math.ceil(this.filteredDevices.length / this.perPage);
        },
        
        get startIndex() {
            return (this.currentPage - 1) * this.perPage;
        },
        
        get endIndex() {
            return this.startIndex + this.perPage;
        },
        
        get paginatedDevices() {
            return this.filteredDevices.slice(this.startIndex, this.endIndex);
        },

        // Paginación truncada (#5): genera [1, 2, '...', 8, 9, 10] etc.
        get visiblePages() {
            const total = this.totalPages;
            const current = this.currentPage;
            
            if (total <= 7) {
                return Array.from({ length: total }, (_, i) => i + 1);
            }
            
            const pages = [];
            pages.push(1);
            
            if (current > 3) {
                pages.push('...');
            }
            
            const start = Math.max(2, current - 1);
            const end = Math.min(total - 1, current + 1);
            
            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            
            if (current < total - 2) {
                pages.push('...');
            }
            
            if (total > 1) {
                pages.push(total);
            }
            
            return pages;
        },

        async fetchData() {
            try {
                const response = await fetch(`${basePath}/api/dashboard/triangulation`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`Error del servidor (${response.status})`);
                }
                
                const data = await response.json();
                
                this.devices = data.devices;
                this.g1Active = data.g1_active;
                this.g2Active = data.g2_active;
                this.errorMessage = null; // Limpiar error previo si la petición fue exitosa
                
                if (this.currentPage > this.totalPages && this.totalPages > 0) {
                    this.currentPage = this.totalPages;
                }
            } catch (error) {
                console.error('Error fetching triangulation data:', error);
                this.errorMessage = `No se pudieron cargar los datos: ${error.message}`;
            } finally {
                this.loading = false;
            }
        },

        async openDeviceHistory(device) {
            this.selectedDevice = device;
            this.showModal = true;
            this.loadingHistory = true;
            this.deviceReadings = [];
            this.historyError = null;

            try {
                const response = await fetch(`${basePath}/api/dashboard/device/${device.id}/readings`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(`Error del servidor (${response.status})`);
                }

                const data = await response.json();
                this.deviceReadings = data.readings;
            } catch (error) {
                console.error('Error fetching device readings:', error);
                this.historyError = `No se pudo cargar el historial: ${error.message}`;
            } finally {
                this.loadingHistory = false;
            }
        },

        getRssiColor(rssi) {
            if (rssi > -60) return 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300';
            if (rssi > -80) return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300';
            return 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300';
        }
    }))
});

// Actualizar reloj
setInterval(() => {
    const liveTimeElement = document.getElementById('live-time');
    if (liveTimeElement) {
        const now = new Date();
        liveTimeElement.textContent = now.toLocaleString('es-ES');
    }
}, 1000);
