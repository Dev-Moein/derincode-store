<script setup>
import {
    RouterLink,
    RouterView,
    useRouter,
} from 'vue-router'

import {
    LayoutDashboard,
    FolderKanban,
    ClipboardList,
    Users,
    LogOut,
    LoaderCircle,
} from 'lucide-vue-next'

import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const logout = async () => {
    if (authStore.loading) {
        return
    }

    await authStore.logout()

    router.replace({
        name: 'login',
    })
}
</script>

<template>
    <div class="admin-layout">

        <!-- Sidebar -->
        <aside
            class="sidebar"
            aria-label="ناوبری پنل مدیریت"
        >

            <!-- Brand -->
            <RouterLink
                to="/admin"
                class="brand"
                aria-label="DerinCode - داشبورد مدیریت"
            >
                <span>Derin</span>
                <strong>Code</strong>
            </RouterLink>

            <!-- Admin Profile -->
            <section
                class="admin-profile"
                aria-label="حساب مدیر"
            >
                <div
                    class="avatar"
                    aria-hidden="true"
                >
                    {{
                        authStore.user?.name
                            ?.charAt(0)
                            ?.toUpperCase() || 'A'
                    }}
                </div>

                <div class="profile-info">
                    <strong>
                        {{ authStore.user?.name || 'Admin' }}
                    </strong>

                    <span>
                        Administrator
                    </span>
                </div>
            </section>

            <!-- Navigation -->
            <nav
                class="navigation"
                aria-label="منوی مدیریت"
            >
                <span class="nav-label">
                    مدیریت
                </span>

                <RouterLink
                    to="/admin"
                    class="nav-link"
                    exact-active-class="router-link-exact-active"
                >
                    <LayoutDashboard
                        :size="17"
                        aria-hidden="true"
                    />

                    <span>
                        داشبورد
                    </span>
                </RouterLink>

                <RouterLink
                    to="/admin/projects"
                    class="nav-link"
                >
                    <FolderKanban
                        :size="17"
                        aria-hidden="true"
                    />

                    <span>
                        پروژه‌ها
                    </span>
                </RouterLink>

                <RouterLink
                    to="/admin/requests"
                    class="nav-link"
                >
                    <ClipboardList
                        :size="17"
                        aria-hidden="true"
                    />

                    <span>
                        درخواست‌ها
                    </span>
                </RouterLink>

                <RouterLink
                    to="/admin/users"
                    class="nav-link"
                >
                    <Users
                        :size="17"
                        aria-hidden="true"
                    />

                    <span>
                        کاربران
                    </span>
                </RouterLink>
            </nav>

            <!-- Bottom -->
            <div class="sidebar-bottom">

                <div
                    class="status"
                    role="status"
                    aria-label="وضعیت سیستم: فعال"
                >
                    <i aria-hidden="true"></i>

                    <span>
                        سیستم فعال است
                    </span>
                </div>

                <button
                    type="button"
                    class="logout-button"
                    :disabled="authStore.loading"
                    :aria-busy="authStore.loading"
                    @click="logout"
                >
                    <LoaderCircle
                        v-if="authStore.loading"
                        :size="16"
                        class="spin"
                        aria-hidden="true"
                    />

                    <LogOut
                        v-else
                        :size="16"
                        aria-hidden="true"
                    />

                    <span>
                        {{
                            authStore.loading
                                ? 'در حال خروج...'
                                : 'خروج'
                        }}
                    </span>
                </button>

            </div>

        </aside>

        <!-- Main -->
        <main class="admin-main">

            <!-- Topbar -->
            <header class="topbar">
                <div>
                    <span class="topbar-kicker">
                        DERINCODE / ADMIN
                    </span>

                    <h1>
                        پنل مدیریت
                    </h1>
                </div>

                <div
                    v-if="authStore.user"
                    class="topbar-user"
                >
                    {{ authStore.user.name || 'Admin' }}
                </div>
            </header>

            <!-- Nested Admin Pages -->
            <div class="page-content">
                <RouterView />
            </div>

        </main>

    </div>
</template>

<style scoped>
.admin-layout {
    min-height: 100vh;

    display: flex;

    direction: rtl;

    color: #fff;

    background:
        radial-gradient(
            circle at 80% 0%,
            rgba(255, 106, 0, 0.035),
            transparent 30rem
        ),
        #050505;
}

/* ================================================= */
/* Sidebar */
/* ================================================= */

.sidebar {
    position: fixed;

    top: 0;
    right: 0;
    bottom: 0;

    z-index: 50;

    width: 250px;

    display: flex;
    flex-direction: column;

    padding: 24px 16px;

    border-left:
        1px solid rgba(255, 255, 255, 0.07);

    background:
        linear-gradient(
            180deg,
            rgba(14, 14, 14, 0.98),
            rgba(6, 6, 6, 0.99)
        );

    box-shadow:
        -20px 0 60px rgba(0, 0, 0, 0.18);
}

/* Brand */

.brand {
    direction: ltr;

    width: fit-content;

    display: block;

    margin-bottom: 25px;

    color: #fff;

    font-family: var(--font-mono);

    font-size: 24px;

    font-weight: 900;

    letter-spacing: -0.06em;

    line-height: 1;

    text-decoration: none;
}

.brand span {
    color: #fff;
}

.brand strong {
    color: #ff6a00;
}

/* Profile */

.admin-profile {
    display: flex;

    align-items: center;

    gap: 10px;

    padding: 12px;

    border:
        1px solid rgba(255, 255, 255, 0.06);

    border-radius: 11px;

    background:
        rgba(255, 255, 255, 0.02);

    box-shadow:
        inset 0 1px 0
        rgba(255, 255, 255, 0.02);
}

.avatar {
    width: 38px;
    height: 38px;

    flex-shrink: 0;

    display: flex;

    align-items: center;
    justify-content: center;

    color: #090909;

    border-radius: 10px;

    background: #ff6a00;

    font-size: 12px;

    font-weight: 900;
}

.profile-info {
    min-width: 0;

    display: flex;
    flex-direction: column;

    gap: 4px;
}

.profile-info strong {
    overflow: hidden;

    color: #e8e8e8;

    font-size: 11px;

    font-weight: 700;

    line-height: 1.4;

    text-overflow: ellipsis;

    white-space: nowrap;
}

.profile-info span {
    color: #5e5e5e;

    font-family: var(--font-mono);

    font-size: 8px;

    line-height: 1.4;
}

/* Navigation */

.navigation {
    display: flex;
    flex-direction: column;

    gap: 5px;

    margin-top: 28px;
}

.nav-label {
    margin-bottom: 5px;

    padding-inline: 10px;

    color: #474747;

    font-family: var(--font-mono);

    font-size: 8px;

    letter-spacing: 0.08em;
}

.nav-link {
    min-height: 43px;

    display: flex;

    align-items: center;

    gap: 10px;

    padding: 0 11px;

    color: #777;

    border:
        1px solid transparent;

    border-radius: 8px;

    text-decoration: none;

    font-size: 11px;

    font-weight: 600;

    line-height: 1.5;

    transition:
        color 0.2s ease,
        background 0.2s ease,
        border-color 0.2s ease,
        transform 0.2s ease;
}

.nav-link:hover {
    color: #dedede;

    background:
        rgba(255, 255, 255, 0.025);

    transform: translateX(-2px);
}

.nav-link svg {
    flex-shrink: 0;

    color: #555;

    transition:
        color 0.2s ease;
}

.nav-link.router-link-active {
    color: #fff;

    border-color:
        rgba(255, 106, 0, 0.16);

    background:
        linear-gradient(
            90deg,
            rgba(255, 106, 0, 0.12),
            rgba(255, 106, 0, 0.035)
        );

    box-shadow:
        inset -2px 0 0 #ff6a00;
}

.nav-link.router-link-active svg {
    color: #ff6a00;
}

/*
 * Only the exact /admin route gets the dashboard active state.
 */
.nav-link.router-link-exact-active {
    color: #fff;

    border-color:
        rgba(255, 106, 0, 0.16);

    background:
        linear-gradient(
            90deg,
            rgba(255, 106, 0, 0.12),
            rgba(255, 106, 0, 0.035)
        );

    box-shadow:
        inset -2px 0 0 #ff6a00;
}

.nav-link.router-link-exact-active svg {
    color: #ff6a00;
}

/* Bottom */

.sidebar-bottom {
    margin-top: auto;

    padding-top: 18px;
}

.status {
    min-height: 34px;

    display: flex;

    align-items: center;

    gap: 8px;

    padding: 0 10px;

    color: #555;

    font-size: 9px;

    line-height: 1.5;
}

.status i {
    width: 6px;
    height: 6px;

    flex-shrink: 0;

    border-radius: 50%;

    background: #36d67a;

    box-shadow:
        0 0 9px
        rgba(54, 214, 122, 0.45);
}

.logout-button {
    width: 100%;

    min-height: 40px;

    display: flex;

    align-items: center;
    justify-content: center;

    gap: 8px;

    margin-top: 7px;

    color: #8b6464;

    border:
        1px solid rgba(255, 92, 92, 0.1);

    border-radius: 8px;

    background:
        rgba(255, 92, 92, 0.015);

    font-family: inherit;

    font-size: 10px;

    font-weight: 600;

    cursor: pointer;

    transition:
        color 0.2s ease,
        border-color 0.2s ease,
        background 0.2s ease;
}

.logout-button:hover:not(:disabled) {
    color: #ed7777;

    border-color:
        rgba(255, 92, 92, 0.22);

    background:
        rgba(255, 92, 92, 0.04);
}

.logout-button:disabled {
    opacity: 0.5;

    cursor: not-allowed;
}

/* ================================================= */
/* Main */
/* ================================================= */

.admin-main {
    width: calc(100% - 250px);

    min-height: 100vh;

    margin-right: 250px;
}

/* Topbar */

.topbar {
    position: sticky;

    top: 0;

    z-index: 40;

    min-height: 86px;

    padding: 20px 32px;

    display: flex;

    align-items: center;
    justify-content: space-between;

    gap: 20px;

    border-bottom:
        1px solid rgba(255, 255, 255, 0.06);

    background:
        rgba(5, 5, 5, 0.78);

    backdrop-filter: blur(15px);

    -webkit-backdrop-filter: blur(15px);
}

.topbar-kicker {
    color: #ff6a00;

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 9px;

    letter-spacing: 0.08em;
}

.topbar h1 {
    margin-top: 5px;

    color: #f3f3f3;

    font-size: 18px;

    font-weight: 800;

    line-height: 1.4;
}

.topbar-user {
    max-width: 180px;

    overflow: hidden;

    padding: 7px 10px;

    color: #777;

    border:
        1px solid rgba(255, 255, 255, 0.07);

    border-radius: 7px;

    background:
        rgba(255, 255, 255, 0.02);

    font-size: 9px;

    text-overflow: ellipsis;

    white-space: nowrap;
}

/* Content */

.page-content {
    padding: 30px 32px 50px;
}

/* Accessibility */

.nav-link:focus-visible,
.brand:focus-visible,
.logout-button:focus-visible {
    outline: 2px solid #ff6a00;

    outline-offset: 3px;
}

/* Animation */

.spin {
    animation:
        spin 0.9s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* ================================================= */
/* Responsive */
/* ================================================= */

@media (max-width: 900px) {
    .sidebar {
        width: 215px;
    }

    .admin-main {
        width: calc(100% - 215px);

        margin-right: 215px;
    }

    .topbar {
        padding-inline: 22px;
    }

    .page-content {
        padding-inline: 22px;
    }
}

@media (max-width: 700px) {
    .admin-layout {
        display: block;
    }

    .sidebar {
        position: static;

        width: 100%;
        min-height: auto;

        border-left: 0;

        border-bottom:
            1px solid rgba(255, 255, 255, 0.07);
    }

    .admin-main {
        width: 100%;

        margin-right: 0;
    }

    .navigation {
        display: grid;

        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

    .nav-label {
        grid-column: 1 / -1;
    }

    .sidebar-bottom {
        margin-top: 15px;
    }

    .topbar {
        position: static;

        min-height: 75px;

        padding: 17px;
    }

    .page-content {
        padding: 20px 17px 40px;
    }
}

@media (max-width: 430px) {
    .navigation {
        grid-template-columns: 1fr;
    }

    .topbar-user {
        display: none;
    }

    .topbar h1 {
        font-size: 16px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .spin {
        animation: none;
    }

    .nav-link,
    .nav-link svg,
    .logout-button {
        transition: none;
    }
}
</style>
