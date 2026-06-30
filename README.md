# Asset Management System

A robust and secure Asset Management System built with CodeIgniter 4. This application is designed to help organizations track, manage, and maintain their physical and digital assets efficiently.

## Core Features

- **Asset Tracking:** Monitor the complete lifecycle of all organizational assets from acquisition to disposal.
- **User Management:** Role-based access control for secure and segregated operations.
- **Reporting & Analytics:** Generate detailed reports on asset status, location, assignments, and maintenance schedules.
- **Modern Interface:** Responsive and intuitive design accessible across various devices and screen sizes.

## System Requirements

- PHP version 8.3 or higher
- Required PHP Extensions:
  - `intl`
  - `mbstring`
  - `json`
  - `pgsql` (for PostgreSQL database connections)
  - `libcurl` (for HTTP requests)

## Installation Guide

1. **Clone the repository:**
   ```bash
   git clone https://github.com/M3PH1569/Awann
   cd Awann
   ```

2. **Install dependencies:**
   Ensure you have Composer installed, then run:
   ```bash
   composer install
   ```

3. **Environment Configuration:**
   - Copy the example environment file:
     ```bash
     cp env .env
     ```
   - Open `.env` and configure your specific settings. Pay special attention to:
     - `app.baseURL`
     - Database configuration (`database.default.hostname`, `database.default.database`, `database.default.username`, `database.default.password`)

4. **Database Setup:**
   Run the database migrations and seeders (if applicable) to initialize your database structure:
   ```bash
   php spark migrate
   ```

## Web Server Configuration

For security reasons, this application requires the web server document root to point to the `public` folder, **not** the project root directory. 

- **Apache:** Ensure your VirtualHost `DocumentRoot` points to `/path/to/AssetManagement/public`.
- **Nginx:** Ensure your server block `root` points to `/path/to/AssetManagement/public`.

Never expose the project root to the public web.

## Support and Issue Tracking

To report bugs, request features, or seek assistance, please use the GitHub Issues section of this repository. Provide as much detail as possible to help us address your concerns promptly.

## Docker Images

- [Assets Management (Awann)](https://hub.docker.com/r/choss69/awann)

## License

Please refer to the [LICENSE](LICENSE) file in the repository root for information regarding the licensing of this project.
