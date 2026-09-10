<script setup>
import {
    MessageSquare,
    Search,
    PenTool,
    Code2,
    Rocket,
} from 'lucide-vue-next'

const steps = [
    {
        number: '01',
        title: 'گفتگو',
        description:
            'نیازها، ایده و اهداف پروژه را با هم بررسی می‌کنیم.',
        icon: MessageSquare,
    },
    {
        number: '02',
        title: 'تحلیل و برنامه‌ریزی',
        description:
            'راه‌حل فنی، امکانات و مسیر اجرای پروژه مشخص می‌شود.',
        icon: Search,
    },
    {
        number: '03',
        title: 'طراحی',
        description:
            'ساختار و تجربه کاربری محصول قبل از توسعه شکل می‌گیرد.',
        icon: PenTool,
    },
    {
        number: '04',
        title: 'توسعه',
        description:
            'محصول با کدنویسی استاندارد و معماری قابل توسعه ساخته می‌شود.',
        icon: Code2,
    },
    {
        number: '05',
        title: 'تحویل و همراهی',
        description:
            'محصول نهایی تحویل داده می‌شود و در مسیر توسعه همراه شما هستیم.',
        icon: Rocket,
    },
]
</script>

<template>
    <section
        id="process"
        class="process-section section"
        aria-labelledby="process-title"
    >
        <div class="container">

            <div class="process-heading">
                <span class="section-kicker">
                    فرآیند همکاری
                </span>

                <h2 id="process-title">
                    از ایده تا
                    <span>محصول نهایی</span>
                </h2>

                <p>
                    یک مسیر شفاف و مرحله‌به‌مرحله برای تبدیل ایده
                    به محصولی که واقعاً بتواند رشد کند.
                </p>
            </div>

            <div
                class="process-list"
                role="list"
            >
                <article
                    v-for="(step, index) in steps"
                    :key="step.number"
                    class="process-step"
                    role="listitem"
                >
                    <div
                        class="step-number"
                        aria-hidden="true"
                    >
                        {{ step.number }}
                    </div>

                    <div
                        class="step-icon"
                        aria-hidden="true"
                    >
                        <component
                            :is="step.icon"
                            :size="22"
                            :stroke-width="1.7"
                        />
                    </div>

                    <div class="step-content">
                        <h3>
                            {{ step.title }}
                        </h3>

                        <p>
                            {{ step.description }}
                        </p>
                    </div>

                    <div
                        v-if="index < steps.length - 1"
                        class="step-line"
                        aria-hidden="true"
                    ></div>
                </article>
            </div>

        </div>
    </section>
</template>

<style scoped>
.process-section {
    padding-top: 70px;
    padding-bottom: 90px;
}

/* ----------------------------- */
/* Heading */
/* ----------------------------- */

.process-heading {
    max-width: 620px;

    margin: 0 auto 48px;

    text-align: center;
}

.section-kicker {
    position: relative;

    display: inline-block;

    padding-bottom: 10px;

    color: var(--text-primary);

    font-size: 21px;
    font-weight: 800;
    line-height: 1.6;
}

.section-kicker::after {
    content: "";

    position: absolute;

    right: 50%;
    bottom: 0;

    width: 34px;
    height: 3px;

    transform: translateX(50%);

    background: var(--orange);

    border-radius: 999px;
}

.process-heading h2 {
    margin-top: 20px;

    color: var(--text-primary);

    font-size: clamp(30px, 3.5vw, 42px);
    font-weight: 850;
    line-height: 1.35;

    letter-spacing: -0.035em;
}

.process-heading h2 span {
    color: var(--orange);
}

.process-heading p {
    max-width: 500px;

    margin: 14px auto 0;

    color: var(--text-muted);

    font-size: 13px;
    font-weight: 400;
    line-height: 2.15;
}

/* ----------------------------- */
/* Process List */
/* ----------------------------- */

.process-list {
    display: grid;

    grid-template-columns: repeat(5, 1fr);

    overflow: hidden;

    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 14px;

    background:
        linear-gradient(
            145deg,
            rgba(255, 255, 255, 0.022),
            rgba(255, 255, 255, 0.007)
        );
}

.process-step {
    position: relative;

    min-height: 235px;

    padding: 20px 18px 19px;

    display: flex;
    flex-direction: column;

    border-left: 1px solid rgba(255, 255, 255, 0.07);

    transition:
        background var(--transition);
}

.process-step:last-child {
    border-left: 0;
}

.process-step:hover {
    background: rgba(255, 107, 0, 0.025);
}

/* ----------------------------- */
/* Number */
/* ----------------------------- */

.step-number {
    color: #3b4044;

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 10px;
    font-weight: 700;
    line-height: 1.5;
}

/* ----------------------------- */
/* Icon */
/* ----------------------------- */

.step-icon {
    width: 44px;
    height: 44px;

    margin-top: 28px;

    display: flex;
    align-items: center;
    justify-content: center;

    color: var(--orange);

    border: 1px solid rgba(255, 107, 0, 0.22);
    border-radius: 10px;

    background: rgba(255, 107, 0, 0.035);

    transition:
        background var(--transition),
        border-color var(--transition),
        transform var(--transition);
}

.process-step:hover .step-icon {
    background: rgba(255, 107, 0, 0.07);

    border-color: rgba(255, 107, 0, 0.4);

    transform: translateY(-3px);
}

/* ----------------------------- */
/* Content */
/* ----------------------------- */

.step-content {
    margin-top: 18px;
}

.step-content h3 {
    color: var(--text-primary);

    font-size: 14px;
    font-weight: 750;
    line-height: 1.7;
}

.step-content p {
    margin-top: 7px;

    color: var(--text-muted);

    font-size: 11px;
    font-weight: 400;
    line-height: 2;
}

/* ----------------------------- */
/* Connecting Line */
/* ----------------------------- */

.step-line {
    position: absolute;

    left: -1px;
    top: 67px;

    width: 1px;
    height: 30px;

    background:
        linear-gradient(
            transparent,
            var(--orange),
            transparent
        );

    opacity: 0.35;
}

/* ----------------------------- */
/* Responsive */
/* ----------------------------- */

@media (max-width: 1100px) {
    .process-step {
        padding-inline: 15px;
    }

    .step-content h3 {
        font-size: 13px;
    }

    .step-content p {
        font-size: 10px;
    }
}

@media (max-width: 950px) {
    .process-list {
        grid-template-columns: repeat(3, 1fr);
    }

    .process-step {
        min-height: 225px;

        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    }

    .process-step:nth-child(3) {
        border-left: 0;
    }

    .process-step:nth-child(4),
    .process-step:nth-child(5) {
        border-bottom: 0;
    }
}

@media (max-width: 650px) {
    .process-section {
        padding-top: 55px;
        padding-bottom: 70px;
    }

    .process-heading {
        margin-bottom: 35px;
    }

    .process-heading h2 {
        font-size: 32px;
    }

    .process-heading p {
        font-size: 12px;
        line-height: 2.1;
    }

    .process-list {
        grid-template-columns: 1fr 1fr;
    }

    .process-step {
        min-height: 210px;
    }

    .process-step:nth-child(3) {
        border-left: 1px solid rgba(255, 255, 255, 0.07);
    }

    .process-step:nth-child(even) {
        border-left: 0;
    }

    .process-step:nth-child(4) {
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    }

    .process-step:nth-child(5) {
        grid-column: 1 / -1;

        border-left: 0;
        border-bottom: 0;
    }

    .step-line {
        display: none;
    }
}

@media (max-width: 450px) {
    .process-list {
        grid-template-columns: 1fr;
    }

    .process-step,
    .process-step:nth-child(3),
    .process-step:nth-child(4) {
        min-height: auto;

        padding: 19px 17px 20px;

        border-left: 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    }

    .process-step:last-child {
        border-bottom: 0;
    }

    .step-icon {
        margin-top: 20px;
    }

    .step-content h3 {
        font-size: 14px;
    }

    .step-content p {
        font-size: 11px;
        line-height: 2;
    }
}
</style>

