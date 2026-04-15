<p align="center">
  <img src="https://img.shields.io/badge/Devacto-FaceID-blue?style=for-the-badge&logo=data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0id2hpdGUiPjxwYXRoIGQ9Ik05IDExLjc1YTEuMjUgMS4yNSAwIDEgMCAwLTIuNSAxLjI1IDEuMjUgMCAwIDAgMCAyLjV6bTYgMGExLjI1IDEuMjUgMCAxIDAgMC0yLjUgMS4yNSAxLjI1IDAgMCAwIDAgMi41ek0xMiAyQzYuNDggMiAyIDYuNDggMiAxMnM0LjQ4IDEwIDEwIDEwIDEwLTQuNDggMTAtMTBTMTcuNTIgMiAxMiAyem0wIDE4Yy00LjQxIDAtOC0zLjU5LTgtOHMzLjU5LTggOC04IDggMy41OSA4IDgtMy41OSA4LTggNHoiLz48L3N2Zz4=&logoColor=white" alt="Devacto FaceID" height="40"/>
</p>

<h1 align="center">Devacto FaceID</h1>

<p align="center">
  <strong>AI-Powered Guest Management & Face Recognition System</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 11"/>
  <img src="https://img.shields.io/badge/React-19-61DAFB?style=flat-square&logo=react&logoColor=black" alt="React 19"/>
  <img src="https://img.shields.io/badge/Inertia.js-2.0-9553E9?style=flat-square&logo=inertia&logoColor=white" alt="Inertia.js"/>
  <img src="https://img.shields.io/badge/TailwindCSS-4.0-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white" alt="TailwindCSS"/>
  <img src="https://img.shields.io/badge/face--api.js-SSD_MobileNet-4285F4?style=flat-square&logo=tensorflow&logoColor=white" alt="face-api.js"/>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/License-MIT-green?style=flat-square" alt="License"/>
  <img src="https://img.shields.io/badge/Status-Production-success?style=flat-square" alt="Status"/>
</p>

---

## 📋 Overview

**Devacto FaceID** is an intelligent guest management system that uses **real-time face recognition** to streamline guest registration and attendance tracking. Built for schools, offices, and events — it identifies returning visitors instantly and automates the check-in process.

### ✨ Key Features

| Feature | Description |
|---------|-------------|
| 🎯 **AI Face Detection** | SSD MobileNetV1 model with multi-frame descriptor averaging for high accuracy |
| 👓 **Glasses Support** | Optimized detection that works reliably with glasses, sunglasses, and partial occlusions |
| 🔄 **Smart Check-in** | Recognized guests get auto-filled forms; new guests register seamlessly |
| 🗣️ **Voice Feedback** | Text-to-speech greetings in Bahasa Indonesia |
| 📊 **Admin Dashboard** | Real-time monitoring, reports, and manual checkout management |
| 👤 **VIP Management** | Pre-register important guests with face data for instant recognition |
| 📱 **Responsive Design** | Works on desktop, tablet, and kiosk displays |

---

## 🏗️ Tech Stack

```
Frontend:   React 19 + Inertia.js 2.0 + TailwindCSS 4.0
Backend:    Laravel 11 + SQLite
AI/ML:      face-api.js (SSD MobileNetV1 + Face Recognition ResNet)
Build:      Vite 7
```

---

## 🚀 Quick Start

### Prerequisites

- **PHP** ≥ 8.2 with SQLite extension
- **Composer** ≥ 2.x
- **Node.js** ≥ 18
- **Git**

### Installation

```bash
# Clone the repository
git clone https://github.com/Ozan1123/tesxa.git devacto-faceid
cd devacto-faceid

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Setup environment
copy .env.example .env
php artisan key:generate

# Create database & run migrations
# Windows CMD:
type nul > database\database.sqlite
# PowerShell:
# New-Item database\database.sqlite -ItemType File -Force

php artisan migrate

# Create storage symlink
php artisan storage:link

# Start the application
composer run dev
```

Open **http://127.0.0.1:8000** in your browser.

---

## 📖 Usage

### Guest Scanner (Main Page)

1. Click **"Mulai Sistem"** to activate the camera
2. Face the camera — the AI will detect and analyze your face
3. **New Guest**: Fill in name, purpose, and guest type → Submit
4. **Returning Guest**: Name auto-fills, select purpose → Check In

### Admin Dashboard

| Route | Description |
|-------|-------------|
| `/admin/monitoring` | Live view of active visits, force checkout |
| `/admin/vip` | Pre-register VIP guests with face data |
| `/admin/reports` | Visit history and reports |

### Verification Page

- `/registered-guests` — Standalone face verification kiosk mode

---

## 🧠 How It Works

```
┌─────────────┐     ┌──────────────┐     ┌───────────────┐
│   Camera     │────▶│  SSD Model   │────▶│  Face Match   │
│   1280x720   │     │  Detection   │     │  (0.55 thr)   │
└─────────────┘     └──────────────┘     └───────┬───────┘
                                                  │
                                    ┌─────────────┴──────────────┐
                                    │                            │
                              ┌─────▼─────┐              ┌──────▼──────┐
                              │  Unknown   │              │   Known     │
                              │  → Register│              │  → Check-in │
                              └───────────┘              └─────────────┘
```

**Detection Pipeline:**
1. Camera captures 1280×720 HD video feed
2. **SSD MobileNetV1** detects faces (supports glasses & partial occlusions)
3. **68-point Landmark Detection** maps facial features
4. **ResNet Face Recognition** generates 128-dimension face descriptor
5. **Multi-frame averaging** (5 frames) stabilizes descriptor for registration
6. **FaceMatcher** compares against database (Euclidean distance, threshold: 0.55)

---

## 📁 Project Structure

```
devacto-faceid/
├── app/
│   ├── Http/Controllers/
│   │   └── GuestController.php    # All guest & visit logic
│   └── Models/
│       ├── Guest.php              # Guest model with face descriptor
│       └── Visit.php              # Visit tracking model
├── resources/
│   ├── js/
│   │   ├── Hooks/
│   │   │   └── useFaceApi.js      # Face detection React hook
│   │   ├── Pages/
│   │   │   ├── Scanner/
│   │   │   │   ├── Index.jsx      # Main scanner page
│   │   │   │   └── Verification.jsx # Verification kiosk
│   │   │   └── Admin/
│   │   │       ├── Monitoring.jsx # Live dashboard
│   │   │       ├── VIP.jsx        # VIP management
│   │   │       └── Reports.jsx    # Visit reports
│   │   └── Layouts/
│   │       └── GuestLayout.jsx    # Base layout
│   └── views/
│       └── main.blade.php         # Legacy Blade scanner
├── public/
│   ├── models/                    # AI model weights
│   │   ├── ssd_mobilenetv1_model-*
│   │   ├── face_recognition_model-*
│   │   ├── face_landmark_68_model-*
│   │   └── age_gender_model-*
│   └── js/
│       └── face-api.min.js        # face-api.js library
├── routes/
│   └── web.php                    # All routes (web + API)
└── database/
    └── migrations/                # Database schema
```

---

## 🔧 Configuration

### Face Detection Tuning

Edit `resources/js/Hooks/useFaceApi.js`:

| Parameter | Default | Description |
|-----------|---------|-------------|
| `minConfidence` | `0.5` | Minimum detection confidence (lower = more faces detected) |
| `FaceMatcher threshold` | `0.55` | Match tolerance (higher = more lenient with glasses) |
| Camera resolution | `1280×720` | HD for better face analysis |
| Descriptor buffer | `5 frames` | Number of frames averaged for registration |

### Network Access

To access from other devices on the same network:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

> ⚠️ Camera requires HTTPS for non-localhost access. Use [ngrok](https://ngrok.com) or configure SSL.

---

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'feat: add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the **MIT License** — see the [LICENSE](LICENSE) file for details.

---

<p align="center">
  Built with ❤️ by <strong>Muhamad Fauzan Pratama</strong>
</p>
