
    // ── Thumbnail switcher ──────────────────────
    const mainImg = document.getElementById('main-img');
    const thumbs = document.querySelectorAll('.thumb');

    thumbs.forEach(thumb => {
      thumb.addEventListener('click', () => {
        // Fade out
        mainImg.classList.add('fading');
        setTimeout(() => {
          mainImg.src = thumb.dataset.src;
          mainImg.classList.remove('fading');
        }, 200);
        // Active state
        thumbs.forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
      });
    });
