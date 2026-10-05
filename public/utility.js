(() => {
  "use strict";

  const params = new URLSearchParams(window.location.search);
  const view = params.get("view");
  if (!["search", "archive", "about", "calendar", "submit"].includes(view)) return;

  document.body.classList.add("utility-route");

  const stories = [
    {
      slug: "birds-of-wesleyan",
      view: "feature",
      category: "feature",
      label: "Field Feature",
      division: "News, Features & Perspectives",
      title: "The Birds of Wesleyan and How To Find Them",
      date: "October 2026",
      readTime: "8 min read",
      excerpt: "Begin with observation. A field-guided exploration of avian ecology across campus, from campus canopies to the Wadsworth arboretum border."
    },
    {
      slug: "bile-salt-micelles",
      view: "research",
      category: "research",
      label: "Journal Article",
      division: "Research & Reviews",
      title: "Hydroxylation Pattern of Bile Salt Governs Mixed Micelle Architecture and Peptide Association: Insights from Molecular Dynamics",
      date: "October 2026",
      readTime: "12 min read",
      excerpt: "An evidence-led investigation into the structural reorganization, hydrodynamic dimensions, and association kinetics of therapeutic peptides in mixed bile salt assemblies."
    },
    {
      slug: "math-behind-llms",
      view: "nohero",
      category: "essay",
      label: "Perspective Essay",
      division: "News, Features & Perspectives",
      title: "The Math Behind LLMs",
      date: "October 2026",
      readTime: "6 min read",
      excerpt: "A text-first inquiry examining the linear algebra and probabilistic geometry that power modern transformer attention mechanisms."
    },
    {
      slug: "skipping-stones",
      view: "research",
      category: "research",
      label: "Journal Article",
      division: "Research & Reviews",
      title: "The Physics of Skipping Stones, Explained",
      date: "12 August 2026",
      readTime: "5 min read",
      excerpt: "A short original-research piece modeling the ricochet dynamics, hydrodynamic drag, and angle of attack of flat stones on water."
    },
    {
      slug: "crispr-screen",
      view: "research",
      category: "research",
      label: "News & Views",
      division: "Research & Reviews",
      title: "New CRISPR Screen Reveals Drug-Resistance Pathway",
      date: "12 August 2026",
      readTime: "4 min read",
      excerpt: "Summarizing a recent high-impact paper on a newly identified genetic resistance mechanism in cancer cell lines under targeted therapy."
    },
    {
      slug: "exoplanet-detection",
      view: "research",
      category: "research",
      label: "Literature Review",
      division: "Research & Reviews",
      title: "A Century of Exoplanet Detection: What We’ve Learned",
      date: "12 August 2026",
      readTime: "7 min read",
      excerpt: "A comprehensive review of astronomical detection methods from radial velocity to transit photometry, and what lies ahead for atmospheric spectroscopy."
    },
    {
      slug: "zebrafish-circuits",
      view: "research",
      category: "research",
      label: "Journal Article",
      division: "Research & Reviews",
      title: "Mapping Neural Circuits in the Wesleyan Zebrafish Lab",
      date: "12 August 2026",
      readTime: "6 min read",
      excerpt: "Original student research on circuit-level functional mapping of zebrafish larval brains using whole-brain light-sheet fluorescence microscopy."
    },
    {
      slug: "connecticut-river-erosion",
      view: "feature",
      category: "feature",
      label: "Field News",
      division: "News, Features & Perspectives",
      title: "Wesleyan Geologists Publish on Connecticut River Erosion Patterns",
      date: "12 August 2026",
      readTime: "5 min read",
      excerpt: "Coverage of a new field study tracking decade-long sediment shifts, riverbank morphology, and fluvial transport along the lower Connecticut River basin."
    },
    {
      slug: "undergrads-coauthors",
      view: "nohero",
      category: "essay",
      label: "Op/Ed",
      division: "News, Features & Perspectives",
      title: "Should Undergrads Be Co-Authors More Often?",
      date: "12 August 2026",
      readTime: "4 min read",
      excerpt: "An argument for structural changes to how academic credit, publication authorship, and intellectual contribution are assigned to undergraduate researchers."
    },
    {
      slug: "computational-biology-lab",
      view: "feature",
      category: "feature",
      label: "Laboratory Feature",
      division: "News, Features & Perspectives",
      title: "Inside Wesleyan’s New Computational Biology Lab",
      date: "12 August 2026",
      readTime: "6 min read",
      excerpt: "A behind-the-scenes look at the faculty-student research group developing deep learning algorithms and structural bioinformatics pipelines for genomics."
    },
    {
      slug: "cs-papers-reproducible",
      view: "nohero",
      category: "essay",
      label: "Perspective",
      division: "News, Features & Perspectives",
      title: "Why Are So Few CS Papers Reproducible?",
      date: "12 August 2026",
      readTime: "5 min read",
      excerpt: "A critical perspective piece on reproducibility standards, artifact evaluation committees, and code availability across computational science publishing."
    }
  ];
  const routes = [
    ["home", "Home"], ["archive", "Archives / issue"], ["search", "Search"],
    ["about", "About"], ["calendar", "Calendar"], ["submit", "Submit"],
  ];
  const titles = { search: "Search", archive: "Archives / issue", about: "About WesSciJo", calendar: "Calendar", submit: "Submit" };
  const categories = [["all", "All"], ["research", "Research"], ["feature", "Features"], ["essay", "Essays"]];
  const category = categories.some(([key]) => key === params.get("category")) ? params.get("category") : "all";
  const query = params.get("q") || "";

  function storyList() {
    return `<ul class="result-list">${stories.map((story) => `<li class="result-item" data-od-id="${view}-story-${story.slug || story.view}">
      <h2><a href="?view=${story.view}" data-od-id="${view}-read-${story.slug || story.view}">${story.title}</a></h2>
      <div class="result-meta">
        <span class="meta-tag">${story.label}</span>
        <span class="meta-sep" aria-hidden="true">•</span>
        <span class="meta-division">${story.division}</span>
        <span class="meta-sep" aria-hidden="true">•</span>
        <span class="meta-date">${story.date}</span>
        <span class="meta-sep" aria-hidden="true">•</span>
        <span class="meta-time">${story.readTime}</span>
      </div>
      <p class="result-excerpt">${story.excerpt}</p>
    </li>`).join("")}</ul>`;
  }

  const pages = {
    search: `<p>Search published research, articles, and essays across the journal.</p>
      <form class="search-form" method="get" data-od-id="search-form">
        <input type="hidden" name="view" value="search">
        <input type="hidden" name="category" value="${category}">
        <label for="story-query">Article title or keyword</label>
        <input id="story-query" name="q" type="search" data-od-id="search-query">
        <button class="primary-button" type="submit" data-od-id="search-submit">Search</button>
      </form>
      <nav class="filter-nav" aria-label="Story categories" id="search-filters" data-od-id="search-filters"></nav>
      <p id="search-status" role="status" aria-live="polite" data-od-id="search-status"></p>
      <div id="search-results" data-od-id="search-results">${storyList()}</div>
      <section class="empty-state" id="search-empty" hidden data-od-id="search-empty">
        <h2>No matching articles</h2><p>Try a shorter title or choose All to search across every category.</p>
        <a class="read-link" href="?view=search" data-od-id="reset-search">Clear search and filters</a>
      </section>`,
    archive: `<p>Browse past issues and published research volumes from the Wesleyan Science Journal archive.</p>
      <div class="archive-volume-header">
        <img class="volume-seal" src="assets/logo-192.png" width="44" height="44" alt="Volume 14 Seal">
        <div>
          <h2>Current Issue: Volume 14</h2>
          <p>Articles published in the current volume are indexed below.</p>
        </div>
      </div>${storyList()}`,
    about: `<p>WesSciJo is Wesleyan University’s undergraduate science journal, showcasing research, field studies, and perspectives across the sciences.</p>
      <div class="about-hero-seal">
        <img class="official-seal" src="assets/logo.png" width="220" height="220" alt="Official Seal of the Wesleyan Science Journal: red cardinal in laboratory safety goggles holding a micropipette.">
        <div class="seal-caption">
          <strong>Official Seal &amp; Insignia</strong>
          <p>The cardinal in safety goggles with micropipette symbolizes student-driven empirical inquiry, laboratory rigor, and field observation across Wesleyan University.</p>
        </div>
      </div>
      <h2>Mission &amp; Scope</h2><p>The Wesleyan Science Journal is an undergraduate-led scientific publication celebrating scholarship, research, and science communication across Wesleyan University. We publish peer-reviewed student research, in-depth scientific features, and interdisciplinary essays spanning biology, chemistry, physics, mathematics, computer science, and earth sciences.</p>
      <figure class="utility-art" data-od-id="about-nest-identity"><img src="assets/nest.svg" width="1500" height="1000" alt="WesSciJo identity motif: a cardinal above a woven nest holding laboratory glassware." loading="lazy" data-od-id="about-nest-artwork"><figcaption>Scientific inquiry grounded in natural observation.</figcaption></figure>
      <h2>Editorial Board &amp; Masthead</h2>
      <p>The journal is edited and produced by undergraduate researchers across Wesleyan University in collaboration with faculty advisors throughout the Natural Sciences and Mathematics Division.</p>

      <section class="masthead-division" data-od-id="masthead-executive">
        <h3 class="masthead-division-title">Executive Leadership</h3>
        <div class="masthead-grid">
          <div class="person-card">
            <div class="person-avatar" aria-hidden="true">AB</div>
            <div class="person-info"><strong class="person-name">Aryia Banihashem-Ahmad</strong><span class="person-role">Editor-in-Chief</span></div>
          </div>
          <div class="person-card">
            <div class="person-avatar" aria-hidden="true">SS</div>
            <div class="person-info"><strong class="person-name">Shriya Sakalkale</strong><span class="person-role">Editor-in-Chief</span></div>
          </div>
          <div class="person-card">
            <div class="person-avatar" aria-hidden="true">EM</div>
            <div class="person-info"><strong class="person-name">Elena Mente</strong><span class="person-role">Editor-in-Chief</span></div>
          </div>
        </div>
      </section>

      <section class="masthead-division" data-od-id="masthead-lifesci">
        <h3 class="masthead-division-title">Life Sciences Division</h3>
        <div class="masthead-grid">
          <div class="person-card"><div class="person-avatar" aria-hidden="true">LH</div><div class="person-info"><strong class="person-name">Lorraine Hillgen-Santa</strong><span class="person-role">Lead Life Science Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">MF</div><div class="person-info"><strong class="person-name">Maia Feik Reinhart</strong><span class="person-role">Lead Life Science Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">FH</div><div class="person-info"><strong class="person-name">Feyza Horuz</strong><span class="person-role">Biology Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">KE</div><div class="person-info"><strong class="person-name">Kitty Edwards</strong><span class="person-role">Biology Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">CF</div><div class="person-info"><strong class="person-name">Claire Farina</strong><span class="person-role">Biology Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">SL</div><div class="person-info"><strong class="person-name">Sophie Lambert</strong><span class="person-role">Assistant Biology Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">SS</div><div class="person-info"><strong class="person-name">Saara Saini</strong><span class="person-role">Assistant Biology Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">HZ</div><div class="person-info"><strong class="person-name">Hannah Zullow</strong><span class="person-role">Neuroscience &amp; Psychology Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">GS</div><div class="person-info"><strong class="person-name">Gaby Sorin</strong><span class="person-role">Neuroscience &amp; Psychology Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">OO</div><div class="person-info"><strong class="person-name">Olivia Oliveira</strong><span class="person-role">Assistant Neuroscience &amp; Psychology Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">RA</div><div class="person-info"><strong class="person-name">Rhea Ashish Kothari</strong><span class="person-role">Assistant Neuroscience &amp; Psychology Editor</span></div></div>
        </div>
      </section>

      <section class="masthead-division" data-od-id="masthead-physical">
        <h3 class="masthead-division-title">Physical Sciences Division</h3>
        <div class="masthead-grid">
          <div class="person-card"><div class="person-avatar" aria-hidden="true">NP</div><div class="person-info"><strong class="person-name">Natalie Price</strong><span class="person-role">Lead Physical Science Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">HH</div><div class="person-info"><strong class="person-name">Hamza Habib</strong><span class="person-role">Lead Physical Science Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">ZH</div><div class="person-info"><strong class="person-name">Zesun Hossain</strong><span class="person-role">Astronomy Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">ES</div><div class="person-info"><strong class="person-name">Ella Stricker</strong><span class="person-role">Physics Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">RA</div><div class="person-info"><strong class="person-name">Rhea Ashish Kothari</strong><span class="person-role">Assistant Physics Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">EG</div><div class="person-info"><strong class="person-name">Ellen Gudiksen</strong><span class="person-role">Chemistry Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">SS</div><div class="person-info"><strong class="person-name">Saara Saini</strong><span class="person-role">Assistant Chemistry Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">AG</div><div class="person-info"><strong class="person-name">Aniana Garciano</strong><span class="person-role">Earth &amp; Environmental Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">ES</div><div class="person-info"><strong class="person-name">Ella Stricker</strong><span class="person-role">Earth &amp; Environmental Editor</span></div></div>
        </div>
      </section>

      <section class="masthead-division" data-od-id="masthead-quant">
        <h3 class="masthead-division-title">Quantitative &amp; Computational Science</h3>
        <div class="masthead-grid">
          <div class="person-card"><div class="person-avatar" aria-hidden="true">SB</div><div class="person-info"><strong class="person-name">Shloka Bhattacharyya</strong><span class="person-role">Lead Quantitative &amp; Computational Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">GC</div><div class="person-info"><strong class="person-name">Gillian Churchland</strong><span class="person-role">Math Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">CC</div><div class="person-info"><strong class="person-name">Calvin Chiu</strong><span class="person-role">Math Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">SS</div><div class="person-info"><strong class="person-name">Sangye Sherpa</strong><span class="person-role">Math Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">SS</div><div class="person-info"><strong class="person-name">Samantha Sheahan</strong><span class="person-role">Assistant Computer Science Editor</span></div></div>
        </div>
      </section>

      <section class="masthead-division" data-od-id="masthead-sts">
        <h3 class="masthead-division-title">Science, Technology &amp; Society</h3>
        <div class="masthead-grid">
          <div class="person-card"><div class="person-avatar" aria-hidden="true">SS</div><div class="person-info"><strong class="person-name">Sangye Sherpa</strong><span class="person-role">Lead Science, Technology &amp; Society Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">TH</div><div class="person-info"><strong class="person-name">Tessa Higgins</strong><span class="person-role">Lead Science, Technology &amp; Society Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">DC</div><div class="person-info"><strong class="person-name">Dahlia Cedarbaum</strong><span class="person-role">Science, Technology &amp; Society Editor</span></div></div>
          <div class="person-card"><div class="person-avatar" aria-hidden="true">ST</div><div class="person-info"><strong class="person-name">Sarah Toolan</strong><span class="person-role">Science, Technology &amp; Society Editor</span></div></div>
        </div>
      </section>`,
    calendar: `<p>Important dates for submissions, editorial cycles, and campus research symposia.</p>
      <ul class="plain-list">
        <li><strong>Fall Submission Deadline:</strong> November 15</li>
        <li><strong>Peer Review &amp; Revisions:</strong> December 1 – January 15</li>
        <li><strong>Spring Print Edition:</strong> April 20</li>
        <li><strong>Wesleyan Undergraduate Research Symposium:</strong> May 1</li>
      </ul>`,
    submit: `<div class="submit-imprint">
        <img class="submit-seal" src="assets/logo-192.png" width="44" height="44" alt="Wesleyan Science Journal Seal">
        <p>We welcome original research papers, literature reviews, scientific essays, and reporting from all Wesleyan undergraduate students.</p>
      </div>
      <h2>Editorial Workflow</h2>
      <ol class="plain-list">
        <li>Prepare your manuscript in standard format (Research Article, Field Feature, or Perspective Essay).</li>
        <li>Provide high-resolution figures and data tables with complete captions, attribution, and methodological details.</li>
        <li>Submit manuscripts and supporting files through the student portal or by contacting the editorial board.</li>
        <li>Submissions undergo peer review by student editors and faculty mentors prior to layout and publication.</li>
      </ol>
      <h2>Figure &amp; Media Guidelines</h2>
      <p>Research articles support full-width data plots and in-line figures adjacent to relevant discussion. All images must be accompanied by comprehensive descriptive captions and appropriate permissions.</p>`,
  };

  document.getElementById("main").innerHTML = `<section class="utility-page" data-od-id="${view}-page"><div class="utility-inner">
    <h1 data-od-id="${view}-title">${titles[view]}</h1>
    ${pages[view]}
    <nav class="filter-nav" aria-label="Journal pages" data-od-id="${view}-journal-routes">
      ${routes.map(([key, label]) => `<a href="?view=${key}" ${key === view ? 'aria-current="page"' : ""} data-od-id="${view}-route-${key}">${label}</a>`).join("")}
    </nav>
  </div></section>`;

  document.title = `${titles[view]} — WesSciJo`;
  document.querySelector(".skip-link").setAttribute("href", `?view=${view}#main`);
  if (view !== "search") return;

  const input = document.getElementById("story-query");
  input.value = query;
  const filters = document.getElementById("search-filters");
  let currentCategory = category;
  const categoryInput = document.querySelector('.search-form input[name="category"]');

  categories.forEach(([key, label]) => {
    const link = document.createElement("a");
    link.textContent = label;
    link.dataset.odId = `search-filter-${key}`;
    if (key === currentCategory) link.setAttribute("aria-current", "page");
    filters.append(link);
  });

  function updateFilterLinks() {
    categories.forEach(([key], i) => {
      const searchParams = new URLSearchParams({ view: "search", category: key });
      if (input.value) searchParams.set("q", input.value);
      filters.children[i].setAttribute("href", `?${searchParams.toString()}`);
    });
  }

  function filterStories() {
    const term = input.value.trim().toLocaleLowerCase();
    let count = 0;
    stories.forEach((story) => {
      const matches = (currentCategory === "all" || currentCategory === story.category) && (story.title.toLocaleLowerCase().includes(term) || (story.excerpt && story.excerpt.toLocaleLowerCase().includes(term)) || (story.label && story.label.toLocaleLowerCase().includes(term)));
      const el = document.querySelector(`[data-od-id="search-story-${story.slug || story.view}"]`);
      if (el) el.hidden = !matches;
      if (matches) count += 1;
    });
    document.getElementById("search-status").textContent = `${count} matching ${count === 1 ? "article" : "articles"}`;
    document.getElementById("search-empty").hidden = count !== 0;
    document.getElementById("search-results").hidden = count === 0;
    updateFilterLinks();
  }

  function setCategory(newCategory, updateHistory = true) {
    if (newCategory === currentCategory) return;
    currentCategory = newCategory;
    if (categoryInput) categoryInput.value = currentCategory;

    Array.from(filters.children).forEach((child, i) => {
      if (categories[i][0] === currentCategory) {
        child.setAttribute("aria-current", "page");
      } else {
        child.removeAttribute("aria-current");
      }
    });

    const resultsEl = document.getElementById("search-results");
    const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    if (!prefersReducedMotion && resultsEl) {
      resultsEl.style.opacity = "0.3";
      resultsEl.style.transform = "translateY(2px)";
      setTimeout(() => {
        filterStories();
        resultsEl.style.opacity = "1";
        resultsEl.style.transform = "translateY(0)";
      }, 90);
    } else {
      filterStories();
    }

    if (updateHistory) {
      const searchParams = new URLSearchParams({ view: "search", category: currentCategory });
      if (input.value) searchParams.set("q", input.value);
      try {
        history.pushState({ category: currentCategory, q: input.value }, "", `?${searchParams.toString()}`);
      } catch {
        // Fallback for file:// or restricted contexts
      }
    }
  }

  filters.addEventListener("click", (e) => {
    const link = e.target.closest("a");
    if (!link) return;
    e.preventDefault();
    const href = link.getAttribute("href") || "";
    const linkParams = new URLSearchParams(href.replace(/^[^?]*\?/, ""));
    const selectedCat = linkParams.get("category") || "all";
    setCategory(selectedCat, true);
  });

  window.addEventListener("popstate", () => {
    const currentParams = new URLSearchParams(window.location.search);
    const popCat = currentParams.get("category") || "all";
    const popQ = currentParams.get("q") || "";
    input.value = popQ;
    const resolvedCat = categories.some(([k]) => k === popCat) ? popCat : "all";
    if (resolvedCat !== currentCategory) {
      setCategory(resolvedCat, false);
    } else {
      filterStories();
    }
  });

  input.addEventListener("input", filterStories);
  filterStories();
})();
