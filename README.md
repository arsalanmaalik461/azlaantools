# 🛠️ Azlaan Tools — 1100+ Free Online Tools

> **Developed by [Arslan Malik](https://github.com/arsalanmaalik461)**
> 📱 WhatsApp: [+92 300 8987448](https://wa.me/923008987448) · 🌐 Website: [arslanmalik.tech](https://arslanmalik.tech)

**Live site:** [https://arslanmalik.tech](https://arslanmalik.tech)

A free tools hub with **1100+ online tools** — no signup, no login, no paywall. Bill checkers for Pakistan, solar estimators, PDF tools, image tools, text tools, calculators, converters, developer utilities, business documents, health & finance tools, and video downloaders. Almost every tool runs 100% in the visitor's browser (JavaScript), so hosting cost stays near zero and user files never touch the server.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black)
![License](https://img.shields.io/badge/License-Proprietary-red?style=flat-square)

## 📌 About

Azlaan Tools started as a small set of Pakistan bill checkers and grew into one of the largest free tool collections on a single Laravel site: **1100+ tools across 17 categories**, plus a Government Schemes directory (30 verified schemes) and a daily "Solar Rates in Pakistan" page. The whole site is in simple, clear English for a global audience.

## ✨ Featured Categories

- 🧾 **Pakistan bill checkers** — all DISCOs (LESCO, IESCO, FESCO, MEPCO, GEPCO, HESCO, SEPCO, PESCO, QESCO, TESCO), SNGPL/SSGC gas, PTCL
- ☀️ **Solar tools** — load calculator, system price estimator, daily solar rates
- 🎬 **Video downloaders** — TikTok, X/Twitter (more platforms ready)
- 📄 **PDF tools** — merge, split, compress, convert, protect
- 🖼️ **Image tools** — compress, resize, convert, background tweaks
- 🔤 **Text tools** — case converters, word counters, lorem, code formatters
- 🧮 **Calculators** — finance, health, age, GPA, unit converters
- 👨‍💻 **Developer tools** — JSON formatter, Base64, hash generators, regex tester
- 🏛️ **Government schemes** — 30 verified schemes with official source links

## 🛠️ Tech Stack

- **Backend:** PHP 8 + Laravel (Blade templates, zero build step for most tools)
- **Frontend:** Vanilla JavaScript (tools run client-side), custom CSS (light theme)
- **Icons:** 175-icon custom SVG library, one icon per tool
- **Data:** JSON catalogs (`resources/data/`) — adding a tool = one catalog entry + one Blade view
- **SEO:** sitemap with 1100+ URLs, FAQ schema, meta per tool

## 🚀 Getting Started

```bash
git clone https://github.com/arsalanmaalik461/azlaantools.git
cd azlaantools
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

Then open `http://127.0.0.1:8000`. No database is required for the tools themselves.

## 📁 Project Structure

```
app/            → Laravel app (controllers, console)
resources/
  views/        → 1100+ Blade tool pages + layouts
  data/         → catalog.json, gov-schemes.json, solar-rates.json
  css|js        → theme CSS, per-tool JS
routes/web.php  → tool routes
public/         → entry point, images, manifest
```

## 🤝 Contributing

Found a broken tool or want a new one? Open an issue or reach out on [WhatsApp](https://wa.me/923008987448).

## 📄 License

Copyright © Arslan Malik. All rights reserved — the code is shared publicly for learning and review.

---

<div align="center"><b>Developed with ❤️ by <a href="https://github.com/arsalanmaalik461">Arslan Malik</a></b><br>📱 <a href="https://wa.me/923008987448">WhatsApp: +92 300 8987448</a> · 🌐 <a href="https://arslanmalik.tech">arslanmalik.tech</a></div>
