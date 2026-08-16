<?php
declare(strict_types=1);
$v = '20260816b';
$year = (int) date('Y');
$title = 'Check Heart Rate Online — Free BPM Pulse Monitor';
$desc = 'Measure your heart rate in your browser. Tap with each pulse to get BPM, training zones, and a private reading history on this device. No download required.';
$canonical = 'https://checkheartrate.io/';
$adsenseClient = 'ca-pub-6590664002834153';

function render_adsense(string $client, string $format = 'horizontal'): void
{
    $client = htmlspecialchars($client, ENT_QUOTES);
    $format = htmlspecialchars($format, ENT_QUOTES);
    echo <<<HTML
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="{$client}"
     data-ad-format="{$format}"
     data-full-width-responsive="true"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>

HTML;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#0b0c0f">
  <script>
    (function () {
      var key = "chr.theme";
      var theme = "dark";
      try {
        var stored = localStorage.getItem(key);
        if (stored === "light" || stored === "dark") theme = stored;
        else if (window.matchMedia && window.matchMedia("(prefers-color-scheme: light)").matches) theme = "light";
      } catch (e) {}
      document.documentElement.setAttribute("data-theme", theme);
      var meta = document.querySelector('meta[name="theme-color"]');
      if (meta) meta.setAttribute("content", theme === "light" ? "#f3eee6" : "#0b0c0f");
    })();
  </script>
  <title><?php echo htmlspecialchars($title, ENT_QUOTES); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($desc, ENT_QUOTES); ?>">
  <meta name="robots" content="index, follow">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-title" content="Heart Rate">
  <link rel="canonical" href="<?php echo htmlspecialchars($canonical, ENT_QUOTES); ?>">
  <link rel="icon" href="favicon.svg" type="image/svg+xml">
  <link rel="manifest" href="site.webmanifest">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo htmlspecialchars($canonical, ENT_QUOTES); ?>">
  <meta property="og:title" content="<?php echo htmlspecialchars($title, ENT_QUOTES); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($desc, ENT_QUOTES); ?>">
  <meta property="og:site_name" content="checkheartrate.io">
  <meta name="twitter:card" content="summary">
  <meta name="twitter:title" content="<?php echo htmlspecialchars($title, ENT_QUOTES); ?>">
  <meta name="twitter:description" content="<?php echo htmlspecialchars($desc, ENT_QUOTES); ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=<?php echo urlencode($v); ?>">

  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-W5Y02R5FFV"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-W5Y02R5FFV');
  </script>

  <!-- Google AdSense -->
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6590664002834153"
     crossorigin="anonymous"></script>

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "WebApplication",
    "name": "Check Heart Rate",
    "url": "<?php echo $canonical; ?>",
    "applicationCategory": "HealthApplication",
    "operatingSystem": "Any",
    "offers": { "@type": "Offer", "price": "0", "priceCurrency": "USD" },
    "description": "<?php echo $desc; ?>"
  }
  </script>
</head>
<body>
  <a class="skip" href="#measure">Skip to measure</a>

  <header class="header">
    <div class="header__inner">
      <a class="brand" href="#measure" aria-label="checkheartrate.io home">
        <svg class="brand__mark" viewBox="0 0 32 32" aria-hidden="true">
          <rect width="32" height="32" rx="8" fill="var(--surface-2)"/>
          <path d="M4 18h6l2-5 3 10 3-8 2 3h8" fill="none" stroke="var(--accent)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        checkheartrate<em>.io</em>
      </a>
      <nav class="nav" aria-label="Primary">
        <a href="#how">How to</a>
        <a href="#zones">Zones</a>
        <a href="#guide">Guide</a>
        <button type="button" class="theme-toggle" id="themeToggle" aria-label="Switch to day theme" title="Switch to day theme">
          <svg class="theme-icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
          <svg class="theme-icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 14.5A8.5 8.5 0 1 1 9.5 3 7 7 0 0 0 21 14.5z"/></svg>
        </button>
        <a class="nav__cta" href="#measure">Measure</a>
      </nav>
    </div>
  </header>

  <main>
    <aside class="ad-slot" data-ad="top" aria-label="Advertisement">
      <span class="ad-slot__label">Advertisement</span>
      <div class="ad-slot__box">
        <?php render_adsense($adsenseClient); ?>
      </div>
    </aside>

    <section class="hero wrap" id="measure">
      <div class="hero__intro">
        <div class="eyebrow">Daily pulse check</div>
        <h1>Check your heart rate in 15 seconds.</h1>
        <p class="lede">Feel your pulse, tap once per beat, and get a live BPM reading with training zones. Readings stay on this device — nothing is uploaded.</p>
      </div>

      <div class="stage">
        <article class="card panel">
          <div class="controls">
            <div class="field">
              <label for="age">Age</label>
              <input id="age" name="age" type="number" inputmode="numeric" min="8" max="99" value="30" autocomplete="off">
            </div>
            <div class="field">
              <label for="hrmax">HRmax</label>
              <input id="hrmax" name="hrmax" type="number" inputmode="numeric" min="80" max="230" value="190" autocomplete="off">
            </div>
            <div class="field">
              <label>Mode</label>
              <div class="seg" role="group" aria-label="Measurement mode">
                <button type="button" id="modeRest" class="is-on" aria-pressed="true">Resting</button>
                <button type="button" id="modeActive" aria-pressed="false">Active</button>
              </div>
            </div>
          </div>

          <div class="bpm-block">
            <div class="bpm-value" id="bpmValue" aria-live="polite">—</div>
            <div class="bpm-meta">
              <div class="bpm-unit">BPM</div>
              <div class="bpm-sub" id="bpmSub">beats per minute</div>
            </div>
          </div>

          <div class="readout-meta">
            <div class="pills">
              <span class="pill pill--live is-off" id="livePill"><i></i> Live</span>
              <span class="pill pill--conf" id="confidence">Waiting</span>
              <span class="pill pill--stat">Taps <strong id="tapCount">0</strong></span>
              <span class="pill pill--stat">Time <strong id="sessionTime">0:00</strong></span>
            </div>
            <div class="pill pill--zone" id="zonePill"><strong>Zone</strong> · waiting</div>
          </div>

          <div class="zones" aria-hidden="true">
            <div class="zones__bar">
              <span></span><span></span><span></span><span></span><span></span><span></span>
              <div class="zones__needle" id="zoneNeedle"></div>
            </div>
            <div class="zones__labels">
              <span>Rest</span><span>Warm-up</span><span>Fat burn</span><span>Endurance</span><span>Perf.</span><span>Max</span>
            </div>
          </div>

          <canvas class="trace" id="trace" width="640" height="72" aria-hidden="true"></canvas>

          <div class="actions">
            <button class="btn btn--primary" id="saveReading" type="button" disabled>Save reading</button>
            <button class="btn" id="resetSession" type="button">Reset</button>
          </div>
          <p class="hint">Most accurate after 10–15 seconds of steady taps. Resting heart rate is best first thing in the morning, before caffeine.</p>
        </article>

        <div class="card panel panel--tap">
          <button class="tap" id="tap" type="button" aria-label="Tap once for each heartbeat" style="--beat-ms: 900ms; --heat: 0;">
            <span class="tap__aura"></span>
            <span class="tap__orbit"></span>
            <span class="tap__ring"></span>
            <span class="tap__ring tap__ring--2"></span>
            <span class="tap__bursts" id="tapBursts"></span>
            <span class="tap__core">
              <span class="tap__flash"></span>
              <span class="tap__heart">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path fill="currentColor" d="M12.1 21.35 10 19.45C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8 10.95l-1.9 1.9Z"/>
                </svg>
              </span>
            </span>
          </button>
          <div class="tap-copy">
            <strong id="tapTitle">Tap with each beat</strong>
            <span id="tapHint">Find your pulse, then tap or press <span class="kbd">space</span></span>
          </div>
        </div>
      </div>

      <div class="stats">
        <article class="card panel">
          <h2>On this device</h2>
          <p class="lede" style="margin-bottom:14px">Private history, stored only in your browser. Useful as a daily resting-rate log.</p>
          <div class="stat-row">
            <div class="stat"><span>7-day resting avg</span><b id="avg7">—</b></div>
            <div class="stat"><span>Last saved</span><b id="lastSaved">—</b></div>
            <div class="stat"><span>Trend</span><b id="trend">—</b></div>
          </div>
          <p class="hint">Trend compares your earliest and latest resting days. It is a personal log, not a diagnosis.</p>
        </article>
        <article class="card panel">
          <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:10px">
            <h2 style="margin:0">Recent readings</h2>
            <button class="btn btn--ghost" id="clearHistory" type="button">Clear</button>
          </div>
          <ul class="history-list" id="historyList"></ul>
        </article>
      </div>
    </section>

    <section class="section wrap" id="how">
      <div class="eyebrow">Three steps</div>
      <h2>How to measure</h2>
      <div class="steps">
        <article class="step">
          <i>1</i>
          <h3>Find a pulse</h3>
          <p>Use your index and middle fingers on your neck (beside the windpipe) or on your wrist, about a thumb-width below the base of the hand. Press lightly. Do not use your thumb — it has its own pulse.</p>
        </article>
        <article class="step">
          <i>2</i>
          <h3>Tap every beat</h3>
          <p>Click the heart, or press space, once each time you feel a beat. The first tap starts the session. Keep a steady rhythm rather than tapping as fast as you can.</p>
        </article>
        <article class="step">
          <i>3</i>
          <h3>Read, then save</h3>
          <p>BPM is calculated from the gaps between taps over a rolling 20-second window. When confidence says Good or Solid, save the reading if you want it in today’s log.</p>
        </article>
      </div>
    </section>

    <aside class="ad-slot" data-ad="mid" aria-label="Advertisement">
      <span class="ad-slot__label">Advertisement</span>
      <div class="ad-slot__box">
        <?php render_adsense($adsenseClient); ?>
      </div>
    </aside>

    <section class="section wrap prose" id="guide">
      <div class="eyebrow">The basics</div>
      <h2>What your heart rate is telling you</h2>
      <p>Heart rate is the number of times your heart beats in one minute, written as BPM. It rises when your body needs more oxygen — during a walk, a hard session, stress, illness, or after coffee — and falls when you are still. Medical teams use it as one vital sign among several. Athletes use it to pace training. For everyday use, a consistent morning resting measurement is the most useful number to watch.</p>

      <div class="grid-2" style="margin:28px 0">
        <figure class="figure">
          <svg viewBox="0 0 360 180" role="img" aria-label="Checking the pulse at the wrist">
            <rect width="360" height="180" rx="16" fill="var(--figure-fill)"/>
            <path d="M70 150c40-8 80-70 128-78 30-5 62 10 92 38" fill="none" stroke="var(--figure-line)" stroke-width="28" stroke-linecap="round"/>
            <circle cx="198" cy="74" r="10" fill="var(--accent)"/>
            <circle cx="198" cy="74" r="22" fill="none" stroke="var(--accent)" stroke-opacity=".35" stroke-width="2"/>
            <text x="24" y="36" fill="var(--figure-ink)" font-size="16" font-family="Outfit, sans-serif">Wrist (radial)</text>
            <text x="24" y="56" fill="var(--figure-mute)" font-size="12" font-family="Figtree, sans-serif">Palm up, two fingers, light pressure</text>
          </svg>
          <figcaption>
            <h3>Wrist</h3>
            <p>Turn the palm up. Place two fingers on the thumb side of the wrist, about 2–3 cm below the hand, and wait for a regular beat.</p>
          </figcaption>
        </figure>
        <figure class="figure">
          <svg viewBox="0 0 360 180" role="img" aria-label="Checking the pulse at the neck">
            <rect width="360" height="180" rx="16" fill="var(--figure-fill)"/>
            <circle cx="180" cy="86" r="46" fill="var(--figure-mid)"/>
            <path d="M162 86c0-10 8-18 18-18s18 8 18 18" fill="none" stroke="var(--figure-line)" stroke-width="6"/>
            <circle cx="148" cy="96" r="8" fill="var(--accent)"/>
            <circle cx="148" cy="96" r="18" fill="none" stroke="var(--accent)" stroke-opacity=".35" stroke-width="2"/>
            <text x="24" y="36" fill="var(--figure-ink)" font-size="16" font-family="Outfit, sans-serif">Neck (carotid)</text>
            <text x="24" y="56" fill="var(--figure-mute)" font-size="12" font-family="Figtree, sans-serif">Beside the windpipe, never both sides at once</text>
          </svg>
          <figcaption>
            <h3>Neck</h3>
            <p>Place two fingers on the side of the neck, next to the windpipe. Press gently. Check one side only.</p>
          </figcaption>
        </figure>
      </div>

      <h3>Resting heart rate</h3>
      <p>Resting heart rate is the pulse when you have been still for several minutes. For most adults it sits between <a href="https://www.heart.org/en/healthy-living/fitness/fitness-basics/target-heart-rates">60 and 100 BPM</a>. Very fit people often sit in the 40s or 50s. A single high or low reading is usually just context — poor sleep, dehydration, a hot room, medication, or a hard session the day before. A change that lasts days, or a rate that comes with chest pain, faintness, or unusual shortness of breath, is a reason to speak with a clinician.</p>

      <h3>Maximum heart rate (HRmax)</h3>
      <p>HRmax is an estimate of the fastest your heart can beat. The common formula is <strong>220 − age</strong>. A 40-year-old would see 180. It is only an estimate — real HRmax varies — so you can override the field if you already know yours from a lab test or a hard effort. Training zones on this page are percentages of that number.</p>
    </section>

    <section class="section wrap" id="zones">
      <div class="eyebrow">Training</div>
      <h2>Heart rate zones</h2>
      <p class="lede">Zones describe intensity. They are a guide for pacing, not a prescription. Switch the monitor to Active after a warm-up if you want the needle to track the session.</p>
      <div class="zone-cards">
        <article class="zone-card">
          <header><h3><span class="dot" style="background:var(--zone-1)"></span>Warm-up · 50–60%</h3><span>Z1</span></header>
          <p>Easy movement and recovery. Conversation is comfortable. Useful at the start and end of a session.</p>
        </article>
        <article class="zone-card">
          <header><h3><span class="dot" style="background:var(--zone-2)"></span>Fat burn · 60–70%</h3><span>Z2</span></header>
          <p>Steady aerobic work. You can still speak in sentences. A common zone for building an endurance base.</p>
        </article>
        <article class="zone-card">
          <header><h3><span class="dot" style="background:var(--zone-3)"></span>Endurance · 70–80%</h3><span>Z3</span></header>
          <p>Breathing is heavier and talking is shorter. Improves cardiovascular fitness when used in controlled doses.</p>
        </article>
        <article class="zone-card">
          <header><h3><span class="dot" style="background:var(--zone-4)"></span>Performance · 80–90%</h3><span>Z4</span></header>
          <p>Hard. Sustainable only in intervals for most people. Used to raise threshold, not for long daily work.</p>
        </article>
        <article class="zone-card">
          <header><h3><span class="dot" style="background:var(--zone-5)"></span>Maximum · 90–100%</h3><span>Z5</span></header>
          <p>Near all-out. Brief efforts only. Training at true maximum is uncomfortable and is not appropriate for everyone.</p>
        </article>
        <article class="zone-card">
          <header><h3><span class="dot" style="background:var(--zone-0)"></span>Resting · under 50%</h3><span>R</span></header>
          <p>Stillness, sleep, and easy sitting. This is the number worth logging each morning if you use the site daily.</p>
        </article>
      </div>
    </section>

    <section class="section wrap faq" id="faq">
      <div class="eyebrow">Questions</div>
      <h2>Quick answers</h2>
      <details open>
        <summary>Is this as accurate as a chest strap or watch?</summary>
        <p>No device is reading your pulse for you. Accuracy depends on finding a real pulse and tapping once per beat. After 10–15 seconds of clean taps the average is usually close to a manual 15-second count. Wearables still win for hands-free and all-day use.</p>
      </details>
      <details>
        <summary>Why did my number jump around at first?</summary>
        <p>The first few taps do not have enough gaps to average. Outlier taps (a double-click, or a missed beat) are filtered when possible. Keep going — confidence moves from Building to Solid as the window fills.</p>
      </details>
      <details>
        <summary>Do you store my heart rate?</summary>
        <p>Not on a server. Saved readings live in this browser’s local storage. Clearing site data, or tapping Clear, removes them. Analytics may record that the page was visited; it does not receive your BPM log.</p>
      </details>
      <details>
        <summary>When should I not rely on this?</summary>
        <p>If you feel unwell, have chest pain, are dizzy, or have been told to monitor a heart condition, use the method your clinician gave you. This page is a convenience tool, not a medical device.</p>
      </details>
    </section>

    <aside class="ad-slot" data-ad="bottom" aria-label="Advertisement">
      <span class="ad-slot__label">Advertisement</span>
      <div class="ad-slot__box">
        <?php render_adsense($adsenseClient); ?>
      </div>
    </aside>

    <section class="section section--tight wrap" id="privacy">
      <div class="note">
        <h2>Privacy, ads, and disclaimer</h2>
        <p><strong>Privacy.</strong> Pulse taps and saved readings never leave your browser. Age and HRmax are stored locally so the page remembers them tomorrow. This site uses Google Analytics to understand traffic. When advertising is enabled, Google AdSense may use cookies to serve and measure ads. You can block cookies in your browser. We do not sell a personal heart-rate database — there isn’t one on the server.</p>
        <p><strong>Disclaimer.</strong> checkheartrate.io is not a medical device and is not a substitute for professional advice, diagnosis, or treatment. Heart-rate formulas and zones are estimates. If you have questions about a medical condition, talk to a qualified clinician. If you think you may be having an emergency, seek emergency care.</p>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div class="wrap footer__row">
      <div>© <?php echo $year; ?> checkheartrate.io · <a href="https://damonlee.io">damonlee.io</a></div>
      <nav>
        <a href="#measure">Measure</a>
        <a href="#how">How to</a>
        <a href="#privacy">Privacy</a>
      </nav>
    </div>
  </footer>

  <div class="toast" id="toast" role="status" aria-live="polite"></div>
  <script src="app.js?v=<?php echo urlencode($v); ?>"></script>
</body>
</html>
