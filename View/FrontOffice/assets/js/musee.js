(function () {
  "use strict";

  /* ---------- Configuration ---------- */
  const IMG_BASE = "assets/img/produit/";   // dossier des images
  const IMG_EXT = "jpg";                    // extension par défaut si "img" est absent

  /* ---------- Données par défaut ---------- */
  const DEFAULT_VARIETIES = [
    { name: "Blé dur",         cat: "Céréales", regions: "Tunis, Kairouan",             icon: "bi-flower1",
      desc: "Céréale emblématique de la cuisine tunisienne, base du couscous et des pâtes.", img: "bledur.jpg" },
    { name: "Blé tendre",      cat: "Céréales", regions: "Béja",                        icon: "bi-flower2",
      desc: "Cultivé dans les plaines du nord, utilisé pour le pain et la pâtisserie.", img: "bletend.jpg" },
    { name: "Orge",            cat: "Céréales", regions: "Tunis, Sfax, Béja, Médenine", icon: "bi-flower3",
      desc: "Céréale rustique adaptée aux zones semi-arides, pour l’alimentation humaine et animale.", img: "orge.jpg" },

    { name: "Olivier",         cat: "Oliviers", regions: "Sfax, Médenine",              icon: "bi-tree",
      desc: "Arbre emblématique de la Tunisie, source d’une huile d’olive très réputée.", img: "olivier.jpg" },

    { name: "Agrumes",         cat: "Fruits",   regions: "Tunis",                       icon: "bi-brightness-high",
      desc: "Oranges, citrons et clémentines cultivés dans la région de Tunis.", img: "agrumes.jpg" },
    { name: "Amandier",        cat: "Fruits",   regions: "Sfax",                        icon: "bi-tree-fill",
      desc: "Arbre résistant à la sécheresse, cultivé pour ses amandes.", img: "amande.jpg" },
    { name: "Pistachier",      cat: "Fruits",   regions: "Kairouan",                    icon: "bi-tree",
      desc: "Arbre des zones arides, cultivé pour ses pistaches.", img: "pistachier.jpg" },
    { name: "Grenadier",       cat: "Fruits",   regions: "Gafsa, Tozeur",               icon: "bi-apple",
      desc: "Grenade des oasis, appréciée pour sa douceur.", img: "grenadier.jpg" },
    { name: "Abricotier",      cat: "Fruits",   regions: "Gafsa",                       icon: "bi-apple",
      desc: "Fruit de printemps parfumé, cultivé dans les vergers du centre-ouest.", img: "abricotier.jpg" },
    { name: "Palmier dattier", cat: "Fruits",   regions: "Gafsa, Tozeur",               icon: "bi-sun",
      desc: "Palmier des oasis du sud tunisien, producteur de dattes.", img: "palmier dattier.jpg" },
    { name: "Figuier",         cat: "Fruits",   regions: "Médenine",                    icon: "bi-tree-fill",
      desc: "Arbre rustique dont les figues se consomment fraîches ou séchées.", img: "figuier.jpg" },

    { name: "Piment",          cat: "Légumes",  regions: "Kairouan",                    icon: "bi-fire",
      desc: "Ingrédient de base de la harissa et de nombreux plats tunisiens.", img: "piment.jpg" },
    { name: "Tomate",          cat: "Légumes",  regions: "Bizerte",                     icon: "bi-basket",
      desc: "Légume incontournable de la cuisine tunisienne, frais ou en concentré.", img: "tomate.jpg" },
    { name: "Pomme de terre",  cat: "Légumes",  regions: "Bizerte",                     icon: "bi-basket2",
      desc: "Culture du nord, consommée toute l’année.", img: "pomme de terre.jpg" },
    { name: "Artichaut",       cat: "Légumes",  regions: "Bizerte",                     icon: "bi-flower2",
      desc: "Légume de saison apprécié pour son cœur tendre.", img: "artichaut.jpg" },

    { name: "Henné",           cat: "Plantes",  regions: "Tozeur",                      icon: "bi-flower3",
      desc: "Plante tinctoriale des oasis, utilisée dans les soins et la décoration traditionnelle.", img: "henné.jpg" }
  ];

  /* ---------- Utilitaires ---------- */
  const $ = (s, r = document) => r.querySelector(s);
  const esc = (s) => String(s).replace(/[&<>"']/g, (c) =>
    ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));
  const slug = (s) => s.normalize("NFD").replace(/[\u0300-\u036f]/g, "")
    .toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/^-+|-+$/g, "");
  const norm = (s) => s.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
  const store = {
    get(k, d) { try { return JSON.parse(localStorage.getItem(k)) ?? d; } catch (e) { return d; } },
    set(k, v) { try { localStorage.setItem(k, JSON.stringify(v)); return true; } catch (e) { return false; } }
  };

  /* ---------- Variétés persistantes ---------- */
  // Compatibilité : anciennes données enregistrées avec la clé "region"
  const fixOld = (v) => Object.assign({}, v, { regions: v.regions || v.region || "" });
  let VARIETIES = [];
  const saveVarieties = () => store.set("taam_varieties", VARIETIES);

  // Noms de variétés supprimées ou renommées : elles ne reviennent pas avec les valeurs par défaut
  const getDeleted = () => store.get("taam_deleted", []);
  const markDeleted = (name) => {
    const d = getDeleted();
    if (!d.some((x) => norm(x) === norm(name))) { d.push(name); store.set("taam_deleted", d); }
  };
  const unmarkDeleted = (name) => {
    store.set("taam_deleted", getDeleted().filter((x) => norm(x) !== norm(name)));
  };

  (function loadVarieties() {
    const stored = store.get("taam_varieties", null);
    if (!stored) { VARIETIES = DEFAULT_VARIETIES.map(fixOld); return; }
    // Ajoute les nouvelles variétés par défaut (sauf celles supprimées par l'agriculteur)
    const have = new Set(stored.map((v) => norm(v.name)));
    const gone = new Set(getDeleted().map(norm));
    const extra = DEFAULT_VARIETIES.filter((d) => !have.has(norm(d.name)) && !gone.has(norm(d.name)));
    VARIETIES = stored.concat(extra).map(fixOld);
    if (extra.length) saveVarieties();
  })();

  /* ---------- Authentification (locale) ----------
     connexion.html (via auth.js) enregistre :
     taam_user = { name, email, role }   (role : "user" ou "farmer") */
  const Auth = {
    user() { return store.get("taam_user", null); },
    logout() {
      localStorage.removeItem("taam_user");
      localStorage.removeItem("taam_role");
      location.reload();
    }
  };
  window.TaamAuth = Auth;

  /* ---------- Rôle : lu depuis la connexion ---------- */
  const Role = {
    get() {
      const u = Auth.user();
      const r = (u && (u.role || u.type || u.user_type)) || store.get("taam_role", "");
      return String(r || "").trim().toLowerCase();
    }
  };
  const isFarmer = () => ["farmer", "agriculteur"].includes(Role.get());

  function renderAuthNav() {
    const li = $("#authNav");
    if (!li) return;
    const u = Auth.user();
    if (u) {
      li.innerHTML = '<a class="nav-link" href="#" id="logoutLink">Déconnexion (' + esc(u.name || "compte") + ")</a>";
      $("#logoutLink").addEventListener("click", (e) => { e.preventDefault(); Auth.logout(); });
    }
  }

  /* ---------- Adoptions ---------- */
  const adoptKey = () => "taam_adoptions_" + ((Auth.user() || {}).email || "anon");
  const getAdopted = () => store.get(adoptKey(), []);
  const addAdopted = (name) => {
    const a = getAdopted();
    if (!a.includes(name)) { a.push(name); store.set(adoptKey(), a); }
  };
  // Synchronise les adoptions de tous les comptes : newName = null => suppression
  function updateAdoptions(oldName, newName) {
    try {
      for (let i = 0; i < localStorage.length; i++) {
        const k = localStorage.key(i);
        if (!k || k.indexOf("taam_adoptions_") !== 0) continue;
        let a = store.get(k, []);
        if (!a.includes(oldName)) continue;
        a = a.filter((x) => x !== oldName);
        if (newName && !a.includes(newName)) a.push(newName);
        store.set(k, a);
      }
    } catch (e) { /* ignore */ }
  }

  /* ---------- Variétés : rendu ---------- */
  const grid = $("#varietyGrid");
  const filters = $("#varietyFilters");
  const search = $("#varietySearch");
  let activeCat = "Tous";

  function imgSrc(v) {
    if (v.imgData) return v.imgData;                       // image importée
    const file = v.img || (slug(v.name) + "." + IMG_EXT);  // image du dossier
    return encodeURI(IMG_BASE + file);                     // gère espaces et accents
  }

  function cardHTML(v) {
    const adopted = getAdopted().includes(v.name);
    const adoptBtn = adopted
      ? '<button type="button" class="btn-adopted" disabled><i class="bi bi-check-circle"></i> Graine adoptée</button>'
      : '<button type="button" class="btn-adopt" data-adopt="' + esc(v.name) + '"><i class="bi bi-heart"></i> Adopter une graine</button>';
    const farmerBtns = isFarmer()
      ? '<button type="button" class="btn-edit" data-edit="' + esc(v.name) + '"><i class="bi bi-pencil-square"></i> Modifier</button>' +
        '<button type="button" class="btn-delete" data-delete="' + esc(v.name) + '"><i class="bi bi-trash"></i> Supprimer</button>'
      : "";
    return (
      '<div class="col-md-6 col-lg-4">' +
        '<article class="variety-card" data-cat="' + esc(v.cat) + '">' +
          '<div class="variety-media">' +
            '<img src="' + esc(imgSrc(v)) + '" alt="' + esc(v.name) + '" loading="lazy">' +
            '<i class="bi ' + esc(v.icon || "bi-flower1") + '"></i>' +
            '<span class="badge-cat">' + esc(v.cat) + "</span>" +
          "</div>" +
          '<div class="variety-body">' +
            '<h3 class="variety-title">' + esc(v.name) + "</h3>" +
            '<div class="variety-region"><i class="bi bi-geo-alt"></i> ' + esc(v.regions) + "</div>" +
            "<p>" + esc(v.desc) + "</p>" +
          '<div class="variety-actions">' + adoptBtn + farmerBtns + "</div>" + 
          "</div>" +
        "</article>" +
      "</div>"
    );
  }

  function renderGrid() {
    if (!grid) return;
    const q = norm(search ? search.value.trim() : "");
    const list = VARIETIES.filter((v) =>
      (activeCat === "Tous" || v.cat === activeCat) &&
      (!q || norm(v.name + " " + v.regions + " " + v.desc + " " + v.cat).includes(q)));
    grid.innerHTML = list.length
      ? list.map(cardHTML).join("")
      : '<div class="col-12"><p class="muted text-center py-4">Aucune variété trouvée.</p></div>';
    // Image introuvable : on garde le dégradé + l'icône
    grid.querySelectorAll(".variety-media > img").forEach((img) => {
      img.addEventListener("error", () => img.remove());
    });
  }

  function renderFilters() {
    if (!filters) return;
    const cats = ["Tous"].concat([...new Set(VARIETIES.map((v) => v.cat))]);
    if (!cats.includes(activeCat)) activeCat = "Tous";
    filters.innerHTML = cats.map((c) =>
      '<button type="button" class="pill' + (c === activeCat ? " active" : "") + '" data-cat="' + esc(c) + '">' + esc(c) + "</button>").join("");
  }
  if (filters) {
    filters.addEventListener("click", (e) => {
      const b = e.target.closest("[data-cat]");
      if (!b) return;
      activeCat = b.dataset.cat;
      renderFilters();
      renderGrid();
    });
  }

  /* ---------- Modal ajout / modification ---------- */
  const varietyModalEl = $("#varietyModal");
  const varietyModal = varietyModalEl && window.bootstrap ? new bootstrap.Modal(varietyModalEl) : null;
  const varietyForm = $("#varietyForm");
  const addBtn = $("#addVarietyBtn");
  let editingName = null;   // null = ajout, sinon nom de la variété modifiée

  function openVarietyModal(v) {
    editingName = v ? v.name : null;
    if (varietyForm) varietyForm.reset();
    const err = $("#varietyError");
    if (err) err.hidden = true;
    const title = $("#varietyTitle");
    if (title) title.textContent = v ? "Modifier la variété" : "Ajouter une variété";
    if (v && varietyForm) {
      const f = varietyForm.elements;
      f.vName.value = v.name;
      f.vCat.value = v.cat;
      f.vRegion.value = v.regions;
      f.vDesc.value = v.desc;
      f.vImgName.value = v.img || "";
    }
    varietyModal && varietyModal.show();
  }

  if (addBtn) {
    addBtn.addEventListener("click", () => {
      if (!isFarmer()) return;
      openVarietyModal(null);
    });
  }

  function fileToDataURL(file, max) {
    return new Promise((resolve, reject) => {
      const r = new FileReader();
      r.onerror = reject;
      r.onload = () => {
        const img = new Image();
        img.onerror = reject;
        img.onload = () => {
          const k = Math.min(1, max / Math.max(img.width, img.height));
          const c = document.createElement("canvas");
          c.width = Math.round(img.width * k);
          c.height = Math.round(img.height * k);
          c.getContext("2d").drawImage(img, 0, 0, c.width, c.height);
          resolve(c.toDataURL("image/jpeg", 0.8));
        };
        img.src = r.result;
      };
      r.readAsDataURL(file);
    });
  }

  if (varietyForm) {
    varietyForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      if (!isFarmer()) return;
      const f = varietyForm.elements;
      const name = f.vName.value.trim();
      const err = $("#varietyError");
      const fail = (msg) => { if (err) { err.textContent = msg; err.hidden = false; } };

      if (!name || !f.vRegion.value.trim() || !f.vDesc.value.trim()) return fail("Veuillez remplir tous les champs obligatoires.");
      if (VARIETIES.some((v) => norm(v.name) === norm(name) && v.name !== editingName)) return fail("Cette variété existe déjà.");

      const data = { name, cat: f.vCat.value, regions: f.vRegion.value.trim(), desc: f.vDesc.value.trim() };
      const imgName = f.vImgName.value.trim();
      let imgData = null;
      if (f.vImgFile.files[0]) {
        try { imgData = await fileToDataURL(f.vImgFile.files[0], 800); }
        catch (x) { return fail("Image illisible, essayez un autre fichier."); }
      }

      if (editingName) {
        /* ----- UPDATE ----- */
        const i = VARIETIES.findIndex((v) => v.name === editingName);
        if (i < 0) return fail("Variété introuvable.");
        const old = VARIETIES[i];
        const upd = Object.assign({}, old, data);
        if (imgData) { upd.imgData = imgData; }
        else if (imgName && imgName !== old.img) { upd.img = imgName; delete upd.imgData; }
        VARIETIES[i] = upd;
        if (!saveVarieties()) {
          VARIETIES[i] = old;
          return fail("Stockage plein : utilisez plutôt un nom de fichier du dossier assets/img/produit.");
        }
        if (old.name !== name) {
          markDeleted(old.name);          // l'ancien nom par défaut ne doit pas revenir
          unmarkDeleted(name);
          updateAdoptions(old.name, name);
        }
      } else {
        /* ----- CREATE ----- */
        const v = Object.assign({ icon: "bi-flower1" }, data);
        if (imgName) v.img = imgName;
        if (imgData) v.imgData = imgData;
        VARIETIES.push(v);
        if (!saveVarieties()) {
          VARIETIES.pop();
          return fail("Stockage plein : utilisez plutôt un nom de fichier du dossier assets/img/produit.");
        }
        unmarkDeleted(name);
      }

      if (err) err.hidden = true;
      editingName = null;
      varietyModal && varietyModal.hide();
      renderFilters();
      renderGrid();
    });
  }

  /* ---------- Adoption : modal + actions de la grille ---------- */
  let pending = null;
  const modalEl = $("#adoptModal");
  const modal = modalEl && window.bootstrap ? new bootstrap.Modal(modalEl) : null;

  if (grid) {
    grid.addEventListener("click", (e) => {
      // Agriculteur : modifier
      const edit = e.target.closest("[data-edit]");
      if (edit) {
        if (!isFarmer()) return;
        const v = VARIETIES.find((x) => x.name === edit.dataset.edit);
        if (v) openVarietyModal(v);
        return;
      }
      // Agriculteur : supprimer
      const del = e.target.closest("[data-delete]");
      if (del) {
        if (!isFarmer()) return;
        const name = del.dataset.delete;
        if (confirm("Supprimer la variété « " + name + " » ?")) {
          VARIETIES = VARIETIES.filter((v) => v.name !== name);
          saveVarieties();
          markDeleted(name);
          updateAdoptions(name, null);
          renderFilters();
          renderGrid();
        }
        return;
      }
      // Adopter
      const adopt = e.target.closest("[data-adopt]");
      if (adopt) {
        if (!Auth.user()) { location.href = "connexion.html"; return; }
        pending = adopt.dataset.adopt;
        $("#adoptName").textContent = pending;
        modal && modal.show();
        return;
      }
    });
  }

  const confirmBtn = $("#adoptConfirm");
  if (confirmBtn) {
    confirmBtn.addEventListener("click", () => {
      if (pending) addAdopted(pending);
      pending = null;
      modal && modal.hide();
      renderGrid();
    });
  }

  if (search) search.addEventListener("input", renderGrid);

  /* ---------- Affichage selon le rôle ---------- */
  function renderRole() {
    const bar = $("#farmerBar");
    if (bar) bar.hidden = !isFarmer();   // bouton « Ajouter »
    renderGrid();                        // boutons « Modifier » / « Supprimer »
  }

  /* ---------- Jedda IA (assistant) ---------- */
  const Jedda = (function () {
    if (document.body.dataset.jedda !== "on") {
      return { open() {}, send() {} };
    }

    const fab = document.createElement("button");
    fab.type = "button";
    fab.id = "jeddaToggle";
    fab.innerHTML = '<i class="bi bi-chat-heart"></i> Jedda IA';

    const panel = document.createElement("div");
    panel.id = "jeddaPanel";
    panel.setAttribute("role", "dialog");
    panel.setAttribute("aria-label", "Jedda IA");
    panel.innerHTML =
      '<div class="jedda-head"><span>Jedda IA</span><button type="button" class="jedda-close" aria-label="Fermer" style="background:none;border:0;color:#fff;font-size:1.2rem"><i class="bi bi-x-lg"></i></button></div>' +
      '<div class="jedda-messages"></div>' +
      '<form class="jedda-input"><input type="text" placeholder="Posez votre question…" aria-label="Votre message"><button type="submit" aria-label="Envoyer"><i class="bi bi-send"></i></button></form>';

    document.body.append(panel, fab);
    const box = $(".jedda-messages", panel);
    const form = $(".jedda-input", panel);
    const input = $("input", form);

    function add(text, who) {
      const m = document.createElement("div");
      m.className = "jedda-msg" + (who === "user" ? " user" : "");
      m.textContent = text;
      box.appendChild(m);
      box.scrollTop = box.scrollHeight;
    }

    /* Réponse locale. Pour une vraie IA, remplacez cette fonction
       par un appel à votre serveur (fetch) qui renvoie le texte de la réponse. */
    function reply(q) {
      const n = norm(q);
      const v = VARIETIES.find((x) => n.includes(norm(x.name)));
      if (v) return v.name + " (" + v.cat + ") : " + v.desc + " Régions : " + v.regions + ".";
      if (n.includes("adopt")) return "Pour adopter une graine : choisissez une variété, connectez-vous, puis cliquez sur « Adopter une graine ».";
      if (n.includes("bonjour") || n.includes("salut")) return "Bonjour ! Demandez-moi des informations sur une variété.";
      return "Je peux vous parler des variétés du catalogue. Essayez par exemple : « Parle-moi du blé dur ».";
    }

    function send(text) {
      text = (text || "").trim();
      if (!text) return;
      add(text, "user");
      setTimeout(() => add(reply(text), "bot"), 350);
    }

    function open() { panel.classList.add("open"); input.focus(); }
    function close() { panel.classList.remove("open"); }

    add("Salam ! Je suis Jedda, posez-moi vos questions sur les variétés tunisiennes.", "bot");
    fab.addEventListener("click", () => (panel.classList.contains("open") ? close() : open()));
    $(".jedda-close", panel).addEventListener("click", close);
    form.addEventListener("submit", (e) => { e.preventDefault(); send(input.value); input.value = ""; });

    return { open, send };
  })();

  /* ---------- Démarrage ---------- */
  renderAuthNav();
  renderFilters();
  renderRole();   // affiche aussi la grille
})();
