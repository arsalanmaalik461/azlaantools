# Azlaan Tools — Free Online Tools Hub for Pakistan 🇵🇰

A Laravel (PHP) website with **165 free tools** — no signup, no login. Almost every
tool runs 100% in the visitor's browser (JavaScript), so hosting cost is near
zero and user files never touch the server.

**Site by:** Azlaan Electric AC Solar Center, Faisalabad
Malik Arslan 0300-8987448 · Malik Rehan 0314-6332385
**Domains:** azlaanmalik.tech / arslanmalik.site

---

## Tool list (165)

_Phase 1: 34 tools (bills, solar, core PDF/image, utilities). Phase 2: +50 daily-use tools (text & OCR, photo, more PDF, daily calculators, business docs, web & fun tools). Phase 3: +80 research-driven tools (built from the most-used free tool categories on Google — PDF, image, video/audio, text, finance, health, developer, electrical/Pakistan-specific) + 1 daily "Solar Rates Today Pakistan" page._

### 🧾 Bills — Pakistan
| Tool | URL | How it works |
|---|---|---|
| Electricity Bill Check | `/tools/electricity-bill-check` | All DISCOs (LESCO, IESCO, FESCO, MEPCO, GEPCO, HESCO, SEPCO, PESCO, QESCO, TESCO). Enter 14-digit reference number → copied + official company portal opens. DISCOs don't offer a public API, so official-portal redirect is the correct method. |
| Gas Bill Check | `/tools/gas-bill-check` | SNGPL & SSGC — consumer number copied, official portal opens. |
| PTCL Bill Check | `/tools/ptcl-bill-check` | Official PTCL portal with instructions. |
| Electricity Bill Estimator | `/tools/electricity-bill-estimator` | Units → estimated bill using approx. residential slab rates + FPA/GST (all fields editable). Clearly labelled an estimate. |
| E-Challan Check Guide | `/tools/e-challan-check` | **Guide page, not a lookup** (traffic police offer no public API). Verified official links for Punjab (PSCA) and Islamabad Police; Sindh links to the official Sindh Police site; clearly states where no official online portal exists (KPK, Balochistan, GB, AJK). PSID payment steps + fake-SMS scam warning + approximate fines table (labelled approximate). |

### ☀️ Solar
| Tool | URL |
|---|---|
| Solar Load Calculator | `/tools/solar-load-calculator` |
| Solar Price Estimator | `/tools/solar-price-estimator` |
| Solar Panels Required Calculator | `/tools/solar-panels-calculator` |
| Electricity Units Converter (kWh) | `/tools/electricity-units-converter` |
| **Solar Rates Today (Pakistan)** | `/tools/solar-rates-today` |
| Solar ROI & Payback Calculator | `/tools/solar-roi-payback-calculator` |

> **Solar Rates Today** is the hub's only data-driven page: it reads
> `resources/data/solar-rates.json` (10 panels, 24 inverters, 32 batteries with
> price ranges, last researched 2026-10-01). Panel rates are per-watt figures
> from rate lists dated 30 Sep – 1 Oct 2026; the page shows the range and the
> sources. See "Updating solar rates" below.

### 📄 PDF (client-side: pdf-lib + pdf.js via CDN)
Merge PDF · Split PDF · PDF to JPG · JPG to PDF · Compress PDF ·
Add Page Numbers · PDF Watermark · Rotate PDF ·
Sign PDF (visible signature — not cryptographic) ·
Edit PDF (add text/images only — existing text cannot be changed) ·
Delete PDF Pages · Organize PDF (reorder/rotate/extract) ·
Fill PDF Form (AcroForm) · Protect PDF (real encryption, password) ·
Unlock PDF (removes owner restrictions only — cannot crack unknown passwords) ·
PDF to PNG (with ZIP-all) · PDF to Word (text-based, best-effort) · PNG to PDF

### 🖼️ Image (client-side Canvas)
Image Compressor · Image Resizer · Image Converter (JPG/PNG/WebP) ·
Remove Background (opt-in ~40 MB AI model, downloaded once) ·
Compress Image to KB (exact target 20/50/100/200 KB + custom) · Photo Editor ·
HEIC to JPG (iPhone) · Collage Maker · EXIF Remover (location/camera metadata) ·
Photo Censor (manual blur/pixelate/black) · Base64 to Image · GIF Maker

### 🎬 Video & Audio (client-side; MediaRecorder / WebAudio)
Screen Recorder · Voice Recorder (WAV export) · Audio Cutter (WAV/MP3 export) ·
Audio Joiner (WAV/MP3) · Video to GIF (short clips, ~15 s cap) ·
Subtitle Converter (SRT ↔ VTT + timing shift) · Teleprompter (mirror mode) ·
Metronome (tap tempo)

### 📝 Text & OCR (client-side; OCR = Tesseract.js, PDF text = pdf.js)
Image to Text (OCR — English + Urdu) · PDF to Text · Text to Speech ·
Speech to Text (voice typing) · Remove Duplicate Lines · Sort Lines A–Z ·
Reverse Text · Slug Generator · Base64 Encode/Decode · URL Encode/Decode ·
JSON Formatter & Validator · Lorem Ipsum Generator · Online Notepad (localStorage) ·
Character Counter (X/IG/SMS/meta limits) · Fancy Text Generator (15 styles) ·
Text Repeater (up to 10,000) · Remove Line Breaks · Word Frequency Counter
(with density + 2/3-word phrases) · Readability Checker (Flesch — for English) ·
Roman Urdu → Urdu Converter (1,100+ word dictionary, best-effort, labelled) ·
Text to Handwriting (handwriting-style PNG pages) ·
Text Encrypt / Decrypt (Web Crypto AES-256 — forget the password = lost forever, labelled)

### 📸 Photo Tools (client-side Canvas; Cropper.js for cropping)
Passport Size Photo Maker (CNIC/passport sizes + 8-photo print sheet) ·
Image Cropper · Rotate & Flip Image · Image Watermark · Favicon Generator ·
Color Picker from Image · Image to Base64 · Meme Generator

### 🧮 Daily Calculators
Pakistan Salary Income Tax Calculator (FBR 2025-26 salaried slabs, editable/indicative) ·
Fuel Cost Calculator · Discount Calculator · CGPA to Percentage ·
Marks Percentage Calculator · Attendance Calculator · Date to Hijri Converter ·
Prayer Times (Aladhan free API, PK cities) · Pregnancy Due Date Calculator ·
Love Calculator (entertainment only — labelled on page) · Timer & Stopwatch ·
Business Days Calculator (Sat–Sun or Fri–Sat weekend) · Final Grade Calculator ·
Pomodoro Timer (study/focus)

### 💰 Finance Calculators
Compound Interest · Simple Interest · Home Loan / Mortgage ·
EMI Calculator (full repayment schedule) · SIP Calculator ·
GST / Sales Tax Calculator (18% preset, add/remove) · Retirement Calculator ·
Inflation Calculator · Break-Even Calculator · Tip & Bill Split (per-person)

### ❤️ Health Calculators
TDEE / Calorie Calculator · BMR Calculator · Body Fat Calculator ·
Water Intake Calculator · Sleep Calculator (90-min cycles) ·
Ovulation Calculator (estimate only — not for contraception, labelled)

### 💼 Business & Documents
Resume / CV Builder (print to PDF) · Invoice Generator (print) ·
Signature Maker (draw or type → PNG) · Word to PDF (text → printable PDF) ·
Application / Letter Generator (leave, job, fee concession — English + Urdu templates, print CSS)

### 🧰 Utilities
Age Calculator · Date Difference Calculator · QR Code Generator ·
WhatsApp Link Generator · Password Generator · Word Counter ·
Percentage Calculator · GPA/CGPA Calculator (HEC scale) · BMI Calculator ·
Loan/EMI Calculator · Zakat Calculator · Currency Converter (live rates via
open.er-api.com, offline fallback) · YouTube Thumbnail Downloader ·
Unit Converter (incl. Marla/Kanal) · Text Case Converter ·
Random Number Generator · Internet Speed Test · My IP & Device Info ·
Gold Price Calculator (user-entered tola rate — **not** live, labelled) ·
Cash Counter (PKR notes + amount in words, lakh/crore) ·
Plot Size Calculator (Marla/Kanal, 272.25 vs 225 sq ft switch)

### 🌐 Web & Fun Tools
HEX/RGB Color Converter + Palette · Screen Resolution Checker ·
Typing Speed Test (WPM) · Dice Roller & Coin Flip · Countdown Maker
(shareable event links) · Time Zone Converter · Roman Numerals Converter ·
Binary/Text Converter · Profit/Margin Calculator · Download Time Calculator ·
Aspect Ratio Calculator

### 👨‍💻 Developer Tools
Regex Tester · JWT Decoder (decode only — signatures NOT verified, labelled) ·
Hash Generator (MD5/SHA-1/SHA-256, text + file) · UUID Generator (bulk, up to 1,000) ·
Unix Timestamp Converter (with PKT) · HTML Formatter (beautify/minify) ·
JS Minifier (Terser, size saving %) · Meta Tag Generator (SEO + OG + Twitter Card) ·
Robots.txt Generator · CSS Gradient Generator · Diff Checker

### ⚡ Electrical & Energy Tools
UPS Backup Time Calculator · Battery Sizing Calculator ·
Appliance Running Cost Calculator · AC Tonnage Calculator ·
CCTV Storage Calculator · Generator Fuel Consumption Calculator ·
Ohm's Law Calculator (AC/DC) · Voltage Drop & Wire Size Calculator (mm²)

---

## Project structure

```
tools-hub/
├── composer.json              Laravel 11 / PHP 8.2
├── .env.example
├── routes/web.php             All 165 tool routes (Route::view)
├── resources/data/
│   └── solar-rates.json       Daily-update data for /tools/solar-rates-today
├── resources/views/
│   ├── layouts/app.blade.php  Header, nav, footer (contact info)
│   ├── home.blade.php         Homepage: search + tools grid by category
│   └── tools/*.blade.php      One Blade view per tool (165 files)
├── public/
│   ├── index.php, .htaccess
│   └── css/app.css
├── app/Http/Controllers/      Base controller (tools need none)
└── bootstrap/app.php          Laravel 11 bootstrap
```

## How to deploy on cPanel shared hosting

**Option A — Fresh Laravel install (recommended)**
1. In cPanel → Terminal (or SSH): `cd ~/public_html` (or the domain's document root's parent) and run
   `composer create-project laravel/laravel azlaan-app` — or use Softaculous/cPanel's Laravel installer if available.
2. Copy these folders from this zip **over** the fresh install:
   `routes/web.php`, `resources/views/`, `public/css/`
3. Point the domain's document root to the Laravel app's `public/` folder
   (cPanel → Domains → Document Root).
4. Copy `.env.example` to `.env`, set `APP_URL` to your domain, then run
   `php artisan key:generate` and `php artisan config:cache`.
5. Done — no database is needed (no DB is used at all).

**Option B — Use this zip as the project**
1. Upload and extract the zip in a folder **outside** public_html (e.g. `~/azlaan-tools`).
2. Run `composer install` there (cPanel Terminal).
3. Copy `.env.example` → `.env`, run `php artisan key:generate`.
4. Point the domain document root to `~/azlaan-tools/public`.
5. Make sure `storage/` and `bootstrap/cache/` are writable (755/775).

**Option C — No Composer access?** Ask your host to enable Composer, or deploy on
any PHP 8.2+ hosting/VPS the same way. The views are plain Blade, so the site
also works inside any existing Laravel 11 app by copying `routes` entries +
`resources/views` + `public/css`.

### After going live
- Submit the site to **Google Search Console** and add the sitemap (homepage + 165 tool URLs).
- Apply for **Google AdSense** once traffic starts (needs some content/traffic first).
- Share tool links on Facebook/WhatsApp/TikTok — the bill checkers and solar
  calculators are the most shareable in Pakistan.
- Update slab rates in `electricity-bill-estimator.blade.php` and solar rates in
  `solar-price-estimator.blade.php` when market/government rates change (plain numbers in the JS).

### Updating solar rates (`solar-rates-today`)
The `/tools/solar-rates-today` page renders entirely from **one data file**:
`resources/data/solar-rates.json`. The Blade view (`tools/solar-rates-today.blade.php`)
reads it with `file_get_contents(resource_path('data/solar-rates.json'))`, so no
code change is needed to refresh rates.

**Manual update (recommended for now):**
1. Open `resources/data/solar-rates.json`.
2. Edit `updated` to today's date (e.g. `"2026-10-02"`).
3. Update any item's `price_min` / `price_max` (or `rate_per_watt_min` / `rate_per_watt_max`
   for panels), set `trend` to `"up"`, `"down"` or `"same"` versus yesterday,
   and add the source site name in `source`.
4. Re-deploy the single JSON file (upload over the old one) — the page updates immediately.
   Validate the JSON first if unsure: `node -e "JSON.parse(require('fs').readFileSync('resources/data/solar-rates.json','utf8'))"`.

**Optional automated update (server cron + PHP):**
Add a daily server-side PHP script (cron job) that pulls prices from whichever
rate-list websites are currently permitting light, polite reads, then rewrites the
JSON file. Notes:
- Rates on the page are **compiled from public listings with source attribution**;
  do not build an aggressive scraper — keep request volume low, respect
  `robots.txt`, and always keep the `sources` list on the page accurate.
- Never present the page as live/official market data: keep the
  `market_note` disclaimer ("compiled from public listings … rates vary by city,
  dealer, warranty and stock") and the `updated` date prominent.
- `solar-price-estimator` is intentionally NOT driven by this file: it prices
  **full solar systems** (per-watt system cost, not panel-only rates) — a different
  metric — so the two are maintained separately.

## Notes & honesty
- Bill "check" tools do **not** fetch bill data — electricity/gas/PTCL companies
  provide no public API. The tools copy the reference/consumer number and open
  the **official** portal, which is the safe and legal method.
- Bill estimator and solar prices are **estimates** and are labelled as such on the pages.
- Currency converter uses the free `open.er-api.com` API in the browser; if it
  fails, labelled offline fallback rates are used.
- Phase 2 notes: Salary tax uses FBR Finance Act 2025 salaried slabs and is
  labelled an estimate on the page (slab table visible). Prayer Times uses the
  free Aladhan API (no key) at runtime; it needs internet and shows an error
  if the API is unreachable. OCR (Tesseract.js) downloads its language data on
  first use, so the first OCR run is slower. Hijri conversion uses the
  Umm al-Qura calendar and may differ ±1 day from local moon sighting (noted
  on the page).
