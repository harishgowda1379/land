# Blockchain-Based Land Registration System - Render Deployment

## Overview
This project simulates a blockchain-based land registration system using PHP, PostgreSQL, HTML, CSS, and JavaScript. The system uses SHA-256 hash chaining to track land ownership transfers and validate land records.

## Render Deployment

### Prerequisites
- Render account (free tier available)
- Git repository (GitHub, GitLab, or Bitbucket)

### Deployment Steps

1. **Push to Git Repository**
   ```bash
   git init
   git add .
   git commit -m "Initial land registration system"
   git remote add origin <your-repo-url>
   git push -u origin main
   ```

2. **Create Render Services**
   - Go to [Render Dashboard](https://dashboard.render.com)
   - Click "New +" and select "Web Service"
   - Connect your Git repository
   - Select "Docker" as the runtime
   - Use the provided `render.yaml` configuration

3. **Database Setup**
   - The PostgreSQL database will be automatically created via `render.yaml`
   - Tables will be initialized automatically on first run
   - Sample data will be populated

### Environment Variables
Render automatically sets these environment variables:
- `DB_HOST` - PostgreSQL host
- `DB_PORT` - PostgreSQL port (5432)
- `DB_NAME` - Database name (land_chain)
- `DB_USER` - Database user
- `DB_PASSWORD` - Database password

### Features
- **User Authentication**: Registration and login system
- **Land Registration**: Add new land records
- **Ownership Transfer**: Transfer land ownership with blockchain hashing
- **History Tracking**: View complete transaction history
- **Verification**: Verify land record integrity using blockchain hashes

### Sample Test Data
- Owner: `Alice Johnson`
- Survey Number: `SVY-1001`
- Location: `Block B, Sector 14, Cityview`
- Area: `2500 sq.ft`

### Security Features
- Passwords hashed with `password_hash()`
- Prepared statements prevent SQL injection
- Server-side session checks protect restricted actions
- SHA-256 blockchain hashing for data integrity

### File Structure
```
land_project/
|-- css/
|   `-- style.css              # UI styling
|-- js/
|   `-- script.js              # Client-side validation
|-- *.html                     # Frontend pages
|-- *.php                      # Backend handlers
|-- Dockerfile                 # Render container configuration
|-- render.yaml                # Render service configuration
|-- db_production.php          # Production database connection
|-- .gitignore                 # Git ignore rules
`-- README_RENDER.md           # This file
```

### Accessing Your Application
Once deployed, your application will be available at:
`https://your-app-name.onrender.com`

### Troubleshooting
- If database connection fails, the system falls back to file-based storage
- Check Render logs for deployment issues
- Ensure all PHP files are included in the Git repository
- Verify Docker build completes successfully

### Notes
- The system uses PostgreSQL on Render (not MySQL)
- File-based storage is available as fallback
- Sample data is automatically populated
- The application works on Render's free tier
