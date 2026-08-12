(() => {
  const STORAGE = {
    age: "chr.age",
    hrmax: "chr.hrmax",
    customMax: "chr.customMax",
    mode: "chr.mode",
    history: "chr.history.v1",
  };

  const MIN_IBI = 250;
  const MAX_IBI = 2500;
  const WINDOW_MS = 20000;
  const HISTORY_MAX = 60;

  const ZONES = [
    { id: "rest", name: "Resting", min: 0, max: 0.5, color: getVar("--zone-0") },
    { id: "z1", name: "Warm-up", min: 0.5, max: 0.6, color: getVar("--zone-1") },
    { id: "z2", name: "Fat burn", min: 0.6, max: 0.7, color: getVar("--zone-2") },
    { id: "z3", name: "Endurance", min: 0.7, max: 0.8, color: getVar("--zone-3") },
    { id: "z4", name: "Performance", min: 0.8, max: 0.9, color: getVar("--zone-4") },
    { id: "z5", name: "Maximum", min: 0.9, max: 1.2, color: getVar("--zone-5") },
  ];

  const el = {
    age: document.getElementById("age"),
    hrmax: document.getElementById("hrmax"),
    modeRest: document.getElementById("modeRest"),
    modeActive: document.getElementById("modeActive"),
    bpmValue: document.getElementById("bpmValue"),
    bpmSub: document.getElementById("bpmSub"),
    tap: document.getElementById("tap"),
    tapTitle: document.getElementById("tapTitle"),
    tapHint: document.getElementById("tapHint"),
    tapCount: document.getElementById("tapCount"),
    sessionTime: document.getElementById("sessionTime"),
    confidence: document.getElementById("confidence"),
    zonePill: document.getElementById("zonePill"),
    livePill: document.getElementById("livePill"),
    needle: document.getElementById("zoneNeedle"),
    save: document.getElementById("saveReading"),
    reset: document.getElementById("resetSession"),
    clearHistory: document.getElementById("clearHistory"),
    avg7: document.getElementById("avg7"),
    lastSaved: document.getElementById("lastSaved"),
    trend: document.getElementById("trend"),
    history: document.getElementById("historyList"),
    toast: document.getElementById("toast"),
    trace: document.getElementById("trace"),
    tapBursts: document.getElementById("tapBursts"),
  };

  const state = {
    taps: [],
    bpm: null,
    mode: "resting",
    sessionStart: null,
    timer: null,
    beatTimer: null,
    history: [],
    customMax: false,
    spikes: [],
  };

  function getVar(name) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
  }

  function load() {
    const age = localStorage.getItem(STORAGE.age);
    const hrmax = localStorage.getItem(STORAGE.hrmax);
    const mode = localStorage.getItem(STORAGE.mode);
    const customMax = localStorage.getItem(STORAGE.customMax) === "1";
    try {
      state.history = JSON.parse(localStorage.getItem(STORAGE.history) || "[]");
    } catch {
      state.history = [];
    }
    if (age) el.age.value = age;
    state.customMax = customMax;
    if (customMax && hrmax) el.hrmax.value = hrmax;
    else syncHrmaxFromAge();
    setMode(mode === "active" ? "active" : "resting", false);
    renderHistory();
    renderStats();
    updateReadout();
  }

  function persist() {
    localStorage.setItem(STORAGE.age, el.age.value || "");
    localStorage.setItem(STORAGE.hrmax, el.hrmax.value || "");
    localStorage.setItem(STORAGE.customMax, state.customMax ? "1" : "0");
    localStorage.setItem(STORAGE.mode, state.mode);
    localStorage.setItem(STORAGE.history, JSON.stringify(state.history.slice(0, HISTORY_MAX)));
  }

  function ageValue() {
    const n = parseInt(el.age.value, 10);
    return Number.isFinite(n) ? Math.min(99, Math.max(8, n)) : 30;
  }

  function hrmaxValue() {
    const n = parseInt(el.hrmax.value, 10);
    if (Number.isFinite(n) && n >= 80 && n <= 230) return n;
    return 220 - ageValue();
  }

  function syncHrmaxFromAge() {
    if (state.customMax) return;
    el.hrmax.value = String(220 - ageValue());
  }

  function setMode(mode, persistIt = true) {
    state.mode = mode;
    el.modeRest.classList.toggle("is-on", mode === "resting");
    el.modeActive.classList.toggle("is-on", mode === "active");
    el.modeRest.setAttribute("aria-pressed", mode === "resting" ? "true" : "false");
    el.modeActive.setAttribute("aria-pressed", mode === "active" ? "true" : "false");
    if (persistIt) persist();
    updateReadout();
  }

  function median(values) {
    if (!values.length) return 0;
    const s = [...values].sort((a, b) => a - b);
    const mid = Math.floor(s.length / 2);
    return s.length % 2 ? s[mid] : (s[mid - 1] + s[mid]) / 2;
  }

  function confidence(taps, durationMs) {
    if (taps < 3 || durationMs < 4000) return { label: "Locking", level: 0 };
    if (taps < 6 || durationMs < 8000) return { label: "Building", level: 1 };
    if (taps < 10 || durationMs < 15000) return { label: "Good", level: 2 };
    return { label: "Solid", level: 3 };
  }

  function formatDuration(ms) {
    const total = Math.max(0, Math.floor(ms / 1000));
    const m = Math.floor(total / 60);
    const s = total % 60;
    return m + ":" + String(s).padStart(2, "0");
  }

  function setLive(on) {
    el.livePill.classList.toggle("is-off", !on);
  }

  function zoneFor(bpm) {
    const pct = bpm / hrmaxValue();
    return ZONES.find((z) => pct >= z.min && pct < z.max) || ZONES[ZONES.length - 1];
  }

  function restingLabel(bpm) {
    if (bpm < 50) return "Athletic / low";
    if (bpm < 60) return "Athletic";
    if (bpm <= 100) return "Typical resting";
    return "Elevated";
  }

  function prune(now) {
    const cut = now - WINDOW_MS;
    while (state.taps.length && state.taps[0] < cut) state.taps.shift();
  }

  function computeBpm() {
    if (state.taps.length < 2) return state.bpm;
    const intervals = [];
    for (let i = 1; i < state.taps.length; i++) {
      const d = state.taps[i] - state.taps[i - 1];
      if (d >= MIN_IBI && d <= MAX_IBI) intervals.push(d);
    }
    if (!intervals.length) return state.bpm;
    const med = median(intervals);
    const filtered = intervals.filter((d) => d > med * 0.55 && d < med * 1.75);
    const use = filtered.length >= 2 ? filtered : intervals;
    const avg = use.reduce((a, b) => a + b, 0) / use.length;
    return 60000 / avg;
  }

  function onTap() {
    const now = Date.now();
    const last = state.taps[state.taps.length - 1];
    if (last && now - last < MIN_IBI) return;

    if (!state.sessionStart) {
      state.sessionStart = now;
      startTimer();
      setLive(true);
    }

    state.taps.push(now);
    prune(now);
    state.bpm = computeBpm();
    pulseVisual();
    updateReadout();
  }

  function heatFromBpm(bpm) {
    if (!bpm) return 0;
    return Math.min(1, Math.max(0, (bpm - 55) / 125));
  }

  function applyHeartRateVisual(bpm) {
    const heat = heatFromBpm(bpm);
    const interval = bpm ? Math.max(260, Math.min(1100, 60000 / bpm)) : 900;
    const hit = bpm ? Math.max(160, Math.min(420, 60000 / bpm * 0.55)) : 340;
    el.tap.style.setProperty("--beat-ms", interval + "ms");
    el.tap.style.setProperty("--beat-hit-ms", hit + "ms");
    el.tap.style.setProperty("--heat", heat.toFixed(3));
    el.tap.classList.toggle("has-rate", !!bpm);
    el.tap.classList.toggle("is-hot", heat >= 0.52);
    el.tap.classList.toggle("is-racing", heat >= 0.78);
    return { heat, interval, hit };
  }

  function spawnBurst(heat) {
    if (!el.tapBursts) return;
    while (el.tapBursts.childElementCount > 8) el.tapBursts.firstChild.remove();
    const ring = document.createElement("span");
    ring.className = "tap__burst";
    const life = heat >= 0.75 ? 320 : heat >= 0.45 ? 460 : 640;
    ring.style.setProperty("--burst-ms", life + "ms");
    el.tapBursts.appendChild(ring);
    setTimeout(() => ring.remove(), life);
    if (heat >= 0.62) {
      const ring2 = document.createElement("span");
      ring2.className = "tap__burst";
      ring2.style.setProperty("--burst-ms", life + 90 + "ms");
      ring2.style.inset = "8px";
      el.tapBursts.appendChild(ring2);
      setTimeout(() => ring2.remove(), life + 90);
    }
  }

  function pulseVisual() {
    const { heat, hit } = applyHeartRateVisual(state.bpm);
    el.tap.classList.remove("is-beat");
    void el.tap.offsetWidth;
    el.tap.classList.add("is-beat");
    clearTimeout(state.beatTimer);
    state.beatTimer = setTimeout(() => el.tap.classList.remove("is-beat"), hit);
    spawnBurst(heat);
    state.spikes.push(1);
    if (navigator.vibrate) {
      if (heat >= 0.78) navigator.vibrate([10, 24, 16]);
      else if (heat >= 0.45) navigator.vibrate(12);
      else navigator.vibrate(8);
    }
  }

  function startTimer() {
    clearInterval(state.timer);
    state.timer = setInterval(() => {
      prune(Date.now());
      const last = state.taps[state.taps.length - 1];
      if (last && Date.now() - last > 8000) setLive(false);
      updateReadout();
    }, 250);
  }

  function resetSession() {
    state.taps = [];
    state.bpm = null;
    state.sessionStart = null;
    clearInterval(state.timer);
    setLive(false);
    applyHeartRateVisual(null);
    if (el.tapBursts) el.tapBursts.replaceChildren();
    el.tap.classList.remove("is-beat");
    updateReadout();
  }

  function updateReadout() {
    const now = Date.now();
    const taps = state.taps.length;
    const duration = state.sessionStart ? now - state.sessionStart : 0;
    const conf = confidence(taps, duration);
    const bpm = state.bpm;
    const coarse = window.matchMedia("(pointer: coarse)").matches;

    el.tapCount.textContent = String(taps);
    el.sessionTime.textContent = formatDuration(duration);
    el.confidence.textContent = taps ? conf.label : "Waiting";

    applyHeartRateVisual(bpm);

    if (bpm) {
      el.bpmValue.textContent = String(Math.round(bpm));
      const zone = zoneFor(bpm);
      const pct = Math.round((bpm / hrmaxValue()) * 100);
      if (state.mode === "resting") {
        el.bpmSub.textContent = restingLabel(bpm);
        el.zonePill.innerHTML = `<strong>${restingLabel(bpm)}</strong> · ${pct}% HRmax`;
      } else {
        el.bpmSub.textContent = zone.name + " zone";
        el.zonePill.innerHTML = `<strong>${zone.name}</strong> · ${pct}% HRmax`;
      }
      el.zonePill.style.setProperty("--zone-color", zone.color);
      el.needle.style.left = Math.min(100, Math.max(0, pct)) + "%";
      el.needle.classList.add("is-on");
      el.save.disabled = conf.level < 1;
      el.tapTitle.textContent = "Keep tapping";
      el.tapHint.textContent = coarse
        ? "One tap per beat."
        : "One tap per beat. Spacebar works too.";
    } else {
      el.bpmValue.textContent = "—";
      el.bpmSub.textContent = "beats per minute";
      el.zonePill.innerHTML = "<strong>Zone</strong> · waiting";
      el.needle.classList.remove("is-on");
      el.save.disabled = true;
      el.tapTitle.textContent = "Tap with each beat";
      el.tapHint.innerHTML = coarse
        ? "Find your pulse, then tap once per beat"
        : 'Find your pulse, then tap or press <span class="kbd">space</span>';
    }
  }

  function toast(msg) {
    el.toast.textContent = msg;
    el.toast.classList.add("is-on");
    clearTimeout(toast._t);
    toast._t = setTimeout(() => el.toast.classList.remove("is-on"), 2200);
  }

  function saveReading() {
    if (!state.bpm) return;
    const item = {
      bpm: Math.round(state.bpm),
      ts: Date.now(),
      mode: state.mode,
      age: ageValue(),
      hrmax: hrmaxValue(),
      taps: state.taps.length,
    };
    state.history.unshift(item);
    persist();
    renderHistory();
    renderStats();
    toast("Reading saved on this device");
  }

  function dayStart(ts) {
    const d = new Date(ts);
    d.setHours(0, 0, 0, 0);
    return d.getTime();
  }

  function renderStats() {
    const weekAgo = Date.now() - 7 * 86400000;
    const resting = state.history.filter((h) => h.mode === "resting" && h.ts >= weekAgo);
    const avg = resting.length
      ? Math.round(resting.reduce((a, b) => a + b.bpm, 0) / resting.length)
      : null;
    el.avg7.textContent = avg ? String(avg) : "—";

    const last = state.history[0];
    el.lastSaved.textContent = last ? String(last.bpm) : "—";

    const byDay = new Map();
    for (const h of state.history.filter((x) => x.mode === "resting")) {
      const key = dayStart(h.ts);
      if (!byDay.has(key)) byDay.set(key, []);
      byDay.get(key).push(h.bpm);
    }
    const days = [...byDay.keys()].sort((a, b) => a - b);
    if (days.length >= 2) {
      const first = avgOf(byDay.get(days[0]));
      const latest = avgOf(byDay.get(days[days.length - 1]));
      const diff = latest - first;
      el.trend.textContent = (diff > 0 ? "+" : "") + Math.round(diff);
    } else {
      el.trend.textContent = "—";
    }
  }

  function avgOf(arr) {
    return arr.reduce((a, b) => a + b, 0) / arr.length;
  }

  function renderHistory() {
    if (!state.history.length) {
      el.history.innerHTML = '<li class="empty">No saved readings yet. Measure for about 15 seconds, then save.</li>';
      return;
    }
    el.history.innerHTML = state.history
      .slice(0, 12)
      .map((h) => {
        const d = new Date(h.ts);
        const when = d.toLocaleString(undefined, {
          month: "short",
          day: "numeric",
          hour: "2-digit",
          minute: "2-digit",
        });
        const label = h.mode === "resting" ? "Resting" : "Active";
        return `<li>
          <div class="bpm">${h.bpm}</div>
          <div><strong>${label}</strong><small>${when} · ${h.taps} taps</small></div>
          <button class="btn btn--ghost" data-del="${h.ts}" type="button" aria-label="Delete reading">✕</button>
        </li>`;
      })
      .join("");
  }

  function clearHistory() {
    if (!state.history.length) return;
    if (!confirm("Clear all saved readings on this device?")) return;
    state.history = [];
    persist();
    renderHistory();
    renderStats();
  }

  /* Rolling ECG-style trace: baseline wander plus a QRS on each tap */
  function startTrace() {
    const canvas = el.trace;
    if (!canvas || !canvas.getContext) return;
    const ctx = canvas.getContext("2d");
    const qrsPat = [0, -0.08, -0.16, 0.12, 1, -0.42, -0.08, 0.22, 0.14, 0.04, 0];
    let qrsI = -1;
    let buf = [];
    let last = 0;

    function resize() {
      const dpr = Math.min(window.devicePixelRatio || 1, 2);
      const w = canvas.clientWidth || canvas.parentElement.clientWidth || 300;
      const h = canvas.clientHeight || 72;
      canvas.width = Math.floor(w * dpr);
      canvas.height = Math.floor(h * dpr);
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      buf = new Array(Math.max(120, Math.floor(w))).fill(0);
    }
    resize();
    window.addEventListener("resize", resize);

    function frame(ts) {
      const w = canvas.clientWidth;
      const h = canvas.clientHeight;
      if (!last) last = ts;
      const steps = Math.min(6, Math.max(1, Math.round((ts - last) / 16)));
      last = ts;

      for (let s = 0; s < steps; s++) {
        let y = Math.sin(ts / 260 + s) * 0.05;
        if (state.spikes.length) {
          state.spikes.shift();
          qrsI = 0;
        }
        if (qrsI >= 0) {
          y += qrsPat[qrsI] || 0;
          qrsI += 1;
          if (qrsI >= qrsPat.length) qrsI = -1;
        }
        buf.push(y);
        if (buf.length > w) buf.shift();
      }

      ctx.clearRect(0, 0, w, h);
      ctx.beginPath();
      ctx.strokeStyle = "rgba(255,77,109,0.88)";
      ctx.lineWidth = 1.6;
      const mid = h * 0.58;
      const amp = h * 0.42;
      for (let x = 0; x < buf.length; x++) {
        const py = mid - buf[x] * amp;
        if (x === 0) ctx.moveTo(x, py);
        else ctx.lineTo(x, py);
      }
      ctx.stroke();
      requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
  }

  el.tap.addEventListener("pointerdown", (e) => {
    e.preventDefault();
    onTap();
  });

  window.addEventListener("keydown", (e) => {
    if (e.code !== "Space" && e.code !== "Enter") return;
    const tag = (e.target && e.target.tagName) || "";
    if (tag === "INPUT" || tag === "TEXTAREA" || tag === "BUTTON") return;
    e.preventDefault();
    onTap();
  });

  el.age.addEventListener("input", () => {
    state.customMax = false;
    syncHrmaxFromAge();
    persist();
    updateReadout();
  });

  el.hrmax.addEventListener("input", () => {
    state.customMax = true;
    persist();
    updateReadout();
  });

  el.modeRest.addEventListener("click", () => setMode("resting"));
  el.modeActive.addEventListener("click", () => setMode("active"));
  el.save.addEventListener("click", saveReading);
  el.reset.addEventListener("click", resetSession);
  el.clearHistory.addEventListener("click", clearHistory);

  el.history.addEventListener("click", (e) => {
    const btn = e.target.closest("[data-del]");
    if (!btn) return;
    const ts = Number(btn.getAttribute("data-del"));
    state.history = state.history.filter((h) => h.ts !== ts);
    persist();
    renderHistory();
    renderStats();
  });

  const THEME_KEY = "chr.theme";

  function currentTheme() {
    const attr = document.documentElement.getAttribute("data-theme");
    return attr === "light" || attr === "dark" ? attr : "dark";
  }

  function applyTheme(theme, persist) {
    const next = theme === "light" ? "light" : "dark";
    document.documentElement.setAttribute("data-theme", next);
    const meta = document.querySelector('meta[name="theme-color"]');
    if (meta) meta.setAttribute("content", next === "light" ? "#f3eee6" : "#0b0c0f");
    const btn = document.getElementById("themeToggle");
    if (btn) {
      const label = next === "light" ? "Switch to night theme" : "Switch to day theme";
      btn.setAttribute("aria-label", label);
      btn.title = label;
    }
    if (persist) {
      try { localStorage.setItem(THEME_KEY, next); } catch {}
    }
  }

  function initTheme() {
    applyTheme(currentTheme(), false);
    const btn = document.getElementById("themeToggle");
    if (btn) {
      btn.addEventListener("click", () => {
        applyTheme(currentTheme() === "light" ? "dark" : "light", true);
      });
    }
    try {
      const mq = window.matchMedia("(prefers-color-scheme: light)");
      const onScheme = () => {
        try { if (localStorage.getItem(THEME_KEY)) return; } catch { return; }
        applyTheme(mq.matches ? "light" : "dark", false);
      };
      if (mq.addEventListener) mq.addEventListener("change", onScheme);
      else if (mq.addListener) mq.addListener(onScheme);
    } catch {}
  }

  initTheme();
  load();
  startTrace();
})();
