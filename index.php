<?php
require_once 'core/connection.php';
require_once 'core/functions.php';

$pageTitle = 'Beranda';
$pageDescription = 'Nonton anime streaming subtitle Indonesia terlengkap';

// Get featured anime
$featuredAnimes = getAllAnimes(10, 0, null, true);

// Get latest episodes
$latestEpisodes = getLatestEpisodes(12);

// Get popular anime
$popularAnimes = getPopularAnimes(10);

// Get ongoing anime
$ongoingAnimes = getAllAnimes(10, 0, 'Ongoing');

// Get new anime (recent)
$newAnimes = getAllAnimes(10, 0);
?>
<?php include 'templates/header.php'; ?>

<!-- Inline CSS fallback untuk memastikan rasio 2:3 & overlay (sesuaikan jika pakai Tailwind full build) -->
<style>
/* fallback aspect 2:3 (portrait) */
.aspect-2-3 {
  position: relative;
  width: 100%;
  padding-top: 150%; /* height = width * 3/2 => 150% (2:3) */
  overflow: hidden;
  border-radius: .5rem; /* sama seperti rounded pada Tailwind */
}

/* memastikan gambar mengisi kontainer */
.aspect-2-3 img,
.img-fill {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
  transform-origin: center center;
}

/* overlay container di atas gambar */
.anime-card-overlay {
  position: absolute;
  inset: 0;
  display: flex;
  align-items: flex-end;
  justify-content: flex-start;
  padding: 0.5rem;
  pointer-events: none; /* klik tetap ke link */
}

/* teks overlay default (untuk grup hover effect) */
.anime-card .anime-card-overlay .overlay-content,
.group .anime-card-overlay .overlay-content {
  pointer-events: none;
  opacity: 0;
  transform: translateY(6px);
  transition: opacity .25s ease, transform .25s ease;
}

/* bila pakai kelas group pada parent, tampilkan overlay saat hover */
.anime-card.group:hover .anime-card-overlay .overlay-content,
.group:hover .anime-card-overlay .overlay-content,
.group:hover .overlay-content {
  opacity: 1;
  transform: translateY(0);
}

/* beberapa styling kecil untuk judul di bawah gambar */
.card-title {
  margin-top: .5rem;
}
.card-title h3 {
  font-weight: 600;
  font-size: .875rem; /* text-sm */
  color: #111827; /* text-gray-900 */
  line-height: 1.1;
}
.card-subtitle {
  font-size: .75rem; /* text-xs */
  color: #6b7280; /* text-gray-500 */
}

/* small helper for the underline animation fallback */
.title-underline {
  display: block;
  height: 2px;
  background: linear-gradient(90deg, rgba(59,130,246,0) 0%, rgba(59,130,246,0.6) 50%, rgba(59,130,246,0) 100%);
  transform-origin: left;
  transform: scaleX(0);
  transition: transform .3s ease;
}
.group:hover .title-underline {
  transform: scaleX(1);
}

/* make card clickable without outline */
a.block { display: block; text-decoration: none; color: inherit; }

/* responsive tweak: ensure carousel text readable on small screens */
.carousel .container { max-width: 1100px; margin: 0 auto; }
@media (max-width: 640px) {
  .carousel .container { padding: 0 1rem; }
}
</style>

<!-- Hero Slider -->
<section class="relative">
    <div class="carousel relative w-full" x-data="{ currentSlide: 0, slides: [] }" x-init="slides = JSON.parse($el.dataset.slides); if (slides.length) { setInterval(() => { currentSlide = (currentSlide + 1) % slides.length }, 5000) }" data-slides='<?= json_encode(array_slice($featuredAnimes, 0, 5), JSON_UNESCAPED_SLASHES) ?>'>
        <div class="relative h-96 md:h-[500px] overflow-hidden">
            <template x-for="(slide, index) in slides" :key="index">
                <div class="absolute inset-0 transition-opacity duration-1000" 
                     :class="currentSlide === index ? 'opacity-100' : 'opacity-0'">
                    <img :src="(slide.poster && /^https?:\/\/.*/.test(slide.poster)) ? slide.poster : 'https://via.placeholder.com/1280x720?text=No+Image'"
                         :alt="slide.title"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                        <div class="container mx-auto">
                            <span class="inline-block px-3 py-1 bg-red-600 text-white text-sm rounded-full mb-4">
                                Featured
                            </span>
                            <h2 x-text="slide.title" class="text-3xl md:text-4xl font-bold mb-4"></h2>
                            <p x-text="slide.synopsis ? slide.synopsis.substring(0, 150) + '...' : ''" 
                               class="text-lg mb-6 max-w-2xl"></p>
                            <div class="flex flex-wrap gap-4 mb-6">
                                <span class="text-sm" x-text="`Type: ${slide.type}`"></span>
                                <span class="text-sm" x-text="`Status: ${slide.status}`"></span>
                                <span class="text-sm" x-text="`Year: ${slide.year}`"></span>
                            </div>
                            <a :href="`detail.php?slug=${slide.slug}`" 
                               class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Tonton Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- Slider Controls -->
        <button @click="currentSlide = (currentSlide - 1 + slides.length) % slides.length" 
                class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button @click="currentSlide = (currentSlide + 1) % slides.length" 
                class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white p-2 rounded-full transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        
        <!-- Slider Indicators -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
            <template x-for="(slide, index) in slides" :key="index">
                <button @click="currentSlide = index" 
                        class="w-3 h-3 rounded-full transition-colors duration-200"
                        :class="currentSlide === index ? 'bg-white' : 'bg-white/50'"></button>
            </template>
        </div>
    </div>
</section>

<!-- Latest Episodes -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Episode Terbaru</h2>
            <a href="anime-list.php" class="text-primary-600 hover:text-primary-700 font-medium">
                Lihat Semua →
            </a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
            <?php foreach ($latestEpisodes as $episode): ?>
                <div class="group bg-white rounded-xl overflow-hidden transition-all duration-300">
                    <a href="nonton.php?id=<?php echo $episode['id']; ?>" class="block">
                        <div class="relative aspect-2-3 bg-gray-200 overflow-hidden">
                            <img src="<?php echo getImageUrl($episode['poster'] ?? ''); ?>"
                                 alt="<?php echo htmlspecialchars($episode['anime_title']); ?>"
                                 class="img-fill transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>

                            <!-- bottom overlay: rating (kiri) + ep badge (kanan) -->
                            <div class="absolute bottom-2 left-2 right-2 flex items-center justify-between text-xs">
                                <div class="flex items-center text-white/90">
                                    <!-- star icon -->
                                    <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <?php echo number_format($episode['rating'] ?? 0, 1); ?>
                                </div>

                                <span class="px-2 py-1 rounded bg-blue-600/80 text-white text-[11px]">
                                    Ep <?php echo htmlspecialchars($episode['episode_number']); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Judul / info di bawah gambar -->
                        <h3 class="mt-3 px-1 text-sm font-semibold text-gray-900 group-hover:text-primary-600 transition-colors duration-300">
                            <?php echo htmlspecialchars($episode['anime_title']); ?>
                            <span class="title-underline mt-1"></span>
                        </h3>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- Popular Anime -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Anime Populer</h2>
            <a href="anime-list.php?sort=popular" class="text-primary-600 hover:text-primary-700 font-medium">
                Lihat Semua →
            </a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            <?php foreach ($popularAnimes as $anime): ?>
                <div class=" group bg-white rounded-xl overflow-hidden transition-all duration-300">
                    <a href="detail.php?slug=<?php echo $anime['slug']; ?>" class="block transition-transform duration-300 group-hover:-translate-y-1">
                        <div class="relative aspect-2-3 rounded overflow-hidden">
                            <img src="<?php echo getImageUrl($anime['poster'] ?? ''); ?>"
                                 alt="<?php echo htmlspecialchars($anime['title']); ?>"
                                 class="img-fill transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>

                            <div class="">
                                <div class="overlay-content text-white">
                                    <div class="flex items-center mt-2 text-xs">
                                        <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <?php echo number_format($anime['rating'] ?? 0, 1); ?>
                                    </div>
                                    <div class="mt-2 text-xs">
                                        <span class="px-2 py-1 bg-blue-600 text-white rounded">
                                            <?php echo htmlspecialchars($anime['episode_count']); ?> Episode
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Judul di bawah gambar -->
                        <div class="mt-2 card-title px-1">
                            <h3 class="truncate text-sm font-semibold text-gray-900 group-hover:text-primary-600 transition-colors duration-300">
                                <?php echo htmlspecialchars($anime['title']); ?>
                            </h3>
                            <span class="title-underline mt-1"></span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Ongoing Anime -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Sedang Tayang</h2>
            <a href="anime-list.php?status=Ongoing" class="text-primary-600 hover:text-primary-700 font-medium">
                Lihat Semua →
            </a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            <?php foreach ($ongoingAnimes as $anime): ?>
                <div class=" group bg-white rounded-xl overflow-hidden transition-all duration-300">
                    <a href="detail.php?slug=<?php echo $anime['slug']; ?>" class="block transition-transform duration-300 group-hover:-translate-y-1">
                        <div class="relative aspect-2-3 rounded overflow-hidden">
                            <img src="<?php echo getImageUrl($anime['poster'] ?? ''); ?>"
                                 alt="<?php echo htmlspecialchars($anime['title']); ?>"
                                 class="img-fill transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>

                            <div class="">
                                <div class="overlay-content text-white">
                                    <div class="mt-2 text-xs">
                                        <span class="px-2 py-1 bg-green-600 text-white rounded">
                                            Ongoing
                                        </span>
                                        <span class="ml-2 px-2 py-1 bg-blue-600 text-white rounded">
                                            <?php echo htmlspecialchars($anime['episode_count']); ?> Episode
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Judul di bawah gambar -->
                        <div class="mt-2 card-title px-1">
                            <h3 class="truncate text-sm font-semibold text-gray-900 group-hover:text-primary-600 transition-colors duration-300">
                                <?php echo htmlspecialchars($anime['title']); ?>
                            </h3>
                            <span class="title-underline mt-1"></span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- New Anime -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Anime Terbaru</h2>
            <a href="anime-list.php?sort=newest" class="text-primary-600 hover:text-primary-700 font-medium">
                Lihat Semua →
            </a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            <?php foreach ($newAnimes as $anime): ?>
                <div class=" group bg-white rounded-xl overflow-hidden transition-all duration-300">
                    <a href="detail.php?slug=<?php echo $anime['slug']; ?>" class="block transition-transform duration-300 group-hover:-translate-y-1">
                        <div class="relative aspect-2-3 rounded overflow-hidden">
                            <img src="<?php echo getImageUrl($anime['poster'] ?? ''); ?>"
                                 alt="<?php echo htmlspecialchars($anime['title']); ?>"
                                 class="img-fill transition-transform duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>

                            <div class="">
                                <div class="overlay-content text-white">
                                    <div class="mt-2 text-xs">
                                        <span class="px-2 py-1 bg-purple-600 text-white rounded">
                                            <?php echo htmlspecialchars($anime['type']); ?>
                                        </span>
                                        <span class="ml-2 px-2 py-1 bg-blue-600 text-white rounded">
                                            <?php echo htmlspecialchars($anime['episode_count']); ?> Episode
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Judul di bawah gambar -->
                        <div class="mt-2 card-title px-1">
                            <h3 class="truncate text-sm font-semibold text-gray-900 group-hover:text-primary-600 transition-colors duration-300">
                                <?php echo htmlspecialchars($anime['title']); ?>
                            </h3>
                            <span class="title-underline mt-1"></span>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Auto-slide script (Alpine handles auto-slide, fallback empty) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-slide handled by Alpine x-init within the carousel element
});
</script>

<?php include 'templates/footer.php'; ?>
