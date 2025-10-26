import Alpine from 'alpinejs';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Common components
document.addEventListener('alpine:init', () => {
    // Mobile menu toggle
    Alpine.data('mobileMenu', () => ({
        open: false,
        
        toggle() {
            this.open = !this.open;
        }
    }));
    
    // Dropdown component
    Alpine.data('dropdown', () => ({
        open: false,
        
        toggle() {
            this.open = !this.open;
        },
        
        close() {
            this.open = false;
        }
    }));
    
    // Modal component
    Alpine.data('modal', () => ({
        open: false,
        
        show() {
            this.open = true;
            document.body.style.overflow = 'hidden';
        },
        
        hide() {
            this.open = false;
            document.body.style.overflow = 'auto';
        }
    }));
    
    // Search component
    Alpine.data('search', () => ({
        query: '',
        results: [],
        loading: false,
        
        async search() {
            if (this.query.length < 2) {
                this.results = [];
                return;
            }
            
            this.loading = true;
            
            try {
                const response = await fetch(`search.php?q=${encodeURIComponent(this.query)}`);
                const data = await response.json();
                this.results = data;
            } catch (error) {
                console.error('Search error:', error);
                this.results = [];
            } finally {
                this.loading = false;
            }
        },
        
        clear() {
            this.query = '';
            this.results = [];
        }
    }));
});

// Utility functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function truncateText(text, maxLength) {
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}

// Video player initialization
function initVideoPlayer() {
    const players = document.querySelectorAll('.video-player');
    players.forEach(player => {
        // Build dynamic quality map from data attributes (data-quality480, data-quality720, data-quality1080)
        const ds = player.dataset || {};
        const qualityMap = {};
        const qualityOptions = [];
        ['480', '720', '1080'].forEach(q => {
            const key = 'quality' + q;
            const url = ds[key] || '';
            if (url && typeof url === 'string' && url.length > 0) {
                const qNum = parseInt(q, 10);
                qualityOptions.push(qNum);
                qualityMap[qNum] = url;
            }
        });

        // Determine default quality: dataset.defaultQuality or highest available
        let defaultQuality = parseInt(ds.defaultQuality || '', 10);
        if (!qualityOptions.includes(defaultQuality)) {
            defaultQuality = qualityOptions.length ? Math.max(...qualityOptions) : 0;
        }

        // Initialize Plyr.io if available
        if (typeof Plyr !== 'undefined') {
            const plyr = new Plyr(player, {
                controls: [
                    'play-large',
                    'play',
                    'progress',
                    'current-time',
                    'duration',
                    'mute',
                    'volume',
                    'settings',
                    'pip',
                    'airplay',
                    'fullscreen'
                ],
                settings: ['captions', 'quality', 'speed', 'loop'],
                quality: {
                    default: defaultQuality || 720,
                    options: qualityOptions.length ? qualityOptions : [360, 480, 720, 1080],
                    forced: qualityOptions.length > 0,
                    onChange: function (quality) {
                        const url = qualityMap[quality];
                        if (url) {
                            const currentTime = plyr.currentTime;
                            plyr.pause();
                            plyr.source = {
                                type: 'video',
                                sources: [{ src: url, type: 'video/mp4' }]
                            };
                            plyr.once('loadeddata', function () {
                                try { plyr.currentTime = currentTime; } catch (e) {}
                                plyr.play();
                            });
                        }
                    }
                },
                speed: {
                    selected: 1,
                    options: [0.5, 0.75, 1, 1.25, 1.5, 1.75, 2]
                },
                tooltips: {
                    controls: true,
                    seek: true
                },
                captions: {
                    active: false,
                    update: false,
                    language: 'auto'
                },
                fullscreen: {
                    enabled: true,
                    fallback: true,
                    iosNative: true
                },
                storage: {
                    enabled: true,
                    key: 'plyr'
                },
                listeners: {
                    seek: null,
                    play: null,
                    pause: null,
                    restart: null,
                    rewind: null,
                    forward: null,
                    muted: null,
                    volume: null,
                    captions: null,
                    quality: null,
                    speed: null,
                    pip: null,
                    airplay: null
                },
                i18n: {
                    restart: 'Restart',
                    rewind: 'Rewind {seektime}s',
                    play: 'Play',
                    pause: 'Pause',
                    forward: 'Forward {seektime}s',
                    played: 'Played',
                    buffered: 'Buffered',
                    currentTime: 'Current time',
                    duration: 'Duration',
                    volume: 'Volume',
                    toggleMute: 'Toggle Mute',
                    toggleCaptions: 'Toggle Captions',
                    toggleFullscreen: 'Toggle Fullscreen',
                    frameTitle: '{title}',
                    captions: 'Captions',
                    settings: 'Settings',
                    pip: 'PiP',
                    menuBack: 'Back to previous menu',
                    speed: 'Speed',
                    normal: 'Normal',
                    quality: 'Quality',
                    loop: 'Loop',
                    start: 'Start',
                    end: 'End',
                    all: 'All',
                    reset: 'Reset',
                    disabled: 'Disabled',
                    enabled: 'Enabled',
                    advertisement: 'Ad',
                    qualityBadge: '{quality}'
                }
            });

            // Store player instance and quality map for global access
            player.plyr = plyr;
            player.qualityMap = qualityMap;

            // If default quality is defined and has URL, ensure the player uses it initially
            if (defaultQuality && qualityMap[defaultQuality]) {
                const currentSrcEl = player.querySelector('source');
                const currentSrc = currentSrcEl ? currentSrcEl.getAttribute('src') : '';
                if (!currentSrc || currentSrc !== qualityMap[defaultQuality]) {
                    const currentTime = plyr.currentTime || 0;
                    plyr.source = {
                        type: 'video',
                        sources: [{ src: qualityMap[defaultQuality], type: 'video/mp4' }]
                    };
                    plyr.once('loadeddata', function () {
                        try { plyr.currentTime = currentTime; } catch (e) {}
                    });
                }
            }
        }
    });
}

// Global helper: change quality programmatically (used by quality buttons)
window.setPlayerQuality = function (quality) {
    const players = document.querySelectorAll('.video-player');
    players.forEach(player => {
        if (player.plyr && player.qualityMap && player.qualityMap[quality]) {
            const plyr = player.plyr;
            const url = player.qualityMap[quality];
            const currentTime = plyr.currentTime;
            plyr.pause();
            plyr.source = {
                type: 'video',
                sources: [{ src: url, type: 'video/mp4' }]
            };
            plyr.once('loadeddata', function () {
                try { plyr.currentTime = currentTime; } catch (e) {}
                plyr.play();
            });
        }
    });
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    initVideoPlayer();
});

// Export functions for global use
window.formatDate = formatDate;
window.truncateText = truncateText;
window.initVideoPlayer = initVideoPlayer;