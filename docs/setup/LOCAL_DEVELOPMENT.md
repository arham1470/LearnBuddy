# LearnBuddy — Local Windows Development Setup Guide (XAMPP / Laragon / CLI)

This document provides clear instructions for setting up and running LearnBuddy locally on Windows using either **XAMPP**, **Laragon**, or the **Artisan CLI**.

---

## 1. Prerequisites Verified

* **PHP:** PHP 8.5+ with required extensions:
  * `pdo_mysql`, `curl`, `mbstring`, `openssl`, `sodium`, `zip`, `fileinfo`, `gd`, `intl`, `bcmath`.
* **Composer:** Composer 2.x configured in PATH.
* **Node.js & npm:** Node.js v20+ / v26+, npm 10+.
* **Version Control:** Git on branch `Development`.

---

## 2. Aiven Cloud MySQL & SSL Configuration

LearnBuddy is configured to connect to Aiven Cloud MySQL with mandatory SSL encryption.

### Step A: Obtain the Aiven CA Certificate
1. Log in to your [Aiven Cloud Console](https://console.aiven.io/).
2. Navigate to your MySQL service:
   * **Host:** `mysql-3e258d69-fwvhaqc2992786-b39c.k.aivencloud.com`
   * **Port:** `23001`
   * **Database:** `defaultdb`
   * **Username:** `avnadmin`
3. In the service overview panel, locate **CA Certificate** and click **Download** (or copy the text).
4. Save the file locally as:
   ```
   D:\LearnBuddy\storage\certs\aiven-ca.pem
   ```
   *(Note: `storage/certs/*.pem` is strictly ignored by `.gitignore` and will never be committed to Git).*

### Step B: Configure Local `.env` Secrets
Open your local `.env` file (which is also ignored by Git) and set your database password:

```dotenv
DB_CONNECTION=mysql
DB_HOST=mysql-3e258d69-fwvhaqc2992786-b39c.k.aivencloud.com
DB_PORT=23001
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD="your_actual_aiven_password_here"
MYSQL_ATTR_SSL_CA=storage/certs/aiven-ca.pem
MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=true
```

> [!WARNING]
> Never commit `.env` or paste your database password into chat, logs, or issues.

---

## 3. Verify Database Connectivity Safely

Once you have added your password and certificate to `.env`, run the built-in safe health check:

```powershell
php artisan db:health-check
```

**Expected Output on Success:**
```
Testing database connectivity...
Database Connection: SUCCESSFUL (Driver: mysql)
SSL Encryption: ACTIVE (Cipher: TLS_AES_256_GCM_SHA384)
```

If credentials or certificate path are invalid, the command reports `Database Connection: FAILED` and sanitizes the output without exposing credentials.

---

## 4. Running the Application Locally

### Option 1: Artisan & Vite (Recommended for Development)
In two PowerShell terminal windows:

**Terminal 1 (Backend Server):**
```powershell
php artisan serve
# Application available at http://127.0.0.1:8000
```

**Terminal 2 (Frontend Asset Watcher):**
```powershell
npm run dev
```

### Option 2: Using XAMPP
1. Open the **XAMPP Control Panel**.
2. Start the **Apache** service.
3. Configure an Apache VirtualHost pointing to `D:\LearnBuddy\public`:
   ```apache
   <VirtualHost *:80>
       DocumentRoot "D:/LearnBuddy/public"
       ServerName learnbuddy.test
       <Directory "D:/LearnBuddy/public">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```
4. Add `127.0.0.1 learnbuddy.test` to your `C:\Windows\System32\drivers\etc\hosts` file.

### Option 3: Using Laragon
1. Place or link `D:\LearnBuddy` inside your Laragon `www` directory (or use Laragon's Menu $\to$ Quick App $\to$ Virtual Hosts).
2. Laragon will automatically generate `http://learnbuddy.test`.

---

## 5. Building Frontend Assets

Before deployment or for production preview:
```powershell
npm run build
```
Compiled CSS and JS assets are emitted into `public/build` (which is ignored by Git).
