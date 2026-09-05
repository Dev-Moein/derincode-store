<template>
  <section
    id="cta"
    class="cta-section"
>
    <div class="cta-glow cta-glow-right"></div>
    <div class="cta-glow cta-glow-left"></div>

    <div class="container">
      <div class="cta-content">
        <div class="cta-kicker">
          <span></span>
          شروع همکاری
          <span></span>
        </div>

        <h2 class="cta-title">
          آماده‌ای ایده‌ات را
          <span>واقعی کنیم؟</span>
        </h2>

        <p class="cta-description">
          درباره پروژه‌تان با ما صحبت کنید. با هم بهترین مسیر را برای تبدیل ایده
          به یک محصول واقعی پیدا می‌کنیم.
        </p>

        <div class="cta-actions">
          <a href="#contact" class="cta-primary">
            <span>شروع یک پروژه</span>
            <span class="cta-arrow">←</span>
          </a>

          <a href="#projects" class="cta-secondary"> مشاهده نمونه‌کارها </a>
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

  background: linear-gradient(
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

  background: linear-gradient(
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
  gap: 9px;

  color: var(--orange);

  font-size: 11px;
  font-weight: 700;
}

.cta-kicker span {
  width: 18px;
  height: 1px;

  background: var(--orange);
}

.cta-title {
  margin: 19px 0 0;

  color: var(--text-primary);

  font-size: clamp(37px, 5vw, 62px);
  line-height: 1.2;

  font-weight: 900;

  letter-spacing: -0.045em;
}

.cta-title span {
  color: var(--orange);

  text-shadow: 0 0 30px rgba(255, 107, 0, 0.12);
}

.cta-description {
  max-width: 550px;

  margin: 20px auto 0;

  color: var(--text-secondary);

  font-size: 13px;
  line-height: 2.15;
}

/* ----------------------------- */
/* Actions */
/* ----------------------------- */

.cta-actions {
  margin-top: 31px;

  display: flex;
  align-items: center;
  justify-content: center;
  gap: 11px;
}

.cta-primary,
.cta-secondary {
  min-height: 46px;

  display: inline-flex;
  align-items: center;
  justify-content: center;

  border-radius: 8px;

  font-size: 11px;
  font-weight: 700;

  transition:
    transform var(--transition),
    background var(--transition),
    border-color var(--transition),
    color var(--transition),
    box-shadow var(--transition);
}

.cta-primary {
  gap: 10px;

  padding: 0 15px 0 10px;

  color: #ffffff;

  background: linear-gradient(135deg, #ff780f, #ff5f00);

  box-shadow: 0 10px 30px rgba(255, 107, 0, 0.18);
}

.cta-primary:hover {
  transform: translateY(-2px);

  box-shadow: 0 14px 38px rgba(255, 107, 0, 0.28);
}

.cta-arrow {
  width: 26px;
  height: 26px;

  display: flex;
  align-items: center;
  justify-content: center;

  border-radius: 50%;

  background: rgba(255, 255, 255, 0.14);

  font-size: 15px;

  transition: transform var(--transition);
}

.cta-primary:hover .cta-arrow {
  transform: translateX(-2px);
}

.cta-secondary {
  padding: 0 17px;

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
        font-size: 12px;
    }
}

@media (max-width: 500px) {
    .cta-section {
        padding: 70px 0 72px;
    }

    .cta-title {
        font-size: 34px;
        line-height: 1.3;
    }

    .cta-description {
        max-width: 330px;
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
</style>
