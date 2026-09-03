# External MySQL Providers for Render Deployment

Since Render's managed database (`pserv`) only supports PostgreSQL, you'll need an external MySQL provider. Here are free/cheap options:

## Free Tier Options

| Provider         | Free Tier                      | Connection Format                                                       | Notes                                                  |
| ---------------- | ------------------------------ | ----------------------------------------------------------------------- | ------------------------------------------------------ |
| **PlanetScale**  | 1 DB, 5GB storage, 1B reads/mo | `aws.connect.psdb.cloud` / `username:password@host/db?sslaccept=strict` | MySQL-compatible, serverless, branching                |
| **Railway**      | $5/mo credit (covers small DB) | `mysql.railway.internal:3306` or public URL                             | Easy setup, good DX                                    |
| **Aiven**        | 1GB storage, 1 CPU             | `mysql-xxxx.aivencloud.com:port`                                        | Managed MySQL/PostgreSQL                               |
| **Supabase**     | 500MB PostgreSQL only          | -                                                                       | You mentioned Supabase later - they're PostgreSQL only |
| **TiDB Cloud**   | 5GB storage, serverless        | `gateway01.xxx.tidbcloud.com:4000`                                      | MySQL-compatible, HTAP                                 |
| **Clever Cloud** | 256MB free                     | `bxxxxxxx.cleverdb.com:port`                                            | European hosting                                       |

## Quick Setup: PlanetScale (Recommended for MySQL)

1. **Sign up** at [planetscale.com](https://planetscale.com) (GitHub OAuth)
2. **Create database** → Name: `corporate-site` → Region: closest to you
3. **Get credentials**: Dashboard → Connect → "Create new password" → Select "General" / "PHP (PDO)"
4. **Copy credentials**:
   ```
   Host: aws.connect.psdb.cloud (or similar)
   Username: xxxxxxxxx
   Password: pscale_xxxxxxxxx
   Database: corporate_site
   Port: 3306 (SSL required)
   ```

## Quick Setup: Railway

1. **Sign up** at [railway.app](https://railway.app)
2. **New Project** → "Provision MySQL"
3. **Get credentials**: MySQL service → Variables → Copy `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`

## After Getting Credentials: Set in Render Dashboard

1. Deploy web service via Blueprint (it will fail init-db.php without DB - that's OK)
2. Go to Render Dashboard → `meridian-web` → **Environment**
3. Add these environment variables:
   ```
   DB_HOST = your-host (e.g., aws.connect.psdb.cloud)
   DB_PORT = 3306
   DB_NAME = corporate_site
   DB_USER = your-username
   DB_PASS = your-password
   DB_CHARSET = utf8mb4
   ```
4. **Manual Deploy** → "Clear build cache & deploy" to re-run `init-db.php`

## For PlanetScale Specifically (SSL Required)

PlanetScale requires SSL. The current `config.php` uses PDO MySQL which supports SSL via DSN. Update `config.php` if needed:

```php
// In getDbConnection(), add SSL options for PlanetScale:
$options = [
    // ... existing options ...
    PDO::MYSQL_ATTR_SSL_CA => '/etc/ssl/certs/ca-certificates.crt', // Usually works auto
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
];
```

Most providers work without extra config. PlanetScale may need the CA cert path.

## Alternative: Use Render PostgreSQL (Switch Now)

If you want to avoid external providers, switch to PostgreSQL:

1. Change `render.yaml` to use `pserv` with PostgreSQL 16
2. Convert `schema.sql` to PostgreSQL syntax
3. Update `config.php` to use `pgsql` driver
4. Update `Dockerfile.render` to install `pdo_pgsql`

This is what I recommended earlier - it's the cleanest for Render native deployment.

---

## Recommendation

**For immediate deploy with MySQL**: Use **PlanetScale** (generous free tier, MySQL-compatible, serverless)

**For long-term (since you mentioned Supabase)**: Switch to **PostgreSQL on Render** now, then migrate to Supabase later by just changing env vars.

Let me know which path you prefer and I'll help with the next steps!
