````markdown
> WARNING: Documentation status — OUTDATED
>
> This README contains information that may not reflect the current repository state. Many installation and feature claims are under active migration and/or decommissioning. See [documentation/migration-progress.md](documentation/migration-progress.md) and [documentation/implementation-plan.md](documentation/implementation-plan.md) for the current status and actionable steps.
````markdown
# ISP Solution — Network Services Management Platform

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

**ISP Solution** is a comprehensive, extensible platform for managing ISP services, designed around modern Laravel architecture, FreeRADIUS integration, and MikroTik RouterOS API. This project is actively developed under a service-oriented paradigm, documented, and aligned with multi-tenancy and strict network reliability standards.

---

## 🚀 Features at a Glance

- **Framework:** Laravel (PHP)
- **Authentication:** FreeRADIUS for AAA (Authentication, Authorization, Accounting)
- **Network Integration:** MikroTik routers via RouterOS API
- **Database Architecture:** Dual MySQL databases (one for the app, one for FreeRADIUS)
- **Multi-Tenancy:** Supports strict tenant isolation and role-based permissions
- **Modular Service Layer:** All core logic is in `app/Services`, following clean architecture
- **Extensive Documentation:** Contribution and architecture guides are maintained

## ⚡ Quick Start

_Review `INSTALLATION.md` and the **Gemini Development Guide** before proceeding._

### 1. Automated Setup (Recommended)

```bash
# Download and run the installation script
wget https://raw.githubusercontent.com/i4edubd/ispsolution/main/install.sh
chmod +x install.sh
sudo bash install.sh
```
- Installs PHP, MySQL, Redis, Nginx, FreeRADIUS, and all dependencies.
- For full developer install options, see [INSTALLATION.md](INSTALLATION.md).

### 2. Docker Compose

```bash
git clone https://github.com/i4Edu/ispsolution.git
cd ispsolution
cp .env.example .env
make up              # Starts all containers
make install         # Installs dependencies
make migrate         # Sets up application database
```
_Note: Demo data and environment details are in `.env.example`._

---

## 🏗️ Core Architecture

- **Base:** Laravel 12+, PHP 8.3+
- **App DB:** MySQL 8.x
- **RADIUS DB:** Separate MySQL 8.x for FreeRADIUS integration
- **MikroTik API:** Managed through `app/Services/MikroTikService.php`
- **Service Contracts:** All business logic in `app/Services`, with interfaces in `app/Contracts/`
- **Testing:** Tests required for all business logic; see [docs/TESTING.md](docs/TESTING.md)
- **Multi-Tenancy:** Data-per-tenant, permissions and isolation enforced at the service layer
- **Development guide:** See [Mikrotik_Radius_architecture.md](Mikrotik_Radius_architecture.md)  for system overview

---

## 📚 Development Guide

**ALL contributors must:**
- Review the architecture in [Mikrotik_Radius_architecture.md](Mikrotik_Radius_architecture.md) 
- Follow open tasks in `TODO.md`
- Implement all business logic as services under `app/Services`
- Adhere to PSR-12 coding standard and existing code conventions
- Write tests for new logic or bugfixes

Additional documentation:
- [CONTRIBUTING.md](CONTRIBUTING.md)
- [LOCALIZATION_GUIDE.md](LOCALIZATION_GUIDE.md)
- [ROLE_SYSTEM.md](docs/technical/ROLE_SYSTEM.md)

---

## 🔒 Multi-Tenancy & Roles

- 12-level role system
- Data isolation: enforced at service and DB query level
- Permission system: see `ROLE_SYSTEM.md`
- Demo account seeds: see `DemoSeeder` and `.env.example`

---

## ℹ️ Support & Issues

- Documentation Index: [DOCUMENTATION_INDEX.md](DOCUMENTATION_INDEX.md)
- For bugs or feature requests: use [GitHub Issues](https://github.com/i4Edu/ispsolution/issues)
- Installation and configuration help: see [INSTALLATION.md](INSTALLATION.md) and [docs/NETWORK_SERVICES.md](docs/NETWORK_SERVICES.md)

---

## 💡 Project Status

- This repository is **in active development.**
- Claims regarding "feature completeness" or production rollout should be verified against `FEATURE_IMPLEMENTATION_STATUS.md`, the current commit, and open issues.
- If you find incorrect claims or missing features, please open an issue or PR.

---

## 📝 License

MIT License — see [LICENSE](LICENSE) for details.

---

<div align="center">
  Built with ❤️ by the i4Edu ISP Solution Team
</div>
