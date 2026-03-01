# ISP Solution

Simple, accurate summary and corrected links for the ISP Solution repository.

## About

ISP Solution is an open-source Internet Service Provider management platform. It provides modular components for billing, IPAM (IP Address Management), RADIUS integration, and MikroTik Router management. This README has been simplified — full documentation remains in the docs/ directory.

## Quick links

- Documentation: DOCUMENTATION_INDEX.md
- Installation guide: INSTALLATION.md
- Contributing: CONTRIBUTING.md
- Changelog: CHANGELOG.md

## Quick start (recommended: read INSTALLATION.md)

1. Clone the repository

```bash
git clone https://github.com/i4Edu/ispsolution.git
cd ispsolution
```

2. If using Docker (recommended for development):

```bash
cp .env.example .env
make up
make install
# In the app container:
# docker-compose exec app php artisan key:generate
# make migrate
```

3. For detailed or manual installation steps, follow INSTALLATION.md.

## Notes & fixes applied

- Removed outdated claims and milestone counters from this file.
- Replaced external raw URLs that pointed to a different GitHub owner with the repository clone link above.
- This README is intentionally concise; full technical documentation and installation steps remain in the docs/ and INSTALLATION.md files.

## Contributing

See CONTRIBUTING.md for contribution guidelines and code standards.

## License

This project is licensed under the MIT License. See LICENSE for details.


(Updated via automated repository edit by lupael-cloud request.)