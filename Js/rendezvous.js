// /Js/rendezvous.js — version sans Firebase Storage (100% gratuit, via Apps Script)
import { db } from "./firebase-config.js";
import {
  collection, addDoc, serverTimestamp
} from "https://www.gstatic.com/firebasejs/11.8.1/firebase-firestore.js";

// === CONFIG APPS SCRIPT (à renseigner) ===
const WEBAPP_URL   = "https://script.google.com/macros/s/AKfycbya4eRqBWXuqmVwB1J13J_13nqT-l71Bac7qyogEbMfJjY93cOdDnyWmo88DYORIcgu/exec";
const SHARED_SECRET = "MonSecret2025";


// === UI refs ===
const form = document.getElementById("rdv-form");
const submitBtn = document.getElementById("rdv-submit");
const feedback = document.getElementById("rdv-feedback");
const progressEl = document.getElementById("rdv-progress");

// === helpers UI ===
const val = (id) => (document.getElementById(id)?.value ?? "").trim();
const showMsg = (msg, ok = true) => {
  if (!feedback) return;
  feedback.hidden = false;
  feedback.textContent = msg;
  feedback.style.color = ok ? "green" : "crimson";
};
const showProgress = (msg) => {
  if (!progressEl) return;
  progressEl.hidden = false;
  progressEl.textContent = msg;
  progressEl.style.color = "#444";
};

form?.addEventListener("submit", async (e) => {
  e.preventDefault();

  // Honeypot anti-spam
  if ((document.getElementById("website")?.value ?? "").trim()) {
    showMsg("Requête invalide.", false);
    return;
  }

  // Données formulaire
  const data = {
    fullName: val("fullName"),
    email:    val("email"),
    phone:    val("phone"),
    subject:  val("subject"),
    date:     val("date"),          // "YYYY-MM-DD"
    time:     val("time"),          // "HH:mm"
    meetingType: val("meetingType"), // "in_person" | "zoom"
    timezone: val("timezone") || null,
    message:  val("message"),
    status:   "new",
    createdAt: serverTimestamp(),
    updatedAt: serverTimestamp(),
    sitePath: location.pathname,
    files: []                        // on ne stocke rien ici (pas de Storage) => liste vide
  };

  // Vérifs mini
  if (!data.fullName || !data.email || !data.phone || !data.subject ||
      !data.date || !data.time || !data.meetingType || !data.message) {
    showMsg("Merci de remplir tous les champs requis.", false);
    return;
  }

  try {
    submitBtn?.setAttribute("disabled", "true");
    submitBtn.textContent = "Envoi…";
    showProgress("Création du rendez-vous…");
    console.log("Payload RDV →", JSON.stringify({
      ...data,
      // on remplace les serverTimestamp par une chaîne lisible pour le log
      createdAt: "[serverTimestamp]",
      updatedAt: "[serverTimestamp]"
    }, null, 2));


    // 1) Crée le RDV (ID Firestore)
    const docRef = await addDoc(collection(db, "appointments"), data);

    // 2) Préparer les pièces jointes (base64) — sans Firebase Storage
    const filesInput = document.getElementById("files");
    const fileList = Array.from(filesInput?.files ?? []);
    const MAX_TOTAL = 20 * 1024 * 1024; // ≈20 Mo cumulés (limite MailApp)
    let totalSize = 0;
    const attachments = [];
    let truncated = false;

    if (fileList.length) showProgress("Préparation des pièces jointes…");

    for (let i = 0; i < fileList.length; i++) {
      const file = fileList[i];

      // Filtre client: autoriser images/* et PDF, et limite unitaire soft 10 Mo
      const okType = file.type.startsWith("image/") || file.type === "application/pdf";
      const okSize = file.size <= 10 * 1024 * 1024;
      if (!okType || !okSize) {
        console.warn(`Fichier refusé: ${file.name} (type/poids)`);
        continue;
      }

      if (totalSize + file.size > MAX_TOTAL) {
        truncated = true;
        console.warn("Limite totale atteinte, le reste sera ignoré:", file.name);
        break;
      }

      // Lecture → base64
      const buf = await file.arrayBuffer();
      const base64 = btoa(String.fromCharCode(...new Uint8Array(buf)));
      attachments.push({
        name: file.name,
        type: file.type || "application/octet-stream",
        content: base64
      });
      totalSize += file.size;

      showProgress(`Pièces jointes: ${i + 1}/${fileList.length} préparées…`);
    }

    // 3) Appeler le Web App GAS pour envoyer les emails (admin + client)
    showProgress("Envoi des emails…");

    const payload = {
      secret: SHARED_SECRET,
      docId: docRef.id,
      fullName: data.fullName,
      email: data.email,
      phone: data.phone,
      subject: data.subject,
      date: data.date,
      time: data.time,
      meetingType: data.meetingType,
      timezone: data.timezone,
      message: data.message,
      attachments // tableaux base64 (peut être vide)
    };

    try {
      // Web Apps GAS + CORS: on envoie en no-cors (fire-and-forget)
      await fetch(WEBAPP_URL, {
        method: "POST",
        mode: "no-cors",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });
    } catch (err) {
      console.warn("App Script mail call failed:", err);
    }

    // 4) Feedback UI
    form.reset();
    progressEl.hidden = true;

    if (truncated) {
      showMsg("Votre demande a été envoyée. Certains fichiers n’ont pas pu être joints (limite ~20 Mo). Merci d’envoyer le reste par email.", true);
    } else {
      showMsg("Votre demande a bien été envoyée. Un email de confirmation va vous être adressé. ✅", true);
    }

    // Option: redirection
    // setTimeout(() => (location.href = "./Merci.html"), 1200);

  } catch (err) {
    console.error("AddDoc error:", err?.code, err?.message, err);
    showMsg("Erreur d’envoi. Veuillez réessayer plus tard.", false);
  } finally {
    submitBtn?.removeAttribute("disabled");
    submitBtn.textContent = "Envoyer la demande";
  }
});
