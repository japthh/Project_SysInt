


//___________________________________________________________________________________________________


const studentTable = document.getElementById("studentTable");   //gamiton sa output na variable baiii

function loadStudents(){                                        //load the fetched json into html
    fetch("http://localhost/htjap/students.php")
    .then(response => {
        if (!response.ok) throw new Error(`Request failed: ${response.status}`);
        return response.json();
    })
    .then(data => {let output = "";
        data.forEach(students =>{
            output+=`
            <tr>
                <td>${students.ID}</td>
                <td>${students.Fullname}</td>
                <td>${students.Course}</td>
                <td>
                    <button onclick="viewStudents(${students.ID})">View</button>
                    <button onclick="deleteStudents(${students.ID})">Delete</button>
                </td>
            </tr>`;
        });
        document.getElementById("studentTable").innerHTML = output; //diri nganiiii
    })
    .catch(error => console.error("Unable to load students:", error));
}
loadStudents();

function viewStudents(ID){
    fetch(`http://localhost/htjap/student.php?ID=${ID}`)//call student.php because it can specify ID not ALL
        .then(response => response.json())
        .then(students => {
            //display bai
            document.getElementById("studentsDetails").innerHTML = `
            <h2>Student Information</h2>
            <p><strong>ID:</strong> ${students.ID}</p>
            <p><strong>Name:</strong> ${students.Fullname}</p>
            <p><strong>Course:</strong> ${students.Course}</p>`;
        })
        .catch(error=>{
            console.error("Error:", error);
        });
}

//___________________________________________________________________________________________________

const form = document.getElementById("studentForm");
const message = document.getElementById("message");

form.addEventListener("submit", async function(event) {//detects form submission
        event.preventDefault();                            //prevent page from refreshing
    const fullname = document.getElementById("Fullname").value;
    const course = document.getElementById("Course").value;
    /*_____________________________________________________________*/
    const response = await fetch("addStudent.php",{ //sends data to php // await = wait until php reponse
        method:"POST",
        headers:{
            "Content-Type": "application/json"//tells php data is json
        },
        body: JSON.stringify({ //creates json data
            fullname: fullname, 
            course : course
        })
    })
    /*_____________________________________________________________*/
    const data = await response.json(); //waits until it gets the response
    if(data.success){//if php returns json data
        message.textContent = data.message;
        form.reset();//clears form
        loadStudents();
    }else{
        message.textContent = data.message;
    }
})


//___________________________________________________________________________________________________

async function deleteStudents(ID){
    const confirmed = confirm("Are you sure you want to delete this student?");
    
    if (!confirmed) {
        return;
    } else {   
        const response = await fetch(`deleteStudent.php?ID=${ID}`,{method: "DELETE"});
        const data = await response.json();
        
        if(data.success){
            message.textContent = data.message;
            loadStudents();
        }else{
            message.textContent = data.message;
        }
    }




}