(() => {
  "use strict";

  const stories = {
    feature: {
      title: "The Birds of Wesleyan and How To Find Them",
      label: "Feature",
    },
    research: {
      title: "Hydroxylation Pattern of Bile Salt Governs Mixed Micelle Architecture and Peptide Association: Insights from Molecular Dynamics",
      label: "Research",
    },
    nohero: {
      title: "The Math Behind LLMs",
      label: "Essay",
    },
  };

  const menuToggle = document.getElementById("menu-toggle");
  const menu = document.getElementById("site-menu");
  const menuIcon = menuToggle.querySelector(".btn-arrow") || menuToggle.querySelector("span:last-child");
  const panel = document.getElementById("selected-story");
  const storyButtons = document.querySelectorAll("button[data-story]");

  function setMenu(open, returnFocus = false) {
    menu.hidden = !open;
    menuToggle.setAttribute("aria-expanded", String(open));
    menuIcon.textContent = open ? "−" : "+";
    if (returnFocus) menuToggle.focus({ preventScroll: true });
  }

  menuToggle.addEventListener("click", () => {
    setMenu(menu.hidden);
  });

  document.addEventListener("keydown", (event) => {
    if (event.key !== "Escape" || menu.hidden) return;
    event.preventDefault();
    setMenu(false, true);
  });

  function selectStory(key) {
    const story = stories[key];
    if (!story) return;
    storyButtons.forEach((button) => {
      button.setAttribute("aria-pressed", String(button.dataset.story === key));
    });

    const isReduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const body = panel.querySelector(".selected-story-body") || panel;

    const applyContent = () => {
      panel.querySelector("h2").textContent = story.title;
      panel.querySelector("p").textContent = story.label;
      const readLink = panel.querySelector("a");
      readLink.setAttribute("href", `?view=${key}`);
      readLink.setAttribute("aria-label", `Read ${story.label.toLowerCase()}: ${story.title}`);
    };

    if (isReduced) {
      applyContent();
    } else {
      body.classList.add("is-switching");
      setTimeout(() => {
        applyContent();
        body.classList.remove("is-switching");
      }, 100);
    }
  }

  storyButtons.forEach((button) => {
    button.addEventListener("click", () => selectStory(button.dataset.story));
  });

  const cardinalElem = document.querySelector(".tree-cardinal");
  if (cardinalElem) {
    cardinalElem.addEventListener("click", () => {
      selectStory("feature");
    });
  }

  const skipLink = document.querySelector(".skip-link");
  skipLink.setAttribute("href", "?view=home#main");
  skipLink.addEventListener("click", (event) => {
    event.preventDefault();
    document.getElementById("main").focus({ preventScroll: true });
  });

  const themeToggle = document.getElementById("theme-toggle");
  if (themeToggle) {
    const icons = {
      system: `<span class="theme-icon" aria-hidden="true"><svg class="icon-system" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></span>`,
      light: `<span class="theme-icon" aria-hidden="true"><svg class="icon-sun" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg></span>`,
      dark: `<span class="theme-icon" aria-hidden="true"><svg class="icon-moon" viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg></span>`
    };

    const labels = {
      system: "Theme: System (matches device). Click for Light theme.",
      light: "Theme: Light (pure white). Click for Dark theme.",
      dark: "Theme: Dark (midnight). Click for System theme."
    };

    const mediaQuery = window.matchMedia("(prefers-color-scheme: light)");

    function getResolvedTheme(setting) {
      if (setting === "system") {
        return mediaQuery.matches ? "light" : "dark";
      }
      return setting;
    }

    function applyTheme(setting, store = true) {
      const resolved = getResolvedTheme(setting);
      document.documentElement.setAttribute("data-theme", resolved);
      document.documentElement.setAttribute("data-theme-setting", setting);
      const meta = document.querySelector('meta[name="theme-color"]');
      if (meta) {
        meta.setAttribute("content", resolved === "light" ? "#ffffff" : "#100e0f");
      }
      themeToggle.innerHTML = icons[setting] || icons.system;
      const label = labels[setting] || labels.system;
      themeToggle.setAttribute("aria-label", label);
      themeToggle.setAttribute("title", label);
      if (store) {
        localStorage.setItem("wesscijo-theme", setting);
      }
    }

    // Initialize state
    const savedSetting = localStorage.getItem("wesscijo-theme") || "system";
    applyTheme(savedSetting, false);

    // Listen for OS scheme change when in system mode
    mediaQuery.addEventListener("change", () => {
      const currentSetting = localStorage.getItem("wesscijo-theme") || "system";
      if (currentSetting === "system") {
        applyTheme("system", false);
      }
    });

    themeToggle.addEventListener("click", () => {
      const currentSetting = localStorage.getItem("wesscijo-theme") || "system";
      let nextSetting;
      if (currentSetting === "system") {
        nextSetting = "light";
      } else if (currentSetting === "light") {
        nextSetting = "dark";
      } else {
        nextSetting = "system";
      }
      applyTheme(nextSetting, true);
    });
  }

  // Reading progress and active chapter map interactions
  window.initReadingInteractions = function() {
    const progressBar = document.getElementById("reading-progress");
    const updateProgress = () => {
      if (!progressBar) return;
      const scrollY = window.scrollY;
      const docHeight = document.documentElement.scrollHeight - window.innerHeight;
      if (docHeight > 0) {
        const pct = Math.min(100, Math.max(0, (scrollY / docHeight) * 100));
        progressBar.style.width = `${pct}%`;
      } else {
        progressBar.style.width = "0%";
      }
    };

    window.removeEventListener("scroll", updateProgress);
    window.addEventListener("scroll", updateProgress, { passive: true });
    window.addEventListener("resize", updateProgress, { passive: true });
    updateProgress();

    const chapterLinks = document.querySelectorAll(".chapter-map a");
    const sections = document.querySelectorAll(".reading-section");
    if (chapterLinks.length && sections.length && "IntersectionObserver" in window) {
      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              const id = entry.target.id;
              chapterLinks.forEach((link) => {
                const href = link.getAttribute("href") || "";
                if (href.endsWith(`#${id}`)) {
                  link.setAttribute("aria-current", "true");
                } else {
                  link.removeAttribute("aria-current");
                }
              });
            }
          });
        },
        { rootMargin: "-15% 0px -65% 0px" }
      );
      sections.forEach((sec) => observer.observe(sec));
    }
  };

  selectStory("feature");
  if (new URLSearchParams(window.location.search).get("menu") === "open") {
    setMenu(true);
    menu.querySelector("a").focus({ preventScroll: true });
  }
})();
