const registerForm = document.querySelector("#register-form");
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

if (registerForm) {
  registerForm.addEventListener("submit", async (event) => {
    event.preventDefault();
    const mobile = registerForm.elements.mobile.value;
    const password = registerForm.elements.password.value;
    const confirmPassword = registerForm.elements.confirmPassword.value;
    const withdrawalPassword = registerForm.elements.withdrawalPassword.value;
    const invitationCode = registerForm.elements.invitationCode.value;

    if (mobile.length !== 10 || isNaN(parseInt(mobile))) {
      showAlertMessage1("Invalid Mobile Number, Re-Enter!");
      return;
    }

    if (
      password.length < 8 ||
      /\s/.test(password) ||
      withdrawalPassword.length < 8 ||
      /\s/.test(withdrawalPassword)
    ) {
      showAlertMessage1(
        "Invalid password! Passwords should be at least 8 characters long and should not contain any spaces."
      );
      return;
    }

    if (password !== confirmPassword) {
      showAlertMessage1("Confirm Password does not match.");
      return;
    }

    if (withdrawalPassword === confirmPassword) {
      showAlertMessage1(
        "Withdrawal password cannot be the same as Account password."
      );
      return;
    }

    const registrationData = {
      mobile: mobile,
      password: password,
      withdrawalPassword: withdrawalPassword,
      invitationCode: invitationCode,
    };

    try {
      const registerResponse = await fetch("php/register.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(registrationData),
      });

      const responseJson = await registerResponse.json();

      if (responseJson.success) {
        showAlertMessage1("", true); // Remove danger message
        alertDiv2.classList.remove("no-content");
        showAlertMessage2("Registration Successful!", true);
        setTimeout(() => {
          window.location.href = "login.html";
        }, 3000);
      } else {
        showAlertMessage1(responseJson.message);
      }
    } catch (error) {
      console.error(error);
      showAlertMessage1("", true); // Remove danger message
      alertDiv2.classList.remove("no-content");
      showAlertMessage2("Registration Successful!", true); //THIS MAY ALSO BE "FAILED TO SAVE REGRATION DATA" ERROR
      setTimeout(() => {
        window.location.href = "login.html";
      }, 3000);
    }
  });
}
