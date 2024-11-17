const loginForm = document.querySelector("#login-form");
const alertDiv1 = document.querySelector("#alert-div1");
const alertMessage1 = document.querySelector("#alert-message1");
const alertDiv2 = document.querySelector("#alert-div2");
const alertMessage2 = document.querySelector("#alert-message2");

const showAlertMessage1 = (message, success = false) => {
  alertDiv1.classList.remove("no-content");
  alertDiv1.classList.remove(success ? "alert-danger" : "alert-success");
  alertDiv1.classList.add(success ? "alert-success" : "alert-danger");
  alertMessage1.textContent = message;
};

const showAlertMessage2 = (message, success = false) => {
  alertDiv2.classList.remove("no-content");
  alertDiv2.classList.remove(success ? "alert-danger" : "alert-success");
  alertDiv2.classList.add(success ? "alert-success" : "alert-danger");
  alertMessage2.textContent = message;
};

if (alertMessage1.innerHTML.trim() === "") {
  alertDiv1.classList.add("no-content");
}

if (alertMessage2.innerHTML.trim() === "") {
  alertDiv2.classList.add("no-content");
}

if (loginForm) {
  loginForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    const mobile = loginForm.elements.mobile.value;
    const password = loginForm.elements.password.value;

    if (mobile.length !== 10 || isNaN(parseInt(mobile))) {
      showAlertMessage1("Invalid Mobile Number, Re-Enter!");
      return;
    }

    const loginData = {
      mobile: mobile,
      password: password,
    };

    try {
      const loginResponse = await fetch("php/login.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(loginData),
      });

      const data = await loginResponse.json();

      if (data.success) {
        showAlertMessage2("Login Successful!", true);
        alertDiv1.classList.add("no-content"); // Hide alertDiv1
        setTimeout(() => {
          window.location.href = "mainpage.html";
        }, 3000);
      } else {
        showAlertMessage1(data.message);
      }
    } catch (error) {
      console.error(error);
      showAlertMessage1("Cannot Login");
    }
  });
}
