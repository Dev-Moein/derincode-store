<script setup>
import {
    Globe2,
    ShoppingCart,
    PanelsTopLeft,
    Settings2,
    Boxes,
    ArrowLeft,
} from 'lucide-vue-next'

const services = [
    {
        number: '01',
        title: 'سایت شرکتی',
        description:
            'طراحی و توسعه سایت‌های حرفه‌ای برای معرفی برند و خدمات شما',
        icon: Globe2,
    },
    {
        number: '02',
        title: 'فروشگاه اینترنتی',
        description:
            'فروشگاه‌های سریع، امن و قابل توسعه برای فروش آنلاین',
        icon: ShoppingCart,
    },
    {
        number: '03',
        title: 'پنل مدیریت',
        description:
            'پنل‌های مدیریتی اختصاصی و متناسب با نیاز کسب‌وکار شما',
        icon: PanelsTopLeft,
    },
    {
        number: '04',
        title: 'سیستم‌های اختصاصی',
        description:
            'طراحی و توسعه سیستم‌های سفارشی متناسب با فرآیندهای شما',
        icon: Settings2,
    },
    {
        number: '05',
        title: 'API & Backend',
        description:
            'طراحی API و توسعه بک‌اند امن، مقیاس‌پذیر و استاندارد',
        icon: Boxes,
    },
]
</script>

<template>
    <section
        id="services"
        class="services-section section"
        aria-labelledby="services-title"
    >
        <div class="container">

            <header class="section-heading">
                <span class="section-kicker">
                    خدمات ما
                </span>

                <h2
                    id="services-title"
                    class="section-title"
                >
                    راهکارهایی برای ساخت
                    <span>محصول بهتر</span>
                </h2>

                <p class="section-description">
                    از یک وب‌سایت حرفه‌ای تا سیستم‌های اختصاصی،
                    راهکار مناسب را بر اساس نیاز واقعی کسب‌وکار شما
                    طراحی و توسعه می‌دهیم.
                </p>
            </header>

            <div class="services-grid">
                <article
                    v-for="service in services"
                    :key="service.number"
                    class="service-card"
                >
                    <div class="service-top">
                        <div
                            class="service-icon"
                            aria-hidden="true"
                        >
                            <component
                                :is="service.icon"
                                :size="24"
                                :stroke-width="1.7"
                            />
                        </div>

                        <span
                            class="service-number"
                            aria-hidden="true"
                        >
                            {{ service.number }}
                        </span>
                    </div>

                    <div class="service-body">
                        <h3>
                            {{ service.title }}
                        </h3>

                        <p>
                            {{ service.description }}
                        </p>
                    </div>

                    <a
                        href="#contact"
                        class="service-link"
                        :aria-label="`شروع همکاری برای ${service.title}`"
                    >
                        <span>شروع همکاری</span>

                        <span
                            class="service-arrow"
                            aria-hidden="true"
                        >
                            <ArrowLeft :size="14" />
                        </span>
                    </a>
                </article>
            </div>

        </div>
    </section>
</template>

<style scoped>
.services-section {
    position: relative;

    padding-top: 72px;
    padding-bottom: 95px;
}

/* -------------------------------- */
/* Heading */
/* -------------------------------- */

.section-heading {
    max-width: 650px;

    margin: 0 auto 44px;

    text-align: center;
}

.section-kicker {
    position: relative;

    display: inline-block;

    padding-bottom: 10px;

    color: var(--text-primary);

    font-size: 20px;
    font-weight: 800;
    line-height: 1.6;
}

.section-kicker::after {
    content: "";

    position: absolute;

    left: 50%;
    bottom: 0;

    width: 34px;
    height: 3px;

    transform: translateX(-50%);

    background: var(--orange);
    border-radius: 999px;
}

.section-title {
    margin-top: 18px;

    color: var(--text-primary);

    font-size: clamp(30px, 3.5vw, 42px);
    font-weight: 850;
    line-height: 1.35;

    letter-spacing: -0.035em;
}

.section-title span {
    color: var(--orange);
}

.section-description {
    max-width: 560px;

    margin: 12px auto 0;

    color: var(--text-muted);

    font-size: 13px;
    line-height: 2.15;
}

/* -------------------------------- */
/* Grid */
/* -------------------------------- */

.services-grid {
    display: grid;

    grid-template-columns: repeat(5, minmax(0, 1fr));

    gap: 12px;
}

.service-card {
    position: relative;

    min-height: 220px;

    padding: 18px 15px 15px;

    display: flex;
    flex-direction: column;

    overflow: hidden;

    border: 1px solid rgba(255, 255, 255, 0.075);
    border-radius: 10px;

    background:
        linear-gradient(
            150deg,
            rgba(255, 255, 255, 0.025),
            rgba(255, 255, 255, 0.008)
        );

    transition:
        transform var(--transition),
        border-color var(--transition),
        background var(--transition),
        box-shadow var(--transition);
}

.service-card::before {
    content: "";

    position: absolute;
    inset: 0;

    pointer-events: none;

    background:
        radial-gradient(
            circle at 50% 0%,
            rgba(255, 107, 0, 0.07),
            transparent 58%
        );

    opacity: 0;

    transition: opacity var(--transition);
}

.service-card:hover {
    transform: translateY(-5px);

    border-color: rgba(255, 107, 0, 0.32);

    background:
        linear-gradient(
            150deg,
            rgba(255, 107, 0, 0.04),
            rgba(255, 255, 255, 0.008)
        );

    box-shadow:
        0 18px 45px rgba(0, 0, 0, 0.22);
}

.service-card:hover::before {
    opacity: 1;
}

/* -------------------------------- */
/* Top */
/* -------------------------------- */

.service-top {
    position: relative;
    z-index: 1;

    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}

.service-icon {
    width: 44px;
    height: 44px;

    display: flex;
    align-items: center;
    justify-content: center;

    color: var(--orange);

    border: 1px solid rgba(255, 107, 0, 0.25);
    border-radius: 10px;

    background: rgba(255, 107, 0, 0.035);
}

.service-number {
    direction: ltr;

    color: #3e4347;

    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 500;
    line-height: 1.5;
}

/* -------------------------------- */
/* Body */
/* -------------------------------- */

.service-body {
    position: relative;
    z-index: 1;

    margin-top: 25px;
}

.service-body h3 {
    color: var(--text-primary);

    font-size: 14px;
    font-weight: 750;
    line-height: 1.7;
}

.service-body p {
    margin-top: 8px;

    color: var(--text-muted);

    font-size: 11px;
    line-height: 2;
}

/* -------------------------------- */
/* Link */
/* -------------------------------- */

.service-link {
    position: relative;
    z-index: 1;

    width: fit-content;

    margin-top: auto;
    padding-top: 19px;

    display: inline-flex;
    align-items: center;
    gap: 8px;

    color: var(--text-secondary);

    font-size: 11px;
    font-weight: 700;
    line-height: 1.7;

    text-decoration: none;

    transition: color var(--transition);
}

.service-card:hover .service-link {
    color: var(--orange);
}

.service-link:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 5px;
    border-radius: 4px;
}

.service-arrow {
    width: 24px;
    height: 24px;

    display: flex;
    align-items: center;
    justify-content: center;

    color: var(--orange);

    border: 1px solid rgba(255, 107, 0, 0.18);
    border-radius: 6px;

    background: rgba(255, 107, 0, 0.035);

    transition:
        background var(--transition),
        transform var(--transition);
}

.service-link:hover .service-arrow {
    background: rgba(255, 107, 0, 0.09);

    transform: translateX(-2px);
}

/* -------------------------------- */
/* Responsive */
/* -------------------------------- */

@media (max-width: 1100px) {
    .services-grid {
        gap: 10px;
    }

    .service-card {
        padding-left: 13px;
        padding-right: 13px;
    }

    .service-body p {
        font-size: 10px;
    }
}

@media (max-width: 1000px) {
    .services-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
    }

    .service-card {
        padding: 18px 15px 15px;
    }

    .service-body p {
        font-size: 11px;
    }
}

@media (max-width: 700px) {
    .services-section {
        padding-top: 65px;
    }

    .section-heading {
        margin-bottom: 34px;
    }

    .section-kicker {
        font-size: 20px;
    }

    .section-title {
        font-size: clamp(29px, 7vw, 36px);
    }

    .section-description {
        font-size: 12px;
        line-height: 2.1;
    }

    .services-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 9px;
    }

    .service-card {
        min-height: 205px;

        padding: 16px 13px 14px;
    }

    .service-icon {
        width: 42px;
        height: 42px;
    }

    .service-body {
        margin-top: 22px;
    }

    .service-body h3 {
        font-size: 14px;
    }

    .service-body p {
        font-size: 10px;
    }

    .service-link {
        font-size: 10px;
    }
}

@media (max-width: 480px) {
    .services-grid {
        grid-template-columns: 1fr;
    }

    .service-card {
        min-height: 185px;

        padding: 17px 15px 15px;
    }

    .service-body {
        margin-top: 23px;
    }

    .service-body p {
        font-size: 11px;
    }

    .service-link {
        font-size: 11px;
    }
}

/* -------------------------------- */
/* Reduced motion */
/* -------------------------------- */

@media (prefers-reduced-motion: reduce) {
    .service-card,
    .service-card::before,
    .service-link,
    .service-arrow {
        transition: none;
    }
}
</style>
