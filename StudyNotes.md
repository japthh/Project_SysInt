## Project Overview
This project is a simple student record management system built with PHP, MySQL, HTML, and JavaScript. It supports:

- user login and logout
- displaying a list of students
- viewing a single student record
- adding a new student
- deleting a student

The app is designed as a small web application for practicing CRUD operations and session-based authentication.

---

## Folder Contents and File Notes

| File | Purpose |
|------|---------|
| [index.html](./index.html) | Main authenticated dashboard. Checks session status via auth.php, redirects to login if needed, and loads the app JavaScript. |
| [login.html](./login.html) | Login page UI for the application. |
| [style.css](./style.css) | Shared styling for the main app and login page. |
| [script.js](./script.js) | Handles loading students, viewing details, adding a student, and deleting a student from the table. |
| [login.js](./login.js) | Handles login form submission and redirect logic. |
| [db.php](./db.php) | Establishes the MySQL database connection using localhost, root, and the school database. |
| [auth.php](./auth.php) | Session-check helper and logout handler. Also returns JSON login status for frontend use. |
| [login.php](./login.php) | Authenticates a user against the users table and creates a PHP session. |
| [students.php](./students.php) | Fetches all student records from the database and returns them in JSON format. |
| [student.php](./student.php) | Fetches a single student by ID from the database. |
| [addStudent.php](./addStudent.php) | Inserts a new student into the students table using POST JSON input. |
| [deleteStudent.php](./deleteStudent.php) | Deletes a student by ID using a DELETE request. |
| [school.sql](./school.sql) | SQL dump for creating the school database, students table, and users table with sample data. |

---

## Main Application Flow

### 1) Login Flow
- User opens [login.html](./login.html).
- The page loads [login.js](./login.js).
- The script calls `auth.php` to check if the user is already authenticated.
- If already logged in, the user is redirected to [index.html](./index.html).
- When the form is submitted, it sends a POST request to [login.php](./login.php).
- [login.php](./login.php) validates the username and password against the `users` table.
- If valid, it starts a PHP session and stores `user_id` and `username`.
- After success, the browser redirects to the dashboard.

### 2) Dashboard Flow
- [index.html](./index.html) loads a script that calls `auth.php` again.
- If `authenticated` is false, the user is redirected back to [login.html](./login.html).
- If authenticated, it loads [script.js](./script.js).
- [script.js](./script.js) calls [students.php](./students.php) to fetch the student list and display it in the table.

### 3) Student CRUD Operations
- Add Student:
  - form in [index.html](./index.html) submits JSON data to [addStudent.php](./addStudent.php)
  - backend inserts a row into the `students` table
- View Student:
  - JavaScript calls [student.php](./student.php) with an ID parameter
  - backend fetches a single row and displays it below the table
- Delete Student:
  - JavaScript prompts for confirmation and sends a DELETE request to [deleteStudent.php](./deleteStudent.php)
  - backend removes matching record from `students`

---

## Database Structure
The database is named `school`.

### students table
Fields:
- `ID` - integer primary key, auto-increment
- `Fullname` - varchar(100)
- `Course` - varchar(50)

### users table
Fields:
- `id` - integer primary key, auto-increment
- `username` - unique varchar(50)
- `password` - hashed password using PHP password hashing
- `full_name` - varchar(100)
- `created_at` - timestamp

The SQL dump in [school.sql](./school.sql) includes example data:
- default admin user: `admin`
- password hash is stored, not plain text

---

## Function-by-Function Breakdown

### 1) PHP File Definitions

#### [db.php](./db.php)
- `$conn = new mysqli($host, $user, $password, $database);`
  - Creates the database connection using MySQL credentials.
  - This object is used by every PHP file that needs to query the `school` database.
- `if($conn->connect_error){ ... }`
  - Checks whether the database connection failed.
  - If it fails, the script stops execution and prints the MySQL error.

#### [auth.php](./auth.php)
- `require_authenticated_session(): void`
  - Checks whether a session is active using `session_status()`.
  - If not active, it starts a session.
  - It verifies whether `$_SESSION["user_id"]` exists.
  - If not, it returns a JSON error with HTTP status `401` and stops execution.
  - This function protects private API endpoints.
- `if (realpath($_SERVER["SCRIPT_FILENAME"] ?? "") === __FILE__) { ... }`
  - Runs only when the file is executed directly, not when included by another PHP file.
  - Handles both the login status check and logout request.
- Logout logic:
  - `if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_GET["action"] ?? "") === "logout")`
  - Deletes all session variables and destroys the session.
  - Clears the session cookie so the user is fully logged out.

#### [login.php](./login.php)
- `if ($_SERVER["REQUEST_METHOD"] !== "POST") { ... }`
  - Ensures the endpoint only accepts POST requests.
- `$data = json_decode(file_get_contents("php://input"), true);`
  - Reads raw JSON sent by the browser.
- `$username = ...` and `$password = ...`
  - Extracts and trims the submitted login values.
- `if ($username === "" || $password === "") { ... }`
  - Validates whether the fields are empty.
- `SELECT id, username, password FROM users WHERE username = ?`
  - Searches the database for a matching username.
- `password_verify($password, $user["password"])`
  - Compares the submitted password with the stored hash.
- `session_start(); session_regenerate_id(true);`
  - Starts a secure session and regenerates the session ID to reduce fixation attacks.
- `$_SESSION["user_id"] = $user["id"];`
  - Stores the authenticated user in the session.

#### [students.php](./students.php)
- `while($row = $result->fetch_assoc()) { ... }`
  - Loops through each database row returned by the query.
  - Stores each row in the `$students` array.
- `echo json_encode($students);`
  - Converts the student records into JSON so the browser can read them with `fetch()`.

#### [student.php](./student.php)
- `if(!isset($_GET['ID'])) { ... }`
  - Validates that the student ID is included in the URL.
- `$ID = filter_input(INPUT_GET, 'ID', FILTER_VALIDATE_INT);`
  - Ensures the ID is a valid integer.
- `$stmt = $conn->prepare("SELECT * FROM students WHERE ID = ?");`
  - Uses a prepared statement to prevent SQL injection.
- `$stmt->bind_param("i", $ID);`
  - Binds the integer value to the SQL query.
- `$result = $stmt->get_result();`
  - Executes the query and returns a result set.

#### [addStudent.php](./addStudent.php)
- `$data = json_decode(file_get_contents("php://input"), true);`
  - Reads the JSON request body sent by the browser.
- `$fullname = trim($data["fullname"] ?? "");`
  - Retrieves and sanitizes the full name.
- `$course = trim($data["course"] ?? "");`
  - Retrieves and sanitizes the course.
- `if ($fullname === "" || $course === "") { ... }`
  - Validates required input before inserting the student.
- `$sql = "INSERT INTO students (Fullname, Course) VALUES (?,?)";`
  - Inserts a record into the student table.
- `mysqli_stmt_bind_param($stmt, "ss", $fullname, $course);`
  - Binds both values as strings.
- `mysqli_stmt_execute($stmt)`
  - Executes the insert query.

#### [deleteStudent.php](./deleteStudent.php)
- `if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') { ... }`
  - Allows only DELETE requests.
- `$ID = $_GET['ID'] ?? "";`
  - Reads the student ID from the query string.
- `$sql = "DELETE FROM students WHERE ID = ?";`
  - Defines the deletion query.
- `mysqli_stmt_bind_param($stmt, "i", $ID);`
  - Binds the student ID as an integer.
- `mysqli_stmt_execute($stmt)`
  - Performs the deletion.
- `mysqli_stmt_affected_rows($stmt)`
  - Checks whether a row was actually removed.

---

### 2) JavaScript Function Definitions

#### [script.js](./script.js)
- `function loadStudents(){ ... }`
  - Fetches all students from `[students.php](./students.php)`.
  - Converts the JSON response into HTML rows for the table.
  - Uses `studentTable.innerHTML = output` to display the records.
- `function viewStudents(ID){ ... }`
  - Sends a request to `[student.php](./student.php)` with the selected ID.
  - Displays the result in `#studentsDetails`.
  - This is used when the user clicks the View button beside a student.
- `async function deleteStudents(ID){ ... }`
  - Shows a confirmation dialog before deleting a student.
  - Sends a DELETE request to `[deleteStudent.php](./deleteStudent.php)`.
  - Reloads the student list after a successful deletion.
- `form.addEventListener("submit", async function(event) { ... })`
  - Runs when the Add Student form is submitted.
  - Prevents the page from refreshing using `event.preventDefault();`.
  - Reads values from `#Fullname` and `#Course`.
  - Sends them as JSON to `[addStudent.php](./addStudent.php)`.
  - Displays the result message and refreshes the table.
- `document.getElementById("studentTable")`
  - Selects the table body where student rows are inserted.
- `document.getElementById("message")`
  - Displays success or error feedback obtained from backend responses.

#### [login.js](./login.js)
- `fetch("auth.php", { cache: "no-store" })`
  - Checks whether the current user is already logged in.
  - If authenticated, it redirects to [index.html](./index.html).
- `loginForm.addEventListener("submit", async event => { ... })`
  - Handles the login form submission.
  - Prevents default form submission.
  - Reads the username and password values.
  - Sends them to `[login.php](./login.php)` as JSON.
  - If login succeeds, it redirects the browser to the dashboard.
- `catch (error)`
  - Catches network or server issues and displays a user-friendly message.

---

### 3) HTML and UI Structure

#### [index.html](./index.html)
- `<form id="studentForm">`
  - Contains the form used to add a new student.
- `<table>`
  - Displays the list of students in rows.
- `<tbody id="studentTable">`
  - Empty container filled by JavaScript with dynamic student rows.
- `<button id="logoutButton">`
  - Logs the user out by sending a request to `auth.php?action=logout`.
- `fetch("auth.php", { cache: "no-store" })`
  - Runs on page load to verify login status before loading the app.
- `window.location.replace("login.html")`
  - Redirects the user if they are not authenticated.

#### [login.html](./login.html)
- `<form id="loginForm" class="login-form">`
  - Accepts username and password input.
- `<input type="password" id="password" ...>`
  - Accepts the user's password securely.
- `<p id="loginMessage">`
  - Displays errors and status messages during login.

---

### 4) CSS Functional Roles

#### [style.css](./style.css)
- `body { ... }`
  - Sets the page font, spacing, width, and background color.
- `.page-header { display: flex; ... }`
  - Formats the top section containing the Student List title and logout button.
- `.login-page { ... }`
  - Centers the login form vertically and horizontally.
- `.login-card { ... }`
  - Styles the login panel container.
- `table { border-collapse: collapse; ... }`
  - Defines how the student table looks and how cells are separated.
- `#message` and `.login-message`
  - Styles success/error text shown to the user.

---

## Important Implementation Notes

### Session Authentication
The auth logic is centralized in [auth.php](./auth.php).
- `require_authenticated_session()` checks whether a valid PHP session exists.
- Any protected API route can call this function before returning data.
- The app uses session-based auth rather than JWT or token-based auth.

### Frontend Fetch Calls
The JavaScript files use fetch requests and expect JSON responses.
- This pattern is useful for simple AJAX-based web apps.
- The app uses `Content-Type: application/json` in requests and responds with JSON in PHP.

### Hardcoded Paths
One important detail in [script.js](./script.js):
- the project uses direct URLs such as `http://localhost/htjap/students.php`
- this may fail if the application is running in a different local folder or server root
- if your project folder is not under `htjap`, you should update these URLs to match your local environment

---

## Suggested Local Setup
1. Start Apache and MySQL.
2. Create a database named `school`.
3. Import [school.sql](./school.sql) into MySQL.
4. Place the project folder in your local web server root, such as:
   - `C:/xampp/htdocs/Project_SysInt`
5. Open the browser and go to:
   - `http://localhost/Project_SysInt/login.html`
6. Login with the seeded admin account from the SQL file if needed.

---

## Security and Improvement Notes
This project is a good beginner-level CRUD app, but there are a few areas to improve:

- validate and sanitize all incoming user data more strictly
- use prepared statements consistently everywhere
- consider proper error handling for database connection issues
- avoid hardcoded localhost paths in JavaScript
- protect against direct access to API files when not authenticated
- add input validation for names and courses

---

## Study Summary
This project teaches the basics of:

- session-based authentication in PHP
- connecting frontend JavaScript to backend PHP APIs
- database CRUD with MySQL
- AJAX fetch requests
- server-client interaction in a full-stack web app

It is a practical example for understanding how a small web application connects HTML, JavaScript, PHP, and a database.

---

## Quick File Relationship Map

```text
login.html
  -> login.js
      -> login.php
          -> users table

index.html
  -> auth.php (session check)
      -> if authenticated, load script.js

script.js
  -> students.php (list all)
  -> student.php (view one)
  -> addStudent.php (create)
  -> deleteStudent.php (delete)

all PHP files
  -> db.php
      -> school database
```

---

## Final Notes
This is a beginner-friendly full-stack project. It is useful for learning how a simple CRUD application is structured and how frontend and backend logic communicate in a PHP + MySQL environment.
