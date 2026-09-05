<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import {
    ArrowLeft,
    LogOut,
    Menu,
    UserRound,
    X,
} from 'lucide-vue-next'

import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const mobileOpen = ref(false)
const userMenuOpen = ref(false)

const closeMobile = () => {
    mobileOpen.value = false
}

const closeUserMenu = () => {
    userMenuOpen.value = false
}

const goToProfile = () => {
    closeUserMenu()
    closeMobile()

    router.push({
        name: 'profile',
    })
}

const logout = async () => {
    closeUserMenu()
    closeMobile()

    await authStore.logout()

    router.push({
        name: 'home',
    })
}
</script>

<template>
    <header class="navbar-wrapper">
        <nav class="navbar container">

            <!-- Logo -->
            <RouterLink
                to="/"
                class="logo"
                @click="closeMobile"
            >
                <span class="logo-light">Derin</span><span class="logo-orange">Code</span>
            </RouterLink>

            <!-- Desktop Navigation -->
            <div class="desktop-nav">
                <RouterLink
                    to="/"
                    class="nav-link"
                >
                    خانه
                </RouterLink>

                <a
                    href="#services"
                    class="nav-link"
                >
                    خدمات
                </a>

                <a
                    href="#projects"
                    class="nav-link"
                >
                    نمونه‌کارها
                </a>

                <a
                    href="#process"
                    class="nav-link"
                >
                    فرآیند همکاری
                </a>

                <a
                    href="#contact"
                    class="nav-link"
                >
                    تماس با ما
                </a>
            </div>

            <!-- Guest -->
            <a
                v-if="!authStore.isAuthenticated"
                href="#contact"
                class="start-button"
            >
                شروع یک پروژه

                <ArrowLeft
                    :size="15"
                    class="button-arrow"
                />
            </a>

            <!-- Authenticated -->
            <div
                v-else
                class="user-area"
            >
                <button
                    type="button"
                    class="user-button"
                    :aria-expanded="userMenuOpen"
                    @click="userMenuOpen = !userMenuOpen"
                >
                    <span class="user-avatar">
                        {{ authStore.userName.charAt(0).toUpperCase() }}
                    </span>

                    <span class="user-name">
                        {{ authStore.userName }}
                    </span>
                </button>

                <Transition name="dropdown">
                    <div
                        v-if="userMenuOpen"
                        class="user-dropdown"
                    >
                        <button
                            type="button"
                            @click="goToProfile"
                        >
                            <UserRound :size="15" />
                            پروفایل
                        </button>

                        <button
                            type="button"
                            class="logout-item"
                            @click="logout"
                        >
                            <LogOut :size="15" />
                            خروج
                        </button>
                    </div>
                </Transition>
            </div>

            <!-- Mobile Toggle -->
            <button
                type="button"
                class="mobile-toggle"
                :aria-label="
                    mobileOpen
                        ? 'بستن منو'
                        : 'باز کردن منو'
                "
                :aria-expanded="mobileOpen"
                @click="mobileOpen = !mobileOpen"
            >
                <X
                    v-if="mobileOpen"
                    :size="19"
                />

                <Menu
                    v-else
                    :size="19"
                />
            </button>

            <!-- Mobile Menu -->
            <Transition name="mobile-menu">
                <div
                    v-if="mobileOpen"
                    class="mobile-menu"
                >
                    <RouterLink
                        to="/"
                        class="mobile-link"
                        @click="closeMobile"
                    >
                        خانه
                    </RouterLink>

                    <a
                        href="#services"
                        class="mobile-link"
                        @click="closeMobile"
                    >
                        خدمات
                    </a>

                    <a
                        href="#projects"
                        class="mobile-link"
                        @click="closeMobile"
                    >
                        نمونه‌کارها
                    </a>

                    <a
                        href="#process"
                        class="mobile-link"
                        @click="closeMobile"
                    >
                        فرآیند همکاری
                    </a>

                    <a
                        href="#contact"
                        class="mobile-link"
                        @click="closeMobile"
                    >
                        تماس با ما
                    </a>

                    <template v-if="authStore.isAuthenticated">
                        <button
                            type="button"
                            class="mobile-auth-link"
                            @click="goToProfile"
                        >
                            <UserRound :size="15" />
                            پروفایل
                        </button>

                        <button
                            type="button"
                            class="mobile-auth-link logout"
                            @click="logout"
                        >
                            <LogOut :size="15" />
                            خروج
                        </button>
                    </template>

                    <a
                        v-else
                        href="#contact"
                        class="mobile-start"
                        @click="closeMobile"
                    >
                        شروع یک پروژه
                        <ArrowLeft :size="15" />
                    </a>
                </div>
            </Transition>

        </nav>
    </header>
</template>

<style scoped>
.navbar-wrapper {
    position: relative;
    z-index: 100;

    padding-top: 18px;
}

.navbar {
    position: relative;

    min-height: 68px;

    padding: 0 18px 0 12px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    gap: 24px;

    border: 1px solid var(--border);
    border-radius: 16px;

    background: rgba(8, 10, 12, 0.84);

    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);

    box-shadow:
        0 10px 40px rgba(0, 0, 0, 0.18);
}

.logo {
    flex-shrink: 0;

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 21px;
    font-weight: 800;

    letter-spacing: -0.05em;
}

.logo-light {
    color: var(--text-primary);
}

.logo-orange {
    color: var(--orange);
}

.desktop-nav {
    display: flex;
    align-items: center;

    gap: 30px;

    margin-inline: auto;
}

.nav-link {
    position: relative;

    color: var(--text-secondary);

    font-size: 13px;
    font-weight: 600;

    transition: color var(--transition);
}

.nav-link::after {
    content: "";

    position: absolute;

    right: 0;
    bottom: -8px;

    width: 0;
    height: 1px;

    background: var(--orange);

    transition: width var(--transition);
}

.nav-link:hover,
.nav-link.router-link-active {
    color: var(--text-primary);
}

.nav-link:hover::after,
.nav-link.router-link-active::after {
    width: 100%;
}

.start-button {
    flex-shrink: 0;

    min-height: 44px;

    padding: 0 15px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 9px;

    color: var(--text-primary);

    border: 1px solid var(--border-orange);
    border-radius: 9px;

    background:
        rgba(255, 107, 0, 0.055);

    font-size: 12px;
    font-weight: 700;

    transition:
        color var(--transition),
        background var(--transition),
        border-color var(--transition),
        transform var(--transition);
}

.start-button:hover {
    color: #090909;

    border-color: var(--orange);

    background: var(--orange);

    transform: translateY(-1px);
}

.button-arrow {
    color: var(--orange);

    transition: color var(--transition);
}

.start-button:hover .button-arrow {
    color: #090909;
}

/* User */

.user-area {
    position: relative;
}

.user-button {
    min-height: 44px;

    padding: 0 10px 0 8px;

    display: flex;
    align-items: center;

    gap: 8px;

    color: var(--text-primary);

    border: 1px solid var(--border);
    border-radius: 9px;

    background:
        rgba(255, 255, 255, 0.025);

    cursor: pointer;

    transition:
        border-color var(--transition),
        background var(--transition);
}

.user-button:hover {
    border-color: var(--border-orange);

    background:
        rgba(255, 107, 0, 0.04);
}

.user-avatar {
    width: 28px;
    height: 28px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    color: #111;

    background: var(--orange);

    font-size: 10px;
    font-weight: 900;
}

.user-name {
    max-width: 100px;

    overflow: hidden;

    color: var(--text-secondary);

    font-size: 10px;

    text-overflow: ellipsis;
    white-space: nowrap;
}

.user-dropdown {
    position: absolute;

    top: calc(100% + 9px);
    left: 0;

    min-width: 150px;

    padding: 6px;

    display: flex;
    flex-direction: column;

    border: 1px solid var(--border);
    border-radius: 11px;

    background:
        rgba(8, 10, 12, 0.97);

    backdrop-filter: blur(18px);

    box-shadow:
        0 18px 50px rgba(0, 0, 0, 0.35);
}

.user-dropdown button {
    min-height: 36px;

    padding: 0 10px;

    display: flex;
    align-items: center;

    gap: 8px;

    color: var(--text-secondary);

    border-radius: 7px;

    background: transparent;

    text-align: right;

    font-size: 9px;

    cursor: pointer;

    transition:
        color var(--transition),
        background var(--transition);
}

.user-dropdown button:hover {
    color: var(--text-primary);

    background:
        rgba(255, 255, 255, 0.035);
}

.user-dropdown .logout-item {
    color: #ee7777;
}

.user-dropdown .logout-item:hover {
    color: var(--danger);

    background:
        rgba(255, 92, 92, 0.045);
}

.dropdown-enter-active,
.dropdown-leave-active {
    transition:
        opacity 160ms ease,
        transform 160ms ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-5px);
}

/* Mobile */

.mobile-toggle {
    display: none;

    width: 42px;
    height: 42px;

    align-items: center;
    justify-content: center;

    color: var(--text-primary);

    border: 1px solid var(--border);
    border-radius: 9px;

    background: transparent;

    cursor: pointer;
}

.mobile-menu {
    position: absolute;

    top: calc(100% + 10px);

    inset-inline: 0;

    padding: 10px;

    display: flex;
    flex-direction: column;

    gap: 3px;

    border: 1px solid var(--border);
    border-radius: 14px;

    background:
        rgba(8, 10, 12, 0.97);

    backdrop-filter: blur(20px);

    box-shadow:
        0 20px 60px rgba(0, 0, 0, 0.38);
}

.mobile-link,
.mobile-auth-link {
    min-height: 42px;

    padding: 0 12px;

    display: flex;
    align-items: center;

    color: var(--text-secondary);

    border-radius: 8px;

    background: transparent;

    font-size: 12px;
    font-weight: 600;

    cursor: pointer;

    transition:
        color var(--transition),
        background var(--transition);
}

.mobile-link:hover,
.mobile-auth-link:hover {
    color: var(--text-primary);

    background:
        rgba(255, 255, 255, 0.035);
}

.mobile-auth-link {
    gap: 8px;
}

.mobile-auth-link.logout {
    color: var(--danger);
}

.mobile-start {
    min-height: 43px;

    margin-top: 6px;

    padding: 0 12px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    color: #090909;

    border-radius: 8px;

    background: var(--orange);

    font-size: 11px;
    font-weight: 800;
}

.mobile-menu-enter-active,
.mobile-menu-leave-active {
    transition:
        opacity 180ms ease,
        transform 180ms ease;
}

.mobile-menu-enter-from,
.mobile-menu-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}

@media (max-width: 900px) {
    .desktop-nav,
    .start-button,
    .user-area {
        display: none;
    }

    .mobile-toggle {
        display: flex;
    }

    .navbar {
        min-height: 62px;
        padding-inline: 10px;
    }
}

@media (max-width: 480px) {
    .navbar-wrapper {
        padding-top: 10px;
    }

    .logo {
        font-size: 19px;
    }
}
</style>
