<script setup>
import CreateProjectRequestForm from '../components/project/CreateProjectRequestForm.vue'
</script>

<template>
    <main
        class="request-page"
        aria-labelledby="request-title"
    >
        <div class="container">

            <header class="hero">
                <span class="eyebrow">
                    DERINCODE / PROJECT REQUEST
                </span>

                <h1 id="request-title">
                    پروژه خودت را با ما بساز
                </h1>

                <p>
                    نیازمندی‌های پروژه، امکانات موردنظر و بودجه تقریبی
                    خودت را ارسال کن تا بررسی کنیم و با شما تماس بگیریم.
                </p>
            </header>

            <section
                class="request-form-section"
                aria-label="فرم درخواست پروژه"
            >
                <CreateProjectRequestForm />
            </section>

        </div>
    </main>
</template>

<style scoped>
/* ================================================================ */
/* Page */
/* ================================================================ */

.request-page {
    min-height: 100vh;

    padding: 70px 20px 100px;

    background:
        radial-gradient(
            circle at 50% 0,
            rgba(255, 107, 0, 0.05),
            transparent 30rem
        ),
        var(--bg-primary);

    color: var(--text-primary);
}

/* ================================================================ */
/* Container */
/* ================================================================ */

.container {
    width: min(100%, 920px);

    margin: 0 auto;
}

/* ================================================================ */
/* Hero */
/* ================================================================ */

.hero {
    max-width: 700px;

    margin: 0 auto 35px;

    text-align: center;
}

.eyebrow {
    display: inline-block;

    color: var(--orange);

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 10px;
    font-weight: 600;

    line-height: 1.7;

    letter-spacing: 0.08em;
}

.hero h1 {
    margin-top: 10px;

    color: var(--text-primary);

    font-size:
        clamp(30px, 5vw, 48px);

    line-height: 1.3;

    font-weight: 900;

    letter-spacing: -0.045em;
}

.hero p {
    max-width: 620px;

    margin: 14px auto 0;

    color: var(--text-muted);

    font-size: 13px;

    line-height: 2.1;
}

/* ================================================================ */
/* Form Section */
/* ================================================================ */

.request-form-section {
    width: 100%;
}

/* ================================================================ */
/* Responsive */
/* ================================================================ */

@media (max-width: 650px) {
    .request-page {
        padding:
            50px 16px 70px;
    }

    .hero {
        margin-bottom: 28px;
    }

    .hero h1 {
        font-size: clamp(28px, 8vw, 38px);
    }

    .hero p {
        font-size: 12px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .request-page {
        scroll-behavior: auto;
    }
}
</style>
