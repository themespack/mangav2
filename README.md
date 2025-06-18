# MangaStreamV2 - WordPress Theme

Tema WordPress custom untuk situs pembaca manga dengan fitur chapter navigation, AJAX filter, statistik pembaca, dan optimasi SEO.

---

## 📁 Struktur Direktori

```
mangastreamv2/
├── functions.php
├── style.css
├── single-manga.php
├── inc/
│   ├── ajax-handlers.php
│   ├── performance.php
│   ├── seo.php
│   ├── security.php
│   └── manga-functions.php
├── template-parts/
│   ├── manga-single.php
│   ├── manga-reader.php
│   └── manga-list.php
├── css/
├── js/
```

---

## ⚙️ Fitur Utama

- Custom Post Type: `manga`, `chapter`
- Bookmark & Progress Baca per User
- AJAX Filter Manga (search, genre, status, sort)
- SEO + Schema.org + OpenGraph
- Statistik pembaca (view counter)
- Lazy Load Gambar & Preload Resource
- Rate Limiting & Sanitasi Input

---

## 🛡️ Keamanan

- `check_ajax_referer` untuk AJAX
- Validasi input dengan `sanitize_text_field`, `absint`, dsb
- Pembatasan akses lewat `verify_manga_permissions`
- Fungsi pembatasan aksi IP-based (`rate_limit_check`)
- Disarankan mengaktifkan header keamanan HTTP:

```php
function add_security_headers() {
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: SAMEORIGIN");
    header("Referrer-Policy: no-referrer-when-downgrade");
    header("Permissions-Policy: fullscreen=(self)");
}
add_action('send_headers', 'add_security_headers');
```

---

## 📈 SEO

- Open Graph + Twitter Cards
- Schema.org Book (`@type: Book`)
- Title, excerpt, author, rating, genre auto-generated

---

## 📦 Kebutuhan

- WordPress 5.8+
- PHP 7.4+
- Aktifkan permalink "post name"

---

## 🧩 Hook Penting

- `after_setup_theme`
- `wp_enqueue_scripts`
- `the_content` (untuk lazy load)
- `wp_head` (SEO + preload)

---

## 📝 Lisensi

MIT License
