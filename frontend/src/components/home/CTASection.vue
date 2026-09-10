<template>
  <section
    id="cta"
    class="cta-section"
    aria-labelledby="cta-title"
  >
    <div
      class="cta-glow cta-glow-right"
      aria-hidden="true"
    ></div>

    <div
      class="cta-glow cta-glow-left"
      aria-hidden="true"
    ></div>

    <div class="container">
      <div class="cta-content">

        <div class="cta-kicker">
          <span aria-hidden="true"></span>
          شروع همکاری
          <span aria-hidden="true"></span>
        </div>

        <h2
          id="cta-title"
          class="cta-title"
        >
          آماده‌ای ایده‌ات را
          <span>واقعی کنیم؟</span>
        </h2>

        <p class="cta-description">
          درباره پروژه‌تان با ما صحبت کنید. با هم بهترین مسیر را برای تبدیل ایده
          به یک محصول واقعی پیدا می‌کنیم.
        </p>

        <div class="cta-actions">
          <a
            href="#contact"
            class="cta-primary"
            aria-label="شروع یک پروژه و ارسال درخواست همکاری"
          >
            <span>شروع یک پروژه</span>

            <span
              class="cta-arrow"
              aria-hidden="true"
            >
              ←
            </span>
          </a>

          <a
            href="#projects"
            class="cta-secondary"
          >
            مشاهده نمونه‌کارها
          </a>
        </div>

      </div>
    </div>
  </section>
</template>

<style scoped>
.cta-section {
  position: relative;

  padding: 115px 0 120px;

  overflow: hidden;

  background:
    linear-gradient(
      180deg,
      var(--bg-primary),
      #070809 45%,
      var(--bg-primary)
    );
}

.cta-section::before {
  content: "";

  position: absolute;
  inset-inline: 0;
  top: 0;

  height: 1px;

  background:
    linear-gradient(
      90deg,
      transparent,
      rgba(255, 255, 255, 0.08),
      transparent
    );
}

/* ----------------------------- */
/* Glow */
/* ----------------------------- */

.cta-glow {
  position: absolute;

  width: 380px;
  height: 380px;

  border-radius: 50%;

  filter: blur(110px);

  pointer-events: none;
}

.cta-glow-right {
  top: 5%;
  right: -180px;

  background: rgba(255, 107, 0, 0.08);
}

.cta-glow-left {
  bottom: -240px;
  left: -180px;

  background: rgba(255, 107, 0, 0.045);
}

/* ----------------------------- */
/* Content */
/* ----------------------------- */

.cta-content {
  position: relative;
  z-index: 2;

  max-width: 760px;

  margin-inline: auto;

  text-align: center;
}

.cta-kicker {
  display: inline-flex;
  align-items: center;
  gap: 10px;

  color: var(--orange);

  font-size: 13px;
  font-weight: 700;
  line-height: 1.7;
}

.cta-kicker span {
  width: 20px;
  height: 1px;

  flex-shrink: 0;

  background: var(--orange);
}

/* ----------------------------- */
/* Title */
/* ----------------------------- */

.cta-title {
  margin: 19px 0 0;

  color: var(--text-primary);

  font-size: clamp(37px, 5vw, 62px);
  font-weight: 850;
  line-height: 1.32;

  letter-spacing: -0.035em;
}

.cta-title span {
  color: var(--orange);

  text-shadow:
    0 0 30px rgba(255, 107, 0, 0.12);
}

/* ----------------------------- */
/* Description */
/* ----------------------------- */

.cta-description {
  max-width: 550px;

  margin: 20px auto 0;

  color: var(--text-secondary);

  font-size: 14px;
  font-weight: 400;
  line-height: 2.15;
}

/* ----------------------------- */
/* Actions */
/* ----------------------------- */

.cta-actions {
  margin-top: 32px;

  display: flex;
  align-items: center;
  justify-content: center;

  gap: 12px;
}

.cta-primary,
.cta-secondary {
  min-height: 47px;

  display: inline-flex;
  align-items: center;
  justify-content: center;

  border-radius: 8px;

  font-family: inherit;
  font-size: 12px;
  font-weight: 700;
  line-height: 1.7;

  text-decoration: none;

  transition:
    transform var(--transition),
    background var(--transition),
    border-color var(--transition),
    color var(--transition),
    box-shadow var(--transition);
}

/* Primary */

.cta-primary {
  gap: 10px;

  padding: 0 16px 0 11px;

  color: #ffffff;

  background:
    linear-gradient(
      135deg,
      #ff780f,
      #ff5f00
    );

  box-shadow:
    0 10px 30px rgba(255, 107, 0, 0.18);
}

.cta-primary:hover {
  transform: translateY(-2px);

  box-shadow:
    0 14px 38px rgba(255, 107, 0, 0.28);
}

.cta-arrow {
  width: 27px;
  height: 27px;

  display: flex;
  align-items: center;
  justify-content: center;

  border-radius: 50%;

  background: rgba(255, 255, 255, 0.14);

  font-size: 15px;
  line-height: 1;

  transition:
    transform var(--transition),
    background var(--transition);
}

.cta-primary:hover .cta-arrow {
  transform: translateX(-2px);

  background: rgba(255, 255, 255, 0.2);
}

/* Secondary */

.cta-secondary {
  padding: 0 18px;

  color: var(--text-primary);

  border: 1px solid rgba(255, 255, 255, 0.11);

  background: rgba(255, 255, 255, 0.02);
}

.cta-secondary:hover {
  transform: translateY(-2px);

  border-color: rgba(255, 107, 0, 0.35);

  background: rgba(255, 107, 0, 0.045);
}

/* ----------------------------- */
/* Focus */
/* ----------------------------- */

.cta-primary:focus-visible,
.cta-secondary:focus-visible {
  outline: 2px solid var(--orange);
  outline-offset: 4px;
}

/* ----------------------------- */
/* Responsive */
/* ----------------------------- */

@media (max-width: 768px) {
  .cta-section {
    padding: 82px 0 88px;
  }

  .cta-title {
    font-size: 40px;
  }

  .cta-description {
    max-width: 500px;

    font-size: 13px;
    line-height: 2.1;
  }
}

@media (max-width: 500px) {
  .cta-section {
    padding: 70px 0 72px;
  }

  .cta-title {
    font-size: 34px;
    line-height: 1.35;
  }

  .cta-description {
    max-width: 330px;

    font-size: 12px;
    line-height: 2.15;
  }

  .cta-actions {
    flex-direction: column;

    width: 100%;
  }

  .cta-primary,
  .cta-secondary {
    width: 100%;
  }
}

@media (max-width: 380px) {
  .cta-title {
    font-size: 31px;
  }

  .cta-description {
    font-size: 11.5px;
  }
}
</style>
