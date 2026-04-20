# Blockchain-Based Land Registration System

## Overview
This project simulates a blockchain-based land registration system using PHP, MySQL, HTML, CSS, and JavaScript. The system uses SHA-256 hash chaining to track land ownership transfers and validate land records.

## Project Files
- `index.html` - Landing page
- `register.html` - User registration page
- `login.html` - Login page
- `dashboard.html` - Main dashboard
- `add_land.html` - Land registration form
- `transfer.html` - Land ownership transfer form
- `history.html` - Land history lookup page
- `verify.html` - Land verification page
- `css/style.css` - Shared UI styling
- `js/script.js` - Basic client-side input validation
- `db.php` - Database connection and helper functions
- `register.php` - User registration handler
- `login.php` - Login authentication handler
- `add_land.php` - Add land and create the initial blockchain transaction
- `transfer.php` - Ownership transfer logic and transaction hashing
- `history.php` - Fetch and display land transaction history
- `verify.php` - Verify survey number integrity and owner details
- `database.sql` - MySQL schema and sample data script

## Database Setup
1. Open XAMPP and start Apache and MySQL.
2. Open phpMyAdmin at `http://localhost/phpmyadmin`.
3. Create a new database named `land_chain` or import `database.sql` directly.
4. If importing directly, run the SQL script from `database.sql`.

## Database Credentials
- The app uses `root` as the MySQL user by default.
- If your MySQL root account uses a password, open `db.php` and set `$pass` to your root password or export `LAND_DB_PASSWORD`.

## Running the Project
1. Copy the `land_project` folder into your XAMPP `htdocs` directory.
   - Example: `C:\xampp\htdocs\land_project`
2. Open the browser and visit:
   - `http://localhost/land_project/index.html`
3. Register a new user using the `Register` page.
4. Login and use the dashboard to add land, transfer ownership, view history, and verify land.

## Sample Test Data
- Preloaded sample land record:
  - Owner: `Alice Johnson`
  - Survey Number: `SVY-1001`
  - Location: `Block B, Sector 14, Cityview`
  - Area: `2500 sq.ft`
- The sample transaction includes the first blockchain hash for validation.

## Notes for Viva
- `hashBlock()` in `db.php` generates SHA-256 hashes using `land_id`, `seller`, `buyer`, `date`, and `previous_hash`.
- The `transactions` table stores `current_hash` and `previous_hash` to simulate blockchain chaining.
- Ownership transfer updates the `lands.owner_name` value and appends a new transaction block.
- The verification module recalculates hashes and compares them to stored values to detect tampering.

## Security Features
- Passwords are hashed with `password_hash()` before storage.
- Prepared statements prevent SQL injection.
- Server-side session checks protect restricted actions.
