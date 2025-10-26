<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">
            <?php echo isset($episode) ? 'Edit Episode' : 'Tambah Episode'; ?>
        </h3>
        <?php if (isset($anime)): ?>
            <p class="text-sm text-gray-500 mt-1">Anime: <?php echo $anime['title']; ?></p>
        <?php endif; ?>
    </div>
    
    <form method="POST" enctype="multipart/form-data" class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Anime Selection -->
                <div>
                    <label for="anime_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Anime <span class="text-red-500">*</span>
                    </label>
                    <select id="anime_id" name="anime_id" required class="input-field">
                        <option value="">Pilih Anime</option>
                        <?php
                        $animes = getAllAnimes();
                        foreach ($animes as $a):
                            $selected = (isset($episode['anime_id']) && $episode['anime_id'] == $a['id']) || 
                                       (isset($anime['id']) && $anime['id'] == $a['id']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $a['id']; ?>" <?php echo $selected; ?>><?php echo $a['title']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Episode Number -->
                <div>
                    <label for="episode_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Episode <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="episode_number" name="episode_number" min="1" required
                           value="<?php echo $episode['episode_number'] ?? ''; ?>"
                           class="input-field" placeholder="Masukkan nomor episode">
                </div>
                
                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Episode <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" required
                           value="<?php echo $episode['title'] ?? ''; ?>"
                           class="input-field" placeholder="Masukkan judul episode">
                </div>
                
                <!-- Duration -->
                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                        Durasi (menit)
                    </label>
                    <input type="number" id="duration" name="duration" min="0"
                           value="<?php echo $episode['duration'] ?? ''; ?>"
                           class="input-field" placeholder="Masukkan durasi episode">
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="space-y-6">
                
                
                <!-- HXFile Upload per Kualitas (opsional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        HXFile Upload per Kualitas (opsional)
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="hxfile_file_480" class="block text-xs font-medium text-gray-600 mb-1">Upload 480p</label>
                            <input type="file" id="hxfile_file_480" name="hxfile_file_480" accept="video/*" class="input-field">
                            <p class="mt-1 text-xs text-gray-500">Jika diisi, sistem akan otomatis mengisi Embed & Download dari HXFile.</p>
                        </div>
                        <div>
                            <label for="hxfile_file_720" class="block text-xs font-medium text-gray-600 mb-1">Upload 720p</label>
                            <input type="file" id="hxfile_file_720" name="hxfile_file_720" accept="video/*" class="input-field">
                        </div>
                        <div>
                            <label for="hxfile_file_1080" class="block text-xs font-medium text-gray-600 mb-1">Upload 1080p</label>
                            <input type="file" id="hxfile_file_1080" name="hxfile_file_1080" accept="video/*" class="input-field">
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Gunakan ini bila ingin upload langsung ke HXFile. Pastikan ukuran file sesuai batas server.</p>
                </div>

                <!-- Per-Quality Embed URLs -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Embed per Kualitas (opsional)
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="embed_480_url" class="block text-xs font-medium text-gray-600 mb-1">480p Embed URL</label>
                            <input type="url" id="embed_480_url" name="embed_480_url"
                                   value="<?php echo $episode['embed_480_url'] ?? ''; ?>"
                                   class="input-field" placeholder="https://example.com/embed-480p">
                        </div>
                        <div>
                            <label for="embed_720_url" class="block text-xs font-medium text-gray-600 mb-1">720p Embed URL</label>
                            <input type="url" id="embed_720_url" name="embed_720_url"
                                   value="<?php echo $episode['embed_720_url'] ?? ''; ?>"
                                   class="input-field" placeholder="https://example.com/embed-720p">
                        </div>
                        <div>
                            <label for="embed_1080_url" class="block text-xs font-medium text-gray-600 mb-1">1080p Embed URL</label>
                            <input type="url" id="embed_1080_url" name="embed_1080_url"
                                   value="<?php echo $episode['embed_1080_url'] ?? ''; ?>"
                                   class="input-field" placeholder="https://example.com/embed-1080p">
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Masukkan link embed (src iframe) per kualitas. Kosongkan jika tidak tersedia.</p>
                </div>

                <!-- Per-Quality Download URLs -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Link Download (opsional)
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="dl_480_url" class="block text-xs font-medium text-gray-600 mb-1">480p Download</label>
                            <input type="url" id="dl_480_url" name="dl_480_url"
                                   value="<?php echo $episode['dl_480_url'] ?? ''; ?>"
                                   class="input-field" placeholder="https://example.com/download-480p.mp4">
                        </div>
                        <div>
                            <label for="dl_720_url" class="block text-xs font-medium text-gray-600 mb-1">720p Download</label>
                            <input type="url" id="dl_720_url" name="dl_720_url"
                                   value="<?php echo $episode['dl_720_url'] ?? ''; ?>"
                                   class="input-field" placeholder="https://example.com/download-720p.mp4">
                        </div>
                        <div>
                            <label for="dl_1080_url" class="block text-xs font-medium text-gray-600 mb-1">1080p Download</label>
                            <input type="url" id="dl_1080_url" name="dl_1080_url"
                                   value="<?php echo $episode['dl_1080_url'] ?? ''; ?>"
                                   class="input-field" placeholder="https://example.com/download-1080p.mp4">
                        </div>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">Link download per kualitas (bisa diarahkan ke file langsung atau halaman download).</p>
                </div>
                <!-- Embed Preview -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Preview Embed
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                        <?php
                        $previewEmbed = $episode['embed_1080_url'] ?? ($episode['embed_720_url'] ?? ($episode['embed_480_url'] ?? ''));
                        if (!empty($previewEmbed)):
                        ?>
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe src="<?php echo htmlspecialchars($previewEmbed); ?>" class="w-full h-full" frameborder="0" allowfullscreen referrerpolicy="origin"></iframe>
                            </div>
                        <?php else: ?>
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Preview akan muncul setelah menyimpan embed per kualitas.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Instructions -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-800 mb-2">Petunjuk Video:</h4>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Gunakan link embed (src dari iframe) per kualitas: 480p, 720p, 1080p</li>
                <li>• Link download per kualitas opsional; jika diisi, tombol download akan mengikuti kualitas yang dipilih</li>
                <li>• Jika hanya satu kualitas yang tersedia, dropdown di halaman nonton akan menampilkan kualitas tersebut</li>
            </ul>
        </div>
        
        <!-- Form Actions -->
        <div class="mt-8 flex justify-between">
            <div>
                <?php if (!isset($episode)): ?>
                    <button type="submit" name="add_more" value="1" class="btn-secondary">
                        Simpan & Tambah Lagi
                    </button>
                <?php endif; ?>
            </div>
            <div class="flex space-x-4">
                <?php
                $cancelUrl = 'manage-episode.php';
                if (isset($episode)) {
                    $cancelUrl .= '?anime_id=' . $episode['anime_id'];
                } elseif (isset($anime)) {
                    $cancelUrl .= '?anime_id=' . $anime['id'];
                }
                ?>
                <a href="<?php echo $cancelUrl; ?>" class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <?php echo isset($episode) ? 'Update Episode' : 'Simpan Episode'; ?>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Auto-fill anime title and next episode when anime is selected,
// and keep title in sync when episode number changes.
document.addEventListener('DOMContentLoaded', function() {
    const animeSelect = document.getElementById('anime_id');
    const epInput = document.getElementById('episode_number');
    const titleInput = document.getElementById('title');
    const isEditing = <?php echo isset($episode) ? 'true' : 'false'; ?>;

    function buildTitle() {
        const animeTitle = animeSelect.options[animeSelect.selectedIndex]?.text || '';
        const epNum = epInput.value || '';
        if (animeTitle && epNum) {
            titleInput.value = `${animeTitle} - Episode ${epNum}`;
        }
    }

    animeSelect.addEventListener('change', function() {
        const animeId = this.value;
        if (!isEditing && animeId && !epInput.value) {
            // Get next episode number for selected anime (only on create or empty value)
            fetch(`get-next-episode.php?anime_id=${animeId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.nextEpisode) {
                        epInput.value = data.nextEpisode;
                    }
                    buildTitle();
                })
                .catch(error => console.error('Error:', error));
        } else {
            // When editing or episode number already set, never override it
            buildTitle();
        }
    });

    // Update title when episode number changes
    epInput.addEventListener('input', buildTitle);

    // Initialize: avoid auto-changing episode number when editing
    if (animeSelect.value) {
        if (!isEditing && !epInput.value) {
            animeSelect.dispatchEvent(new Event('change'));
        } else {
            buildTitle();
        }
    } else {
        buildTitle();
    }
});
</script>
<script>
// HXFile async upload: auto-fill embed/download when a file is selected
document.addEventListener('DOMContentLoaded', function() {
  const map = [
    { q: '480', fileId: 'hxfile_file_480', embedId: 'embed_480_url', dlId: 'dl_480_url' },
    { q: '720', fileId: 'hxfile_file_720', embedId: 'embed_720_url', dlId: 'dl_720_url' },
    { q: '1080', fileId: 'hxfile_file_1080', embedId: 'embed_1080_url', dlId: 'dl_1080_url' }
  ];

  function ensureStatusEl(container) {
    let el = container.querySelector('.hx-status');
    if (!el) {
      el = document.createElement('p');
      el.className = 'hx-status text-xs mt-1 text-gray-500';
      container.appendChild(el);
    }
    return el;
  }

  // Create/find a progress bar element for visual upload progress
  function ensureProgressEl(container) {
    let bar = container.querySelector('.hx-progress-bar');
    if (!bar) {
      const wrap = document.createElement('div');
      wrap.className = 'hx-progress w-full bg-gray-200 rounded h-2 mt-1 overflow-hidden';
      bar = document.createElement('div');
      bar.className = 'hx-progress-bar bg-blue-600 h-2 w-0 transition-all';
      wrap.appendChild(bar);
      container.appendChild(wrap);
    }
    return bar;
  }

  // Disable/enable submit buttons while uploading
  function setSubmitting(form, disable) {
    if (!form) return;
    form.querySelectorAll('button[type="submit"]').forEach(btn => {
      btn.disabled = !!disable;
      btn.classList.toggle('opacity-50', !!disable);
      btn.classList.toggle('cursor-not-allowed', !!disable);
    });
  }

  map.forEach(m => {
    const fileInput = document.getElementById(m.fileId);
    if (!fileInput) return;

    fileInput.addEventListener('change', async function() {
      const file = this.files && this.files[0];
      if (!file) return;

      const container = this.parentElement;
      const statusEl = ensureStatusEl(container);
      const progressBar = ensureProgressEl(container);
      const formEl = fileInput.closest('form');
      setSubmitting(formEl, true);

      // Initial UI
      statusEl.textContent = 'Uploading to HXFile... 0%';
      statusEl.classList.remove('text-green-600', 'text-red-600');
      statusEl.classList.add('text-blue-600');
      progressBar.style.width = '0%';

      const fd = new FormData();
      fd.append('file', file);
      fd.append('quality', m.q);

      // Try direct-to-HXFile upload via server ticket. If successful, return early to avoid fallback.
      try {
        const tkResp = await fetch('hxfile-ticket.php', { method: 'POST' });
        const tk = await tkResp.json();
        if (tk && tk.success && tk.server_url && tk.sess_id) {
          const hxFd = new FormData();
          hxFd.append('sess_id', tk.sess_id);
          hxFd.append('file', file, file.name);

          const xhr2 = new XMLHttpRequest();
          xhr2.open('POST', tk.server_url, true);

          xhr2.upload.onprogress = function(e) {
            if (e.lengthComputable) {
              const percent = Math.max(0, Math.min(100, Math.round((e.loaded / e.total) * 100)));
              progressBar.style.width = percent + '%';
              statusEl.textContent = 'Uploading to HXFile... ' + percent + '%';
            }
          };

          xhr2.onreadystatechange = function() {
            if (xhr2.readyState !== 4) return;
            setSubmitting(formEl, false);

            let raw = xhr2.responseText || '';
            // Try to extract the file code from raw response
            let code = '';
            try {
              let m1 = raw.match(/"file[_ ]?code"\s*:\s*"([A-Za-z0-9]+)"/i);
              let m2 = raw.match(/hxfile\.co\/([A-Za-z0-9]+)/i);
              let m3 = raw.match(/embed-([A-Za-z0-9]+)\.html/i);
              if (m1) code = m1[1];
              else if (m2) code = m2[1];
              else if (m3) code = m3[1];
              else {
                const trimmed = raw.trim();
                if (/^[A-Za-z0-9]{10,20}$/.test(trimmed)) code = trimmed;
              }
            } catch (_e) {}

            const finalizeWithCode = function(c) {
              const embedInput = document.getElementById(m.embedId);
              const dlInput = document.getElementById(m.dlId);
              if (embedInput) embedInput.value = 'https://xshotcok.com/embed-' + c + '.html';
              if (dlInput) dlInput.value = 'https://hxfile.co/' + c;

              if (formEl) {
                const flagName = 'hxfile_uploaded_' + m.q;
                const codeName = 'hxfile_filecode_' + m.q;
                let flag = formEl.querySelector('input[name="' + flagName + '"]');
                if (!flag) {
                  flag = document.createElement('input');
                  flag.type = 'hidden';
                  flag.name = flagName;
                  formEl.appendChild(flag);
                }
                flag.value = '1';
                let codeEl = formEl.querySelector('input[name="' + codeName + '"]');
                if (!codeEl) {
                  codeEl = document.createElement('input');
                  codeEl.type = 'hidden';
                  codeEl.name = codeName;
                  formEl.appendChild(codeEl);
                }
                codeEl.value = c;
              }

              try { fileInput.value = ''; } catch (e) {}

              progressBar.style.width = '100%';
              statusEl.textContent = 'Uploaded: ' + (c || '');
              statusEl.classList.remove('text-blue-600', 'text-red-600');
              statusEl.classList.add('text-green-600');
            };

            if (xhr2.status === 200 && code) {
              finalizeWithCode(code);
            } else {
              // Fallback: lookup by filename (may require short indexing time)
              const body = new URLSearchParams({ name: file.name }).toString();
              fetch('hxfile-lookup.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body
              }).then(r => r.json()).then(data => {
                if (data && data.success && data.filecode) {
                  finalizeWithCode(data.filecode);
                } else {
                  const msg = (data && data.error) ? data.error : ('HTTP ' + xhr2.status);
                  statusEl.textContent = 'Upload failed: ' + msg;
                  statusEl.classList.remove('text-blue-600', 'text-green-600');
                  statusEl.classList.add('text-red-600');
                  console.error('HXFile direct upload/lookup failed', xhr2.status, data, raw);
                }
              }).catch(err => {
                statusEl.textContent = 'Upload error: ' + err.message;
                statusEl.classList.remove('text-blue-600', 'text-green-600');
                statusEl.classList.add('text-red-600');
                console.error('HXFile lookup exception', err);
              });
            }
          };

          xhr2.onerror = function() {
            setSubmitting(formEl, false);
            statusEl.textContent = 'Upload error: network or server issue';
            statusEl.classList.remove('text-blue-600', 'text-green-600');
            statusEl.classList.add('text-red-600');
            console.error('HXFile direct upload network error');
          };

          xhr2.send(hxFd);
          return;
        }
      } catch (e) {
        console.error('HXFile ticket error', e);
      }
      // Fallback: continue with server-side hxfile-upload.php below

      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'hxfile-upload.php', true);

      // Progress (upload)
      xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
          const percent = Math.max(0, Math.min(100, Math.round((e.loaded / e.total) * 100)));
          progressBar.style.width = percent + '%';
          statusEl.textContent = 'Uploading to HXFile... ' + percent + '%';
        }
      };

      xhr.onreadystatechange = function() {
        if (xhr.readyState !== 4) return;
        setSubmitting(formEl, false);

        let data = null;
        try {
          data = JSON.parse(xhr.responseText || '{}');
        } catch (parseErr) {
          statusEl.textContent = 'Upload failed: invalid JSON response';
          statusEl.classList.remove('text-blue-600');
          statusEl.classList.add('text-red-600');
          console.error('HXFile upload parse error', parseErr, xhr.responseText);
          return;
        }

        if (xhr.status === 200 && data && data.success) {
          const embedInput = document.getElementById(m.embedId);
          const dlInput = document.getElementById(m.dlId);
          if (embedInput) embedInput.value = data.embed_url || '';
          if (dlInput) dlInput.value = data.download_url || '';

          // Mark as uploaded (server-side hint) and prevent file from being posted again on Save/Update
          if (formEl) {
            const flagName = 'hxfile_uploaded_' + m.q;
            const codeName = 'hxfile_filecode_' + m.q;
            let flag = formEl.querySelector('input[name="' + flagName + '"]');
            if (!flag) {
              flag = document.createElement('input');
              flag.type = 'hidden';
              flag.name = flagName;
              formEl.appendChild(flag);
            }
            flag.value = '1';
            let codeEl = formEl.querySelector('input[name="' + codeName + '"]');
            if (!codeEl) {
              codeEl = document.createElement('input');
              codeEl.type = 'hidden';
              codeEl.name = codeName;
              formEl.appendChild(codeEl);
            }
            codeEl.value = data.filecode || '';
          }

          // Clear the file input so it won't be uploaded again during form submit
          try { fileInput.value = ''; } catch (e) {}

          progressBar.style.width = '100%';
          statusEl.textContent = 'Uploaded: ' + (data.filecode || '');
          statusEl.classList.remove('text-blue-600', 'text-red-600');
          statusEl.classList.add('text-green-600');
        } else {
          const msg = (data && data.error) ? data.error : ('HTTP ' + xhr.status);
          statusEl.textContent = 'Upload failed: ' + msg;
          statusEl.classList.remove('text-blue-600', 'text-green-600');
          statusEl.classList.add('text-red-600');
          console.error('HXFile upload failed', xhr.status, data, xhr.responseText);
        }
      };

      xhr.onerror = function() {
        setSubmitting(formEl, false);
        statusEl.textContent = 'Upload error: network or server issue';
        statusEl.classList.remove('text-blue-600', 'text-green-600');
        statusEl.classList.add('text-red-600');
        console.error('HXFile upload network error');
      };

      // Start upload
      xhr.send(fd);
    });
  });
});
</script>