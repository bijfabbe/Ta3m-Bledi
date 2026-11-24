/* =========================================================
   Ta3m Bledi – auth.js
   Inscription / connexion (démo en localStorage)
   Enregistre taam_user = { name, email, role } lu par musee.js
   ========================================================= */
(function () {
  "use strict";

  const $ = (s) => document.querySelector(s);
  const store = {
    get(k, d) { try { return JSON.parse(localStorage.getItem(k)) ?? d; } catch (e) { return d; } },
    set(k, v) { try { localStorage.setItem(k, JSON.stringify(v)); return true; } catch (e) { return false; } }
  };

  const REDIRECT = "services.html";

  // Hachage du mot de passe (SHA-256 si disponible, sinon repli simple)
  async function hash(text) {
    try {
      if (window.crypto && crypto.subtle) {
        const buf = await crypto.subtle.digest("SHA-256", new TextEncoder().encode(text));
        return Array.from(new Uint8Array(buf)).map((b) => b.toString(16).padStart(2, "0")).join("");
      }
    } catch (e) { /* repli ci-dessous */ }
    return "b64:" + btoa(unescape(encodeURIComponent(text)));
  }

  function show(msg, ok) {
    const box = $("#authMsg");
    if (!box) return;
    box.textContent = msg;
    box.className = "alert " + (ok ? "alert-success" : "alert-danger");
    box.hidden = false;
  }

  const accounts = () => store.get("taam_accounts", []);

  /* ----- Inscription ----- */
  const reg = $("#registerForm");
  if (reg) {
    reg.addEventListener("submit", async (e) => {
      e.preventDefault();
      const name = $("#rName").value.trim();
      const email = $("#rEmail").value.trim().toLowerCase();
      const role = $("#roleSelect").value;
      const p1 = $("#rPass").value;
      const p2 = $("#rPass2").value;

      if (name.length < 2) return show("Veuillez saisir votre nom complet.", false);
      if (role !== "user" && role !== "farmer") return show("Veuillez choisir : simple utilisateur ou agriculteur.", false);
      if (p1.length < 6) return show("Le mot de passe doit contenir au moins 6 caractères.", false);
      if (p1 !== p2) return show("Les mots de passe ne sont pas identiques.", false);

      const list = accounts();
      if (list.some((a) => a.email === email)) return show("Un compte existe déjà avec cet e-mail.", false);

      list.push({ name, email, role, pass: await hash(p1) });
      if (!store.set("taam_accounts", list)) return show("Impossible d'enregistrer le compte.", false);

      store.set("taam_user", { name, email, role });
      show("Compte créé. Redirection…", true);
      setTimeout(() => { location.href = REDIRECT; }, 700);
    });
  }

  /* ----- Connexion ----- */
  const login = $("#loginForm");
  if (login) {
    login.addEventListener("submit", async (e) => {
      e.preventDefault();
      const email = $("#lEmail").value.trim().toLowerCase();
      const pass = await hash($("#lPass").value);
      const acc = accounts().find((a) => a.email === email && a.pass === pass);
      if (!acc) return show("E-mail ou mot de passe incorrect.", false);

      store.set("taam_user", { name: acc.name, email: acc.email, role: acc.role });
      show("Connexion réussie. Redirection…", true);
      setTimeout(() => { location.href = REDIRECT; }, 500);
    });
  }
})();
