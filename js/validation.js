document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("contactForm");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(form);

    const name = formData.get("name").trim();
    const email = formData.get("email").trim();
    const message = formData.get("message").trim();

    if (!name || !email || !message) {
      Swal.fire({
        icon: "error",
        title: "Campos obligatorios",
        text: "Por favor completá todos los campos requeridos.",
      });
      return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      Swal.fire({
        icon: "error",
        title: "Correo inválido",
        text: "Por favor ingresá un correo electrónico válido.",
      });
      return;
    }

    const sendButton = form.querySelector(".send");
    sendButton.disabled = true;
    sendButton.textContent = "Enviando...";

    try {
      const response = await fetch("email.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (response.ok && result.status === "success") {
        Swal.fire({ icon: "success", title: "Mensaje enviado" });
        form.reset();
      } else {
        throw new Error(result.message || "Error desconocido");
      }
    } catch (error) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Hubo un problema inesperado al enviar el mensaje. Por favor intentá más tarde.",
      });
      console.error("Error:", error);
    } finally {
      sendButton.disabled = false;
      sendButton.textContent = "Enviar";
    }
  });
});
