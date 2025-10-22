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
                    default: 720,
                    options: [360, 480, 720, 1080],
                    forced: true,
                    onChange: function(quality) {
                        console.log('Quality changed to:', quality);
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
            
            // Store player instance for global access
            player.plyr = plyr;
        }
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    initVideoPlayer();
});

// Export functions for global use
window.formatDate = formatDate;
window.truncateText = truncateText;
window.initVideoPlayer = initVideoPlayer;