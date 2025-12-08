<p align="center">
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset="public/images/dark-logo.png">
        <source media="(prefers-color-scheme: light)" srcset="public/images/logo.png">
        <img alt="Hostoo Logo" src="public/images/logo.png" width="300">
    </picture>
</p>

# Hostoo - Modern Web Hosting Automation Platform

**Hostoo** is a robust web hosting management system built with **Laravel**. It integrates seamlessly with **CyberPanel** to provide automated hosting provisioning, domain management, and a seamless user experience.

---

## 🌟 Key Features

### For Users
- **Automated Provisioning**: Instant activation of hosting accounts upon subscription.
- **Dashboard**: A clean, intuitive interface to manage services.
- **File Manager**: Web-based file management.
- **Domain Management**: Easy domain connection with dynamic DNS instructions.
- **Database & Email**: Self-service management for MySQL databases and Email accounts.

### For Admins
- **Plan Management**: Create and configure hosting packages.
- **User Oversight**: View and manage user subscriptions.
- **Analytics**: Visual dashboard for revenue and user growth.
- **CyberPanel Integration**: Full control over backend hosting.

---

## 📸 Gallery

### System Overview
A glimpse into the Hostoo interface.

| Landing Page | Dashboard |
|:---:|:---:|
| <img src="public/screenshots/Screenshot%20(1801).png" width="400" alt="Landing"> | <img src="public/screenshots/Screenshot%20(1802).png" width="400" alt="Dashboard"> |

### Features & Admin Panel
| Admin Features | User Tools |
|:---:|:---:|
| <img src="public/screenshots/Screenshot%20(1803).png" width="400" alt="Admin"> | <img src="public/screenshots/Screenshot%20(1804).png" width="400" alt="Tools"> |

### 🌙 Dark Mode
| Sleek & Modern |
|:---:|
| <img src="public/screenshots/Screenshot%20(1815).png" width="800" alt="Dark Mode"> |

<details>
<summary><strong>View More Screenshots</strong></summary>

| | |
|:---:|:---:|
| <img src="public/screenshots/Screenshot%20(1809).png" width="300"> | <img src="public/screenshots/Screenshot%20(1810).png" width="300"> |
| <img src="public/screenshots/Screenshot%20(1811).png" width="300"> | <img src="public/screenshots/Screenshot%20(1812).png" width="300"> |
| <img src="public/screenshots/Screenshot%20(1813).png" width="300"> | <img src="public/screenshots/Screenshot%20(1814).png" width="300"> |

</details>

---

## 🛠️ Tech Stack

- **Framework**: [Laravel 10.x](https://laravel.com)
- **Database**: MySQL
- **Frontend**: Blade Templates, Vanilla CSS, FontAwesome
- **Integration**: CyberPanel API

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.1+
- Composer
- MySQL

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/hostoo.git
   cd hostoo
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Environment Setup**
   Copy `.env.example` to `.env` and configure credentials:
   ```env
   DB_DATABASE=hostoo
   
   CYBERPANEL_URL=https://your-panel-url:8090
   CYBERPANEL_USERNAME=admin
   CYBERPANEL_PASSWORD=your_password
   ```

4. **Run Migrations**
   ```bash
   php artisan migrate
   ```

5. **Serve**
   ```bash
   php artisan serve
   ```

---

## 📄 License

[MIT license](https://opensource.org/licenses/MIT).
