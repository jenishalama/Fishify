document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("contactForm");

  if (!form) return;

  form.addEventListener("submit", function (e) {
    e.preventDefault();

    // Get values
    const name = form.querySelector("input[type='text']").value.trim();
    const email = form.querySelector("input[type='email']").value.trim();
    const message = form.querySelector("textarea").value.trim();

    // Basic validation
    if (name === "" || email === "" || message === "") {
      alert("Please fill in all required fields.");
      return;
    }

    // Email format check
    const emailPattern = /^[^ ]+@[^ ]+\\.[a-z]{2,}$/i;
    if (!emailPattern.test(email)) {
      alert("Please enter a valid email address.");
      return;
    }

    // Success message
    alert("Thank you! Your message has been sent successfully.");

    // Reset form
    form.reset();
  });
});
