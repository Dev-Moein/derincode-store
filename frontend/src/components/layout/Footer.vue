<template>
    <footer class="footer">
        <div class="container">

            <div class="footer-top">

                <!-- Brand -->
                <div class="footer-brand">
                    <RouterLink
                        to="/"
                        class="logo"
                        aria-label="Derin Code - صفحه اصلی"
                    >
                        <span>Derin</span><strong>Code</strong>
                    </RouterLink>

                    <p>
                        طراحی و توسعه محصولات دیجیتال
                        برای کسب‌وکارهای مدرن.
                    </p>
                </div>

                <!-- Navigation -->
                <nav
                    class="footer-column"
                    aria-label="دسترسی سریع"
                >
                    <h2>دسترسی سریع</h2>

                    <a href="#services">خدمات</a>
                    <a href="#projects">نمونه‌کارها</a>
                    <a href="#process">فرآیند همکاری</a>
                    <a href="#about">درباره ما</a>
                </nav>

                <!-- Technologies -->
                <div class="footer-column">
                    <h2>تکنولوژی‌ها</h2>

                    <span>Laravel</span>
                    <span>Vue.js</span>
                    <span>JavaScript</span>
                    <span>Docker</span>
                </div>

                <!-- Contact -->
                <div class="footer-column contact-column">
                    <h2>شروع همکاری</h2>

                    <p>
                        پروژه‌ای در ذهن دارید؟
                        از ایده‌تان برای ما بگویید.
                    </p>

                    <a
                        href="#contact"
                        class="contact-link"
                    >
                        <span>شروع یک گفتگو</span>

                        <span
                            class="contact-arrow"
                            aria-hidden="true"
                        >
                            ←
                        </span>
                    </a>
                </div>

            </div>

            <div
                class="footer-line"
                aria-hidden="true"
            ></div>

            <div class="footer-bottom">
                <span>
                    © {{ new Date().getFullYear() }} Derin Code
                </span>

                <span class="status">
                    <i aria-hidden="true"></i>
                    آماده همکاری
                </span>
            </div>

        </div>
    </footer>
</template>

<style scoped>
.footer {
    position: relative;

    padding: 68px 0 22px;

    overflow: hidden;

    background: #050607;
}

.footer::before {
    content: "";

    position: absolute;

    width: 380px;
    height: 380px;

    top: -260px;
    left: 50%;

    transform: translateX(-50%);

    border-radius: 50%;

    background: rgba(255, 107, 0, 0.04);

    filter: blur(90px);

    pointer-events: none;
}

/* -------------------------------- */
/* Footer top */
/* -------------------------------- */

.footer-top {
    position: relative;
    z-index: 1;

    display: grid;

    grid-template-columns:
        1.8fr
        1fr
        1fr
        1.25fr;

    gap: 65px;
}

.footer-brand {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

/* -------------------------------- */
/* Logo */
/* -------------------------------- */

.logo {
    direction: ltr;

    display: inline-flex;

    color: inherit;

    font-family: var(--font-mono);
    font-size: 23px;
    font-weight: 800;
    line-height: 1;

    letter-spacing: -0.7px;

    text-decoration: none;
}

.logo span {
    color: var(--text-primary);
}

.logo strong {
    color: var(--orange);
}

.logo:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 6px;
    border-radius: 4px;
}

/* -------------------------------- */
/* Brand description */
/* -------------------------------- */

.footer-brand p {
    max-width: 270px;

    margin-top: 16px;

    color: var(--text-muted);

    font-size: 12px;
    line-height: 2;
}

/* -------------------------------- */
/* Columns */
/* -------------------------------- */

.footer-column {
    display: flex;
    flex-direction: column;
    align-items: flex-start;

    gap: 9px;
}

.footer-column h2 {
    margin-bottom: 8px;

    color: var(--text-primary);

    font-size: 12px;
    font-weight: 750;
    line-height: 1.7;
}

.footer-column a,
.footer-column > span {
    color: #747a80;

    font-size: 11px;
    line-height: 1.7;

    text-decoration: none;

    transition:
        color var(--transition),
        transform var(--transition);
}

.footer-column a:hover {
    color: var(--orange);

    transform: translateX(-3px);
}

.footer-column a:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 4px;
    border-radius: 3px;
}

/* -------------------------------- */
/* Contact */
/* -------------------------------- */

.contact-column p {
    max-width: 205px;

    color: #858b90;

    font-size: 11px;
    line-height: 2;
}

.contact-link {
    margin-top: 5px;

    display: inline-flex;
    align-items: center;
    gap: 9px;

    color: var(--orange);

    font-size: 11px;
    font-weight: 700;
    line-height: 1.7;

    text-decoration: none;

    transition:
        color var(--transition),
        gap var(--transition);
}

.contact-link:hover {
    gap: 13px;
}

.contact-link:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 5px;
    border-radius: 4px;
}

.contact-arrow {
    color: var(--orange);

    font-size: 17px;
    line-height: 1;
}

/* -------------------------------- */
/* Divider */
/* -------------------------------- */

.footer-line {
    position: relative;
    z-index: 1;

    width: 100%;
    height: 1px;

    margin-top: 52px;

    background: var(--border);
}

/* -------------------------------- */
/* Bottom */
/* -------------------------------- */

.footer-bottom {
    position: relative;
    z-index: 1;

    padding-top: 17px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    color: #595f64;

    font-size: 10px;
    line-height: 1.7;
}

.status {
    display: inline-flex;
    align-items: center;
    gap: 7px;
}

.status i {
    width: 6px;
    height: 6px;

    flex-shrink: 0;

    border-radius: 50%;

    background: var(--success);

    box-shadow:
        0 0 0 3px rgba(55, 214, 122, 0.05),
        0 0 10px rgba(55, 214, 122, 0.45);
}

/* -------------------------------- */
/* Responsive */
/* -------------------------------- */

@media (max-width: 850px) {
    .footer-top {
        grid-template-columns: repeat(2, minmax(0, 1fr));

        gap: 38px 35px;
    }

    .footer-brand {
        grid-column: 1 / -1;
    }
}

@media (max-width: 520px) {
    .footer {
        padding-top: 55px;
    }

    .footer-top {
        grid-template-columns: repeat(2, minmax(0, 1fr));

        gap: 34px 20px;
    }

    .footer-brand {
        grid-column: 1 / -1;
    }

    .contact-column {
        grid-column: 1 / -1;
    }

    .footer-brand p {
        max-width: 100%;
    }

    .footer-bottom {
        flex-direction: column;
        align-items: flex-start;

        gap: 10px;
    }
}

@media (max-width: 360px) {
    .footer-top {
        grid-template-columns: 1fr;
    }

    .footer-brand,
    .contact-column {
        grid-column: auto;
    }
}

/* -------------------------------- */
/* Reduced motion */
/* -------------------------------- */

@media (prefers-reduced-motion: reduce) {
    .footer-column a,
    .contact-link {
        transition: none;
    }
}
</style>
