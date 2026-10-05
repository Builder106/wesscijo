document.querySelectorAll(".handlediv").forEach(function (button) {
  button.addEventListener("click", function () {
    const content = document.getElementById(
      button.getAttribute("aria-controls"),
    );
    content.hidden = !content.hidden;
    button.setAttribute("aria-expanded", String(!content.hidden));
  });
});
["screen-options", "help"].forEach(function (name) {
  const button = document.getElementById(name + "-toggle");
  const panel = document.getElementById(name === "help" ? "help-panel" : name);
  button.addEventListener("click", function () {
    panel.hidden = !panel.hidden;
    button.setAttribute("aria-expanded", String(!panel.hidden));
  });
});
document.querySelectorAll("[data-widget]").forEach(function (input) {
  input.addEventListener("change", function () {
    document.getElementById(input.dataset.widget).hidden = !input.checked;
  });
});
document
  .getElementById("dismiss-welcome")
  .addEventListener("click", function () {
    document.getElementById("welcome-panel").hidden = true;
    document.querySelector('[data-widget="welcome-panel"]').checked = false;
  });
document
  .getElementById("menu-toggle")
  .addEventListener("click", function (event) {
    const open = document.body.classList.toggle("menu-open");
    event.currentTarget.setAttribute("aria-expanded", String(open));
  });
document.getElementById("collapse-menu").addEventListener("click", function () {
  document.body.classList.toggle("menu-collapsed");
});
document
  .getElementById("quick-draft")
  .addEventListener("submit", function (event) {
    event.preventDefault();
    document.getElementById("draft-feedback").textContent =
      "Preview only. Drafts are saved in the installed WordPress dashboard.";
  });
