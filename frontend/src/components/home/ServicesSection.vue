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
        title: 'سایت شرکتی',
        description: 'طراحی و توسعه سایت‌های حرفه‌ای برای معرفی برند و خدمات شما',
        icon: Globe2,
    },
    {
        title: 'فروشگاه اینترنتی',
        description: 'فروشگاه‌های سریع، امن و قابل توسعه برای فروش آنلاین',
        icon: ShoppingCart,
    },
    {
        title: 'پنل مدیریت',
        description: 'پنل‌های مدیریتی اختصاصی و متناسب با نیاز کسب‌وکار شما',
        icon: PanelsTopLeft,
    },
    {
        title: 'سیستم‌های اختصاصی',
        description: 'طراحی و توسعه سیستم‌های سفارشی متناسب با فرآیندهای شما',
        icon: Settings2,
    },
    {
        title: 'API & Backend',
        description: 'طراحی API و توسعه بک‌اند امن، مقیاس‌پذیر و استاندارد',
        icon: Boxes,
    },
]
</script>

<template>
    <section
        id="services"
        class="services-section section"
    >
        <div class="container">
            <div class="section-heading">
                <span class="section-kicker">
                    خدمات ما
                </span>

                <h2 class="section-title">
                    راهکارهایی برای ساخت
                    <span>محصول بهتر</span>
                </h2>

                <p class="section-description">
                    از یک وب‌سایت حرفه‌ای تا سیستم‌های اختصاصی،
                    راهکار مناسب را بر اساس نیاز واقعی کسب‌وکار شما
                    طراحی و توسعه می‌دهیم.
                </p>
            </div>

            <div class="services-grid">
                <article
                    v-for="service in services"
                    :key="service.title"
                    class="service-card"
                >
                    <div class="service-top">
                        <div class="service-icon">
                            <component
                                :is="service.icon"
                                :size="24"
                                :stroke-width="1.7"
                            />
                        </div>

                        <span class="service-number">
                            0{{ services.indexOf(service) + 1 }}
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
                    >
                        <span>شروع همکاری</span>

                        <span class="service-arrow">
                            <ArrowLeft :size="13" />
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

.section-heading {
    max-width: 650px;

    margin: 0 auto 42px;

    text-align: center;
}

.section-kicker {
    display: inline-block;

    position: relative;

    padding-bottom: 13px;

    color: var(--text-primary);

    font-size: 25px;
    font-weight: 900;

    line-height: 1.3;
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
    border-radius: 99px;
}

.section-title {
    margin-top: 22px;

    color: var(--text-primary);

    font-size: 17px;
    font-weight: 700;
}

.section-title span {
    color: var(--orange);
}

.section-description {
    max-width: 560px;

    margin: 12px auto 0;

    color: var(--text-muted);

    font-size: 12px;
    line-height: 2.1;
}

.services-grid {
    display: grid;

    grid-template-columns: repeat(5, minmax(0, 1fr));

    gap: 10px;
}

.service-card {
    position: relative;

    min-height: 220px;

    padding: 17px 15px 14px;

    display: flex;
    flex-direction: column;

    border: 1px solid rgba(255, 255, 255, 0.075);
    border-radius: 10px;

    background:
        linear-gradient(
            150deg,
            rgba(255, 255, 255, 0.025),
            rgba(255, 255, 255, 0.008)
        );

    overflow: hidden;

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

    background:
        radial-gradient(
            circle at 50% 0%,
            rgba(255, 107, 0, 0.07),
            transparent 58%
        );

    opacity: 0;

    transition: opacity var(--transition);

    pointer-events: none;
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

.service-top {
    position: relative;
    z-index: 1;

    display: flex;
    align-items: flex-start;
    justify-content: space-between;
}

.service-icon {
    width: 43px;
    height: 43px;

    display: flex;
    align-items: center;
    justify-content: center;

    color: var(--orange);

    border: 1px solid rgba(255, 107, 0, 0.25);
    border-radius: 10px;

    background:
        rgba(255, 107, 0, 0.035);
}

.service-number {
    color: #3e4347;

    direction: ltr;

    font-family: var(--font-mono);
    font-size: 9px;
}

.service-body {
    position: relative;
    z-index: 1;

    margin-top: 26px;
}

.service-body h3 {
    color: var(--text-primary);

    font-size: 14px;
    font-weight: 800;

    line-height: 1.6;
}

.service-body p {
    margin-top: 9px;

    color: var(--text-muted);

    font-size: 10px;
    line-height: 2;
}

.service-link {
    position: relative;
    z-index: 1;

    width: fit-content;

    margin-top: auto;
    padding-top: 20px;

    display: inline-flex;
    align-items: center;
    gap: 8px;

    color: var(--text-secondary);

    font-size: 9px;
    font-weight: 700;

    transition: color var(--transition);
}

.service-card:hover .service-link {
    color: var(--orange);
}

.service-arrow {
    width: 23px;
    height: 23px;

    display: flex;
    align-items: center;
    justify-content: center;

    border: 1px solid rgba(255, 107, 0, 0.18);
    border-radius: 6px;

    color: var(--orange);

    background:
        rgba(255, 107, 0, 0.035);

    transition:
        background var(--transition),
        transform var(--transition);
}

.service-link:hover .service-arrow {
    background: rgba(255, 107, 0, 0.09);

    transform: translateX(-2px);
}

@media (max-width: 1000px) {
    .services-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 700px) {
    .services-section {
        padding-top: 65px;
    }

    .section-heading {
        margin-bottom: 32px;
    }

    .section-kicker {
        font-size: 21px;
    }

    .section-title {
        font-size: 16px;
    }

    .section-description {
        font-size: 11px;
    }

    .services-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }

    .service-card {
        min-height: 205px;
        padding: 15px 13px 13px;
    }
}

@media (max-width: 480px) {
    .services-grid {
        grid-template-columns: 1fr;
    }

    .service-card {
        min-height: 185px;
    }
}
</style>
