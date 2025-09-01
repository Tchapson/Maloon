// /Js/firebase-config.js
import { initializeApp } from "https://www.gstatic.com/firebasejs/11.8.1/firebase-app.js";
import { getFirestore }  from "https://www.gstatic.com/firebasejs/11.8.1/firebase-firestore.js";
// import { getAnalytics }  from "https://www.gstatic.com/firebasejs/11.8.1/firebase-analytics.js";

const firebaseConfig = {
  apiKey: "AIzaSyBDPAlbIar5bqt4uvTM51kpsTyh5-vn4z8",
  authDomain: "maloon-eb515.firebaseapp.com",
  projectId: "maloon-eb515",
  storageBucket: "maloon-eb515.firebasestorage.app",
  messagingSenderId: "317697823285",
  appId: "1:317697823285:web:6e051f52ccff2623bdd77c",
  measurementId: "G-QEE2X52JJN"
};

export const app = initializeApp(firebaseConfig);
export const db  = getFirestore(app);

// Active Analytics uniquement en prod HTTPS (pas localhost / pas file://)
export let analytics = null;
// if (location.protocol === "https:" && location.hostname !== "localhost") {
//   analytics = getAnalytics(app);
// }
