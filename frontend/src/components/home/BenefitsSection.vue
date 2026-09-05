<template>
    <header class="navbar-wrapper">
        <nav class="navbar container">

            <RouterLink to="/" class="logo">
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
                شروع پروژه
                <span>←</span>
            </RouterLink>

        </nav>
    </header>
</template>

<style scoped>
.navbar-wrapper {
    padding-top: 18px;
}

.navbar {
    min-height: 64px;
    padding: 0 22px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    border: 1px solid var(--border);
    border-radius: 14px;

    background: rgba(8, 9, 10, 0.85);
    backdrop-filter: blur(12px);
}

.logo {
    direction: ltr;
    font-size: 22px;
    font-weight: 800;
}

.logo span {
    color: var(--orange);
}

.nav-links {
    display: flex;
    gap: 32px;
    color: var(--text-secondary);
    font-size: 14px;
}

.nav-links a,
.nav-links .router-link-active {
    transition: var(--transition);
}

.nav-links a:hover,
.nav-links .router-link-active {
    color: var(--orange);
}

.start-button {
    direction: rtl;
    padding: 10px 18px;
    border: 1px solid var(--orange);
    border-radius: 8px;
    color: white;
    font-size: 13px;
    transition: var(--transition);
}

.start-button span {
    color: var(--orange);
    margin-right: 8px;
}

.start-button:hover {
    background: var(--orange);
}

.start-button:hover span {
    color: white;
}

@media (max-width: 850px) {
    .nav-links {
        display: none;
    }

    .navbar {
        padding: 0 14px;
    }
}
</style>
