// This file controls the student dashboard page.
const studentTable = document.getElementById("studentTable");
const form = document.getElementById("studentForm");
const message = document.getElementById("message");

// Fetch the list of students from the backend and display them in the table.
function loadStudents() {
    fetch("students.php")
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || "Unable to load student records");
            }
            if (!Array.isArray(data)) {
                throw new Error("The server returned an invalid student list");
            }
            return data;
        })
        .then(data => {
            let output = "";
            data.forEach(student => {
                output += `
                    <tr>
                        <td>${student.ID}</td>
                        <td>${student.Fullname}</td>
                        <td>${student.Course}</td>
                        <td>
                            <button onclick="viewStudents(${student.ID})">View</button>
                            <button onclick="deleteStudents(${student.ID})">Delete</button>
                        </td>
                    </tr>`;
            });
            studentTable.innerHTML = output;
        })
        .catch(error => {
            studentTable.innerHTML = "";
            message.textContent = `Unable to load student records: ${error.message}`;
        });
}

// Load the student list right away when the page opens.
loadStudents();

// Get one student by ID and show the student details in the page.
function viewStudents(ID) {
    fetch(`student.php?ID=${ID}`)
        .then(response => response.json())
        .then(student => {
            document.getElementById("studentsDetails").innerHTML = `
                <h2>Student Information</h2>
                <p><strong>ID:</strong> ${student.ID}</p>
                <p><strong>Name:</strong> ${student.Fullname}</p>
                <p><strong>Course:</strong> ${student.Course}</p>`;
        });
}

// Submit the add-student form and send the new data to the server.
form.addEventListener("submit", async function(event) {
    event.preventDefault();

    const fullname = document.getElementById("Fullname").value;
    const course = document.getElementById("Course").value;

    const response = await fetch("addStudent.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ fullname, course })
    });

    const data = await response.json();

    if (data.success) {
        message.textContent = data.message;
        form.reset();
        loadStudents();
    } else {
        message.textContent = data.message;
    }
});

// Delete a student after confirmation and refresh the list.
async function deleteStudents(ID) {
    const confirmed = confirm("Are you sure you want to delete this student?");
    if (!confirmed) return;

    const response = await fetch(`deleteStudent.php?ID=${ID}`, { method: "DELETE" });
    const data = await response.json();

    if (data.success) {
        message.textContent = data.message;
        loadStudents();
    } else {
        message.textContent = data.message;
    }
}