<template>
    <header class="navbar-wrapper">
        <nav class="navbar container" aria-label="ناوبری اصلی">

            <RouterLink to="/" class="logo" aria-label="DerinCode - صفحه اصلی">
                Derin<span>Code</span>
            </RouterLink>

            <div class="nav-links">
                <RouterLink to="/">خانه</RouterLink>
                <a href="#services">خدمات</a>
                <a href="#projects">نمونه کارها</a>
                <a href="#about">درباره ما</a>
                <a href="#contact">تماس با ما</a>
            </div>

            <RouterLink to="/request" class="start-button">
                <span class="button-text">شروع پروژه</span>
                <span class="button-arrow" aria-hidden="true">←</span>
            </RouterLink>

        </nav>
    </header>
</template>

<style scoped>
.navbar-wrapper {
    padding-top: 18px;
}

.navbar {
    min-height: 66px;
    padding: 0 22px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    border: 1px solid var(--border);
    border-radius: 14px;

    background: rgba(8, 9, 10, 0.88);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
}

/* Logo */
.logo {
    direction: ltr;
    display: inline-flex;
    align-items: center;

    font-family: inherit;
    font-size: 22px;
    font-weight: 800;
    line-height: 1;

    letter-spacing: -0.4px;
    color: var(--text-primary);

    white-space: nowrap;
}

.logo span {
    color: var(--orange);
}

/* Navigation */
.nav-links {
    display: flex;
    align-items: center;
    gap: 30px;

    font-size: 14px;
    font-weight: 500;
    line-height: 1.7;

    color: var(--text-secondary);
}

.nav-links a {
    position: relative;

    padding: 6px 0;

    color: inherit;
    text-decoration: none;

    transition:
        color 0.2s ease,
        opacity 0.2s ease;
}

.nav-links a::after {
    content: "";

    position: absolute;
    right: 0;
    bottom: 0;

    width: 0;
    height: 1px;

    background: var(--orange);

    transition: width 0.2s ease;
}

.nav-links a:hover,
.nav-links .router-link-active {
    color: var(--orange);
}

.nav-links a:hover::after,
.nav-links .router-link-active::after {
    width: 100%;
}

/* CTA */
.start-button {
    direction: rtl;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-height: 40px;
    padding: 0 16px;

    border: 1px solid var(--orange);
    border-radius: 8px;

    color: #fff;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.6;

    text-decoration: none;

    transition:
        background-color 0.2s ease,
        color 0.2s ease,
        transform 0.2s ease;
}

.button-arrow {
    direction: ltr;

    margin-right: 8px;

    color: var(--orange);
    font-size: 15px;
    line-height: 1;

    transition:
        color 0.2s ease,
        transform 0.2s ease;
}

.start-button:hover {
    background: var(--orange);
    color: #fff;
    transform: translateY(-1px);
}

.start-button:hover .button-arrow {
    color: #fff;
    transform: translateX(-2px);
}

/* Tablet / Mobile */
@media (max-width: 850px) {
    .navbar {
        min-height: 62px;
        padding: 0 14px;
    }

    .nav-links {
        display: none;
    }

    .logo {
        font-size: 20px;
    }

    .start-button {
        min-height: 38px;
        padding: 0 14px;

        font-size: 12px;
    }
}

@media (max-width: 420px) {
    .navbar-wrapper {
        padding-top: 12px;
    }

    .navbar {
        border-radius: 12px;
    }

    .logo {
        font-size: 19px;
    }

    .start-button {
        padding: 0 12px;
    }

    .button-arrow {
        margin-right: 6px;
    }
}
</style>
