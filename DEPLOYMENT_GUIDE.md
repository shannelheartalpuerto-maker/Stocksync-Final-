# StockSync Deployment Guide for Hostinger

This guide will help you deploy your Laravel application to Hostinger Shared Hosting.

## Prerequisites
1.  **Hostinger Account** with a hosting plan.
2.  **Domain Name** connected to your hosting.
3.  **Local Project Files** (the ones on your computer).

---

## Step 1: Prepare the Database
1.  **Export Local Database**:
    *   Open **phpMyAdmin** on your local computer (usually `http://localhost/phpmyadmin`).
    *   Select your database (`stocksync`).
    *   Click the **Export** tab.
    *   Click **Go** to download the `.sql` file.

2.  **Create Hostinger Database**:
    *   Log in to **Hostinger hPanel**.
    *   Go to **Databases** -> **Management**.
    *   Create a New MySQL Database:
        *   **Database Name**: (e.g., `u123456789_stocksync`)
        *   **MySQL Username**: (e.g., `u123456789_admin`)
        *   **Password**: (Create a strong password and save it!)
    *   Click **Create**.

3.  **Import Database**:
    *   In the same Hostinger Database section, click **Enter phpMyAdmin** next to your new database.
    *   Click the **Import** tab.
    *   Upload the `.sql` file you exported from your computer.
    *   Click **Go**.

---

## Step 2: Upload Files (IMPORTANT)
**Do NOT use the "Import Website" or "Migrate Website" tool.** The screenshot you sent shows you are using an automated importer, which often fails with Laravel because it doesn't see an `index.php` in the root folder.

**You MUST use the "File Manager" instead.**

1.  **Zip Your Project Correctly**:
    *   Open your project folder `StockSync Final - FDD`.
    *   **Select ALL files inside** (app, bootstrap, config, public, .env, vendor, etc.).
    *   Right-click -> **Send to** -> **Compressed (zipped) folder**.
    *   *Tip: Do not zip the parent folder itself. Zip the contents.*

2.  **Upload via File Manager**:
    *   Log in to **Hostinger hPanel**.
    *   Click on **Files** -> **File Manager**.
    *   Navigate to: `domains` -> `yourdomain.com` -> `public_html`.
    *   **Delete** any existing default files (like `default.php`).
    *   Click the **Upload** icon (up arrow) in the top right.
    *   Select your new `.zip` file.
    *   Once uploaded, right-click the zip file and choose **Extract**.
    *   *Ensure the files are extracted directly into `public_html`, not a subfolder.*

---

## Step 3: Configure Environment (.env)
1.  In Hostinger File Manager, find the `.env` file.
2.  Right-click and **Edit**.
3.  Update the following lines with your Hostinger details:

    ```env
    APP_NAME=StockSync
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://yourdomain.com

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=u123456789_stocksync  <-- Your Hostinger Database Name
    DB_USERNAME=u123456789_admin      <-- Your Hostinger Username
    DB_PASSWORD=your_strong_password  <-- Your Hostinger Password
    ```

4.  **Save** the file.

---

## Step 4: Point Domain to Public Folder
Laravel serves files from the `public` folder, not the root. You have two options:

### Option A: Change Document Root (Recommended)
1.  Go to **Hostinger hPanel** -> **Websites**.
2.  Find your website and look for **Document Root** settings (sometimes under "Advanced").
3.  Change the path from `public_html` to `public_html/public`.
4.  Save.

### Option B: Use .htaccess (If Option A is not available)
I have already added a `.htaccess` file to your project root. This file automatically redirects visitors to the `public` folder.
*   Ensure the `.htaccess` file is present in your `public_html` folder after uploading.

---

## Step 5: Final Checks
1.  **Storage Permission**:
    *   In File Manager, right-click the `storage` folder.
    *   Select **Permissions**.
    *   Ensure it is set to `775` (or `755`) and check "Recursive".
2.  **Symlink**:
    *   If your product images are not showing, you may need to recreate the storage link.
    *   In hPanel, go to **Advanced** -> **Cron Jobs**.
    *   Create a "Custom" cron job to run once:
        `ln -s /home/u123456789/domains/yourdomain.com/public_html/storage/app/public /home/u123456789/domains/yourdomain.com/public_html/public/storage`
    *   (Replace the paths with your actual server paths shown in File Manager).
    *   Alternatively, enable SSH access in Hostinger and run `php artisan storage:link`.

## Troubleshooting
*   **500 Error**: Check `storage/logs/laravel.log` for details. Ensure `.env` database credentials are correct.
*   **403 Forbidden**: Ensure your permissions are correct (Folders: 755, Files: 644).
