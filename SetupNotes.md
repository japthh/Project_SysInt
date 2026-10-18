# Local setup

## 1. Configure Apache

In XAMPP, open `C:\xampp\apache\conf\extra\vhost.conf` and add this virtual host. If Apache already has a `Listen 9090` directive, do not add a duplicate.

```apache
Listen 9090

<VirtualHost *:9090>
    DocumentRoot "C:\Users\admin\Desktop\jap\Project_SysInt"
    <Directory "C:\Users\admin\Desktop\jap\Project_SysInt">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Make sure Apache includes this virtual-host configuration file, then restart Apache from the XAMPP Control Panel.

## 2. Set up the database

1. Start MySQL from the XAMPP Control Panel.
2. Open phpMyAdmin and create a database named `school`.
3. Select `school`, choose **Import**, and import `school.sql`.

The PHP files connect to the `school` database using the local `root` account with no password, as configured in `db.php`. Update `db.php` if your MySQL credentials differ.

## 3. Open and use the app

Open the app through Apache at:

`http://localhost:9090/login.html`

Do not open `index.html` directly from File Explorer. The student API requests use relative URLs, so they must be served from the same Apache origin as the page. Sign in before opening the student dashboard; the records endpoints require an authenticated session.

## Troubleshooting

- If the page does not load, confirm Apache is running, the virtual-host file is included, and port `9090` is not already in use.
- If login or records fail, confirm MySQL is running, the `school` database was imported, and the configured database credentials are correct.
- If the dashboard shows an authentication error, sign in again at the login page.
- If the records table is blank, check the browser's Developer Tools **Network** tab for the response from `students.php`. The dashboard displays an error message if the request fails.
