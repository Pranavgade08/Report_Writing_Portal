# EventColleges (PHP + MySQL)

A simple college event portal built with **PHP**, **MySQL**, and **XAMPP**.

It supports:

- Public event browsing and filtering
- Event details page with photo gallery + photo captions
- Admin panel to manage departments, academic years, events, and event photos
- “Featured Events” on the home page
- Event PDF report generation from the event page

---

## Requirements

- Windows
- XAMPP (Apache + MySQL)

---

## Installation (XAMPP)

1. Copy this project folder into:

   `C:\xampp\htdocs\eventcolleges`

2. Start XAMPP:

- **Apache**: Start
- **MySQL**: Start

3. Open the site:

- `http://localhost/eventcolleges`

---

## Database Setup

### Create DB + Import Schema

1. Open phpMyAdmin:

- `http://localhost/phpmyadmin`

2. Create a database (recommended name):

- `eventcolleges`

3. Import schema:

- Import `database.sql` from the project root

### Notes

- The schema includes:
  - `departments`
  - `academic_years`
  - `events` (includes `featured` column)
  - `event_photos` (includes `caption` column)

---

## Admin Panel

- Admin login page:
  - `http://localhost/eventcolleges/admin/login.php`

### Default credentials

- Username: `admin`
- Password: `admin123`

After login, you can:

- Add/Edit/Delete events
- Mark events as **Featured**
- Upload event photos with **captions**
- Manage departments and academic years

---

## PDF Report (Event Page)

On an event page you can generate a PDF report.

Notes:

- The PDF generation is implemented with **html2canvas + jsPDF**.
- Pagination uses canvas slicing to reduce page-cut issues.

---

## NSC Department Removal (Optional)

Public pages are configured to hide the `NSC` department.

To permanently delete `NSC` from the database using phpMyAdmin:

```sql
DELETE e
FROM events e
JOIN departments d ON d.id = e.department_id
WHERE d.name='NSC' OR d.short_name='NSC';

DELETE FROM departments
WHERE name='NSC' OR short_name='NSC';
```

---

## Troubleshooting

### “Unknown column” errors

- Ensure you imported the latest `database.sql`.
- This project also includes lightweight auto-migrations for missing columns in `includes/db.php`.

### MySQL “Connection refused”

- Confirm MySQL is started in XAMPP.
- Ensure port `3306` (or your configured port) is not blocked.

---

## Project Structure (high level)

- `index.php` - Home page (includes Featured Events)
- `events.php` - Events listing + filters
- `event.php` - Single event view + PDF export
- `admin/` - Admin panel pages
- `includes/` - DB connection, config, header/footer
- `assets/` - CSS/JS
- `uploads/` - Uploaded photos (created automatically)
