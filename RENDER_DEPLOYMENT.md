# Render Deployment Guide

This guide explains how to deploy the Meridian Systems corporate website to Render.

## Prerequisites

1. A [Render account](https://render.com)
2. A GitHub repository with this code
3. Basic understanding of Render services

## Deployment Options

### Option 1: Using render.yaml (Infrastructure as Code) - Recommended

1. **Push your code to GitHub** (already done)

2. **Connect to Render:**
   - Go to [Render Dashboard](https://dashboard.render.com)
   - Click "New +" → "Blueprint"
   - Connect your GitHub repository
   - Render will detect the `render.yaml` file and show the services to create

3. **Review and Deploy:**
   - Review the services: Web Service + MySQL Database
   - Click "Apply" to create both services
   - Render will automatically:
     - Create a managed MySQL database
     - Build the Docker image
     - Run the pre-deploy command (`php init-db.php`) to initialize the schema
     - Deploy the web service

4. **Access your site:**
   - Once deployed, Render provides a URL like `https://meridian-web.onrender.com`
   - The database is automatically connected via environment variables

### Option 2: Manual Setup via Dashboard

If you prefer manual setup:

1. **Create MySQL Database:**
   - Dashboard → New + → PostgreSQL (or MySQL if available)
   - Name: `meridian-db`
   - Database: `corporate_site`
   - User: `db_username`
   - Plan: Starter (free tier available)

2. **Create Web Service:**
   - Dashboard → New + → Web Service
   - Connect your GitHub repo
   - Runtime: Docker
   - Dockerfile Path: `./Dockerfile.render`
   - Build Command: (leave empty - Docker handles it)
   - Start Command: (leave empty - Docker handles it)
   - Pre-Deploy Command: `php init-db.php`

3. **Add Environment Variables:**
   In the Web Service settings, add these environment variables:
   - `APP_DEBUG` = `false`
   - `APP_NAME` = `Ocktova`
   - `SESSION_TIMEOUT_SECONDS` = `1800`
   - `DB_HOST` = (from database service, e.g., `dpg-xxxxx-a.oregon-postgres.render.com`)
   - `DB_PORT` = `3306` (or `5432` for PostgreSQL)
   - `DB_NAME` = `corporate_site`
   - `DB_USER` = `db_username`
   - `DB_PASS` = (from database service)

4. **Deploy!**

## Important Configuration Details

### Database Connection

The `config.php` file automatically reads database credentials from environment variables. Render's managed databases provide these automatically when you link the services in `render.yaml`.

### HTTPS and Sessions

Render terminates SSL at the load balancer. The `config.php` checks for `X-Forwarded-Proto` header to properly set secure session cookies.

### Port Configuration

Render assigns a `PORT` environment variable (typically 10000). The Dockerfile and Apache configuration are set up to listen on this port.

### Health Checks

The service includes a health check at `/index.php`. Render will monitor this endpoint.

## Post-Deployment Steps

1. **Change Demo Credentials:**
   The database initialization creates a demo client:
   - Client ID: `democlient`
   - Password: `Demo@12345`

   **Change or remove this account immediately after deployment!**

2. **Set Up Custom Domain (Optional):**
   - In Render Dashboard → Your Web Service → Settings → Custom Domains
   - Add your domain and configure DNS

3. **Configure Environment Variables for Production:**
   - `APP_DEBUG` = `false` (already set)
   - Consider setting a custom `APP_NAME`

## Troubleshooting

### Database Connection Issues

If the app can't connect to the database:

1. Check the database service is running
2. Verify environment variables in the web service
3. Check logs: Dashboard → Web Service → Logs

### Build Failures

If Docker build fails:

1. Check the build logs in Render Dashboard
2. Ensure `Dockerfile.render` and `apache.render.conf` are in the repo root
3. Verify the `project/` directory structure matches

### Session Issues

If sessions don't persist:

1. Verify `session.cookie_secure` is set correctly (config.php handles this)
2. Check that Render's load balancer passes `X-Forwarded-Proto`

## Local Development with Render-like Environment

To test locally with similar configuration:

```bash
# Copy example env file
cp .env.example .env

# Edit .env with your local database credentials
# Start with docker-compose (uses 'db' as hostname)
docker-compose up -d

# Or use XAMPP/MAMP with localhost
```

## File Structure for Render

```
├── render.yaml              # Render Blueprint configuration
├── Dockerfile.render        # Docker image for Render
├── apache.render.conf       # Apache config for Render
├── init-db.php              # Database initialization script
├── .env.example             # Example environment variables
├── .gitignore               # Git ignore rules
├── docker-compose.yml       # Local development (unchanged)
└── project/                 # Application code (document root)
    ├── index.php
    ├── includes/
    │   └── config.php       # Updated to use env vars
    └── ...
```

## Costs

- **Web Service (Starter):** Free tier available (spins down after inactivity)
- **MySQL Database (Starter):** Free tier available
- **Custom Domains:** Free on Render

For production, consider upgrading to paid plans for:

- Always-on service (no spin-down)
- More resources
- Better performance
- Automated backups

## Security Notes

1. **Never commit `.env` files** - They're in `.gitignore`
2. **Use Render's managed database** - Credentials are injected securely
3. **Keep `APP_DEBUG=false`** in production
4. **Change demo credentials** after first deploy
5. **Use HTTPS** - Render provides free SSL

## Support

- [Render Documentation](https://render.com/docs)
- [Render Community](https://community.render.com)
- Check service logs in Render Dashboard for debugging
