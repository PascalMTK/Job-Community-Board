// Settings panel toggle
const settingsBtn = document.getElementById("settingsBtn");
const settingsPanel = document.getElementById("settingsPanel");

settingsBtn.addEventListener("click", () => {
   settingsPanel.classList.toggle("active");
});

// Dark mode toggle
const darkModeToggle = document.getElementById("darkModeToggle");
const darkModeLabel = document.getElementById("darkModeLabel");

darkModeToggle.addEventListener("click", () => {
   document.body.classList.toggle("dark-mode");
   darkModeToggle.classList.toggle("active");

   if (document.body.classList.contains("dark-mode")) {
      darkModeLabel.textContent = "On";
      localStorage.setItem("theme", "dark");
   } else {
      darkModeLabel.textContent = "Off";
      localStorage.setItem("theme", "light");
   }
});

// Remember user theme
if (localStorage.getItem("theme") === "dark") {
   document.body.classList.add("dark-mode");
   darkModeToggle.classList.add("active");
   darkModeLabel.textContent = "On";
}

// Language selection (basic example)
document.getElementById("languageSelect").addEventListener("change", (e) => {
   const lang = e.target.value;
   alert(`Language switched to: ${lang}`);
});
