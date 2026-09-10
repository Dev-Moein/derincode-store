<script setup>
import { onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import {
    Users,
    FolderKanban,
    CreditCard,
    Download,
    ClipboardList,
    RefreshCw,
    AlertCircle,
} from 'lucide-vue-next'

import { useAdminStore } from '../../stores/admin'

const adminStore = useAdminStore()

const {
    loading,
    error,
    dashboard,
    stats,
    recentUsers,
    recentPayments,
    recentProjectRequests,
} = storeToRefs(adminStore)

const formatNumber = (value) => {
    return new Intl.NumberFormat('fa-IR').format(
        Number(value || 0)
    )
}

const formatMoney = (value) => {
    if (
        value === null ||
        value === undefined
    ) {
        return '۰'
    }

    return new Intl.NumberFormat('fa-IR').format(
        Number(value)
    )
}

const formatDate = (value) => {
    if (!value) {
        return '-'
    }

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return '-'
    }

    return new Intl.DateTimeFormat(
        'fa-IR',
        {
            dateStyle: 'medium',
        }
    ).format(date)
}

const statusLabel = (status) => {
    const labels = {
        pending: 'در انتظار',
        approved: 'تأیید شده',
        rejected: 'رد شده',
        successful: 'موفق',
        failed: 'ناموفق',
        cancelled: 'لغو شده',
    }

    return labels[status] || status || 'نامشخص'
}

const statusClass = (status) => {
    if (
        status === 'successful' ||
        status === 'approved'
    ) {
        return 'success'
    }

    if (
        status === 'rejected' ||
        status === 'failed'
    ) {
        return 'danger'
    }

    if (status === 'pending') {
        return 'warning'
    }

    return 'neutral'
}

const refreshDashboard = () => {
    adminStore.fetchDashboard()
}

onMounted(() => {
    refreshDashboard()
})
</script>

<template>
    <main
        class="dashboard"
        aria-labelledby="dashboard-title"
    >
        <!-- Header -->
        <header class="page-header">
            <div class="page-heading">
                <span class="kicker">
                    ADMIN / DASHBOARD
                </span>

                <h1 id="dashboard-title">
                    داشبورد مدیریت
                </h1>

                <p>
                    نمای کلی از وضعیت کاربران، پروژه‌ها،
                    فروش و درخواست‌ها.
                </p>
            </div>

            <button
                type="button"
                class="refresh-button"
                :disabled="loading"
                :aria-busy="loading"
                @click="refreshDashboard"
            >
                <RefreshCw
                    :size="15"
                    :class="{ spin: loading }"
                    aria-hidden="true"
                />

                <span>
                    بروزرسانی
                </span>
            </button>
        </header>

        <!-- Error -->
        <div
            v-if="error"
            class="error-banner"
            role="alert"
            aria-live="assertive"
        >
            <AlertCircle
                :size="16"
                aria-hidden="true"
            />

            <span>
                {{ error }}
            </span>
        </div>

        <!-- Loading -->
        <div
            v-if="loading && !dashboard"
            class="loading-state"
            role="status"
            aria-live="polite"
        >
            <RefreshCw
                :size="25"
                class="spin"
                aria-hidden="true"
            />

            <span>
                در حال دریافت اطلاعات...
            </span>
        </div>

        <template v-else>

            <!-- Stats -->
            <section
                class="stats-grid"
                aria-label="آمار کلی"
            >
                <!-- Users -->
                <article class="stat-card">
                    <div class="stat-top">
                        <div
                            class="stat-icon"
                            aria-hidden="true"
                        >
                            <Users :size="18" />
                        </div>

                        <span class="stat-label">
                            کاربران
                        </span>
                    </div>

                    <strong class="stat-value">
                        {{ formatNumber(stats?.users) }}
                    </strong>

                    <span class="stat-meta">
                        کاربران ثبت‌نام‌شده
                    </span>
                </article>

                <!-- Projects -->
                <article class="stat-card">
                    <div class="stat-top">
                        <div
                            class="stat-icon"
                            aria-hidden="true"
                        >
                            <FolderKanban :size="18" />
                        </div>

                        <span class="stat-label">
                            پروژه‌ها
                        </span>
                    </div>

                    <strong class="stat-value">
                        {{
                            formatNumber(
                                stats?.projects
                            )
                        }}
                    </strong>

                    <span class="stat-meta">
                        {{
                            formatNumber(
                                stats?.published_projects
                            )
                        }}
                        منتشر شده
                    </span>
                </article>

                <!-- Sales -->
                <article class="stat-card">
                    <div class="stat-top">
                        <div
                            class="stat-icon"
                            aria-hidden="true"
                        >
                            <CreditCard :size="18" />
                        </div>

                        <span class="stat-label">
                            فروش
                        </span>
                    </div>

                    <strong class="stat-value">
                        {{
                            formatNumber(
                                stats?.sales_count
                            )
                        }}
                    </strong>

                    <span class="stat-meta">
                        تراکنش موفق
                    </span>
                </article>

                <!-- Revenue -->
                <article class="stat-card revenue">
                    <div class="stat-top">
                        <div
                            class="stat-icon"
                            aria-hidden="true"
                        >
                            <CreditCard :size="18" />
                        </div>

                        <span class="stat-label">
                            درآمد
                        </span>
                    </div>

                    <strong class="stat-value">
                        {{
                            formatMoney(
                                stats?.revenue
                            )
                        }}
                    </strong>

                    <span class="stat-meta">
                        مبلغ کل فروش
                    </span>
                </article>

                <!-- Downloads -->
                <article class="stat-card">
                    <div class="stat-top">
                        <div
                            class="stat-icon"
                            aria-hidden="true"
                        >
                            <Download :size="18" />
                        </div>

                        <span class="stat-label">
                            دانلودها
                        </span>
                    </div>

                    <strong class="stat-value">
                        {{
                            formatNumber(
                                stats?.downloads
                            )
                        }}
                    </strong>

                    <span class="stat-meta">
                        کل دانلودها
                    </span>
                </article>

                <!-- Requests -->
                <article class="stat-card">
                    <div class="stat-top">
                        <div
                            class="stat-icon"
                            aria-hidden="true"
                        >
                            <ClipboardList :size="18" />
                        </div>

                        <span class="stat-label">
                            درخواست‌ها
                        </span>
                    </div>

                    <strong class="stat-value">
                        {{
                            formatNumber(
                                stats?.pending_requests
                            )
                        }}
                    </strong>

                    <span class="stat-meta">
                        در انتظار بررسی
                    </span>
                </article>
            </section>

            <!-- Dashboard Grid -->
            <section
                class="dashboard-grid"
                aria-label="اطلاعات اخیر"
            >
                <!-- Recent Users -->
                <article class="dashboard-card">
                    <header class="card-header">
                        <div>
                            <span class="card-kicker">
                                USERS
                            </span>

                            <h2>
                                کاربران اخیر
                            </h2>
                        </div>

                        <RouterLink
                            to="/admin/users"
                            class="view-all"
                        >
                            مشاهده همه
                        </RouterLink>
                    </header>

                    <div
                        v-if="!recentUsers?.length"
                        class="empty"
                    >
                        کاربری وجود ندارد.
                    </div>

                    <div
                        v-else
                        class="list"
                    >
                        <div
                            v-for="
                                user in recentUsers
                            "
                            :key="user.id"
                            class="list-row"
                        >
                            <div
                                class="avatar"
                                aria-hidden="true"
                            >
                                {{
                                    user.name
                                        ?.charAt(0)
                                        ?.toUpperCase()
                                }}
                            </div>

                            <div class="row-main">
                                <strong>
                                    {{ user.name }}
                                </strong>

                                <span dir="ltr">
                                    {{ user.email }}
                                </span>
                            </div>

                            <time
                                :datetime="
                                    user.created_at
                                "
                            >
                                {{
                                    formatDate(
                                        user.created_at
                                    )
                                }}
                            </time>
                        </div>
                    </div>
                </article>

                <!-- Recent Payments -->
                <article class="dashboard-card">
                    <header class="card-header">
                        <div>
                            <span class="card-kicker">
                                PAYMENTS
                            </span>

                            <h2>
                                پرداخت‌های اخیر
                            </h2>
                        </div>

                        <RouterLink
                            to="/admin/payments"
                            class="view-all"
                        >
                            مشاهده همه
                        </RouterLink>
                    </header>

                    <div
                        v-if="!recentPayments?.length"
                        class="empty"
                    >
                        پرداختی وجود ندارد.
                    </div>

                    <div
                        v-else
                        class="list"
                    >
                        <div
                            v-for="
                                payment in recentPayments
                            "
                            :key="payment.id"
                            class="list-row"
                        >
                            <div
                                class="row-icon"
                                aria-hidden="true"
                            >
                                <CreditCard :size="14" />
                            </div>

                            <div class="row-main">
                                <strong>
                                    {{
                                        payment.project?.title ||
                                        'پروژه'
                                    }}
                                </strong>

                                <span dir="ltr">
                                    {{
                                        payment.user?.email ||
                                        'کاربر'
                                    }}
                                </span>
                            </div>

                            <div class="row-side">
                                <strong>
                                    {{
                                        formatMoney(
                                            payment.amount
                                        )
                                    }}
                                </strong>

                                <span
                                    class="status"
                                    :class="
                                        statusClass(
                                            payment.status
                                        )
                                    "
                                >
                                    {{
                                        statusLabel(
                                            payment.status
                                        )
                                    }}
                                </span>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- Requests -->
                <article
                    class="dashboard-card full"
                >
                    <header class="card-header">
                        <div>
                            <span class="card-kicker">
                                PROJECT REQUESTS
                            </span>

                            <h2>
                                درخواست‌های اخیر
                            </h2>
                        </div>

                        <RouterLink
                            to="/admin/requests"
                            class="view-all"
                        >
                            مشاهده همه
                        </RouterLink>
                    </header>

                    <div
                        v-if="
                            !recentProjectRequests?.length
                        "
                        class="empty"
                    >
                        درخواستی وجود ندارد.
                    </div>

                    <div
                        v-else
                        class="request-table"
                    >
                        <div
                            v-for="
                                request in recentProjectRequests
                            "
                            :key="request.id"
                            class="request-row"
                        >
                            <div>
                                <span class="muted">
                                    #{{ request.id }}
                                </span>

                                <strong>
                                    {{ request.title }}
                                </strong>
                            </div>

                            <div>
                                <span class="muted">
                                    کاربر
                                </span>

                                <strong>
                                    {{
                                        request.user?.name ||
                                        '-'
                                    }}
                                </strong>
                            </div>

                            <div>
                                <span
                                    class="status"
                                    :class="
                                        statusClass(
                                            request.status
                                        )
                                    "
                                >
                                    {{
                                        statusLabel(
                                            request.status
                                        )
                                    }}
                                </span>
                            </div>

                            <time
                                :datetime="
                                    request.created_at
                                "
                            >
                                {{
                                    formatDate(
                                        request.created_at
                                    )
                                }}
                            </time>
                        </div>
                    </div>
                </article>
            </section>
        </template>
    </main>
</template>

<style scoped>
/* ================================================================ */
/* Dashboard */
/* ================================================================ */

.dashboard {
    direction: rtl;
    color: var(--text-primary);
}

/* ================================================================ */
/* Header */
/* ================================================================ */

.page-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;

    gap: 20px;

    margin-bottom: 28px;
}

.page-heading {
    min-width: 0;
}

.kicker,
.card-kicker {
    display: inline-block;

    color: var(--orange);

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 10px;
    font-weight: 600;

    line-height: 1.7;

    letter-spacing: 0.08em;
}

.page-header h1 {
    margin-top: 8px;

    color: var(--text-primary);

    font-size: 29px;
    font-weight: 900;

    line-height: 1.4;
}

.page-header p {
    margin-top: 6px;

    color: var(--text-muted);

    font-size: 12px;

    line-height: 1.8;
}

/* ================================================================ */
/* Refresh */
/* ================================================================ */

.refresh-button {
    min-height: 40px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 7px;

    flex-shrink: 0;

    padding: 0 14px;

    color: var(--text-secondary);

    border: 1px solid var(--border);

    border-radius: 8px;

    background: rgba(255, 255, 255, 0.025);

    font-family: inherit;

    font-size: 11px;
    font-weight: 600;

    cursor: pointer;

    transition:
        color var(--transition),
        border-color var(--transition),
        background var(--transition);
}

.refresh-button:hover:not(:disabled) {
    color: var(--orange);

    border-color: var(--border-orange);

    background: var(--orange-soft);
}

.refresh-button:disabled {
    opacity: 0.65;

    cursor: wait;
}

/* ================================================================ */
/* Error */
/* ================================================================ */

.error-banner {
    margin-bottom: 18px;

    padding: 11px 13px;

    display: flex;
    align-items: center;

    gap: 8px;

    color: var(--danger);

    border: 1px solid rgba(230, 80, 80, 0.15);

    border-radius: 9px;

    background: rgba(230, 80, 80, 0.03);

    font-size: 11px;

    line-height: 1.8;
}

/* ================================================================ */
/* Loading */
/* ================================================================ */

.loading-state {
    min-height: 450px;

    display: flex;
    flex-direction: column;

    align-items: center;
    justify-content: center;

    gap: 12px;

    color: var(--text-muted);

    border: 1px solid var(--border);

    border-radius: 14px;

    background: rgba(255, 255, 255, 0.012);

    font-size: 11px;
}

/* ================================================================ */
/* Stats */
/* ================================================================ */

.stats-grid {
    display: grid;

    grid-template-columns:
        repeat(6, minmax(0, 1fr));

    gap: 10px;

    margin-bottom: 14px;
}

.stat-card {
    position: relative;

    min-height: 145px;

    padding: 16px;

    overflow: hidden;

    border: 1px solid var(--border);

    border-radius: 12px;

    background:
        linear-gradient(
            145deg,
            rgba(255, 255, 255, 0.028),
            rgba(255, 255, 255, 0.008)
        );

    box-shadow:
        0 15px 45px rgba(0, 0, 0, 0.16);

    transition:
        border-color var(--transition);
}

.stat-card::after {
    content: "";

    position: absolute;

    width: 110px;
    height: 110px;

    top: -65px;
    left: -45px;

    border-radius: 50%;

    background: rgba(255, 106, 0, 0.04);

    filter: blur(25px);

    pointer-events: none;
}

.stat-card:hover {
    border-color: var(--border-orange);
}

.stat-top {
    display: flex;

    align-items: center;
    justify-content: space-between;
}

.stat-icon {
    width: 33px;
    height: 33px;

    display: flex;

    align-items: center;
    justify-content: center;

    color: var(--orange);

    border: 1px solid var(--border-orange);

    border-radius: 8px;

    background: var(--orange-soft);
}

.stat-label {
    color: var(--text-muted);

    font-size: 10px;
    font-weight: 600;
}

.stat-value {
    display: block;

    margin-top: 19px;

    direction: ltr;

    color: var(--text-primary);

    font-family: var(--font-mono);

    font-size: 22px;
    font-weight: 700;

    line-height: 1.3;
}

.stat-meta {
    display: block;

    margin-top: 5px;

    color: var(--text-muted);

    font-size: 9px;

    line-height: 1.7;
}

/* ================================================================ */
/* Main Grid */
/* ================================================================ */

.dashboard-grid {
    display: grid;

    grid-template-columns:
        minmax(0, 1fr)
        minmax(0, 1fr);

    gap: 10px;
}

.dashboard-card {
    min-width: 0;

    padding: 17px;

    border: 1px solid var(--border);

    border-radius: 12px;

    background:
        linear-gradient(
            145deg,
            rgba(255, 255, 255, 0.025),
            rgba(255, 255, 255, 0.008)
        );
}

.dashboard-card.full {
    grid-column: 1 / -1;
}

/* ================================================================ */
/* Card Header */
/* ================================================================ */

.card-header {
    display: flex;

    align-items: flex-start;
    justify-content: space-between;

    gap: 12px;

    padding-bottom: 13px;

    margin-bottom: 3px;

    border-bottom: 1px solid
        rgba(255, 255, 255, 0.05);
}

.card-kicker {
    font-size: 9px;
}

.card-header h2 {
    margin-top: 4px;

    color: var(--text-primary);

    font-size: 14px;
    font-weight: 800;

    line-height: 1.6;
}

.view-all {
    flex-shrink: 0;

    color: var(--text-muted);

    font-size: 10px;

    transition:
        color var(--transition);
}

.view-all:hover {
    color: var(--orange);
}

/* ================================================================ */
/* List */
/* ================================================================ */

.list {
    display: flex;
    flex-direction: column;
}

.list-row {
    min-height: 57px;

    display: flex;

    align-items: center;

    gap: 9px;

    border-bottom: 1px solid
        rgba(255, 255, 255, 0.04);
}

.list-row:last-child {
    border-bottom: 0;
}

.avatar,
.row-icon {
    width: 31px;
    height: 31px;

    flex-shrink: 0;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 8px;
}

.avatar {
    color: #111;

    background: var(--orange);

    font-size: 10px;
    font-weight: 900;
}

.row-icon {
    color: var(--orange);

    border: 1px solid var(--border-orange);

    background: var(--orange-soft);
}

.row-main {
    min-width: 0;

    display: flex;
    flex-direction: column;

    gap: 3px;

    flex: 1;
}

.row-main strong {
    overflow: hidden;

    color: var(--text-secondary);

    font-size: 10px;
    font-weight: 700;

    text-overflow: ellipsis;
    white-space: nowrap;
}

.row-main span {
    overflow: hidden;

    color: var(--text-muted);

    font-size: 9px;

    text-overflow: ellipsis;
    white-space: nowrap;

    text-align: right;
}

.list-row time {
    flex-shrink: 0;

    color: var(--text-muted);

    font-size: 9px;
}

.row-side {
    display: flex;

    flex-direction: column;

    align-items: flex-end;

    gap: 3px;
}

.row-side strong {
    direction: ltr;

    color: var(--text-secondary);

    font-family: var(--font-mono);

    font-size: 9px;
}

/* ================================================================ */
/* Requests */
/* ================================================================ */

.request-table {
    display: flex;

    flex-direction: column;
}

.request-row {
    min-height: 61px;

    display: grid;

    grid-template-columns:
        2fr 1.2fr auto auto;

    align-items: center;

    gap: 15px;

    border-bottom: 1px solid
        rgba(255, 255, 255, 0.04);
}

.request-row:last-child {
    border-bottom: 0;
}

.request-row > div {
    min-width: 0;

    display: flex;

    flex-direction: column;

    gap: 3px;
}

.request-row strong {
    overflow: hidden;

    color: var(--text-secondary);

    font-size: 10px;
    font-weight: 700;

    text-overflow: ellipsis;
    white-space: nowrap;
}

.muted {
    color: var(--text-muted);

    font-size: 8px;
}

.request-row time {
    color: var(--text-muted);

    font-size: 9px;

    direction: rtl;
}

/* ================================================================ */
/* Status */
/* ================================================================ */

.status {
    width: fit-content;

    display: inline-flex;

    align-items: center;

    padding: 4px 7px;

    border-radius: 5px;

    font-size: 9px;
    font-weight: 600;

    line-height: 1.5;
}

.status.success {
    color: var(--success);

    background: rgba(75, 214, 138, 0.05);
}

.status.warning {
    color: #e4aa50;

    background: rgba(228, 170, 80, 0.05);
}

.status.danger {
    color: var(--danger);

    background: rgba(236, 115, 115, 0.05);
}

.status.neutral {
    color: var(--text-muted);

    background: rgba(255, 255, 255, 0.035);
}

/* ================================================================ */
/* Empty */
/* ================================================================ */

.empty {
    min-height: 150px;

    display: flex;

    align-items: center;
    justify-content: center;

    color: var(--text-muted);

    font-size: 10px;
}

/* ================================================================ */
/* Focus */
/* ================================================================ */

.refresh-button:focus-visible,
.view-all:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 4px;
}

/* ================================================================ */
/* Animation */
/* ================================================================ */

.spin {
    animation:
        spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* ================================================================ */
/* Responsive */
/* ================================================================ */

@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns:
            repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 800px) {
    .page-header {
        align-items: flex-start;

        flex-direction: column;
    }

    .refresh-button {
        width: 100%;
    }

    .dashboard-grid {
        grid-template-columns: 1fr;
    }

    .dashboard-card.full {
        grid-column: auto;
    }

    .request-row {
        grid-template-columns:
            1fr auto;

        gap: 8px;
    }

    .request-row > div:nth-child(2) {
        display: none;
    }
}

@media (max-width: 550px) {
    .stats-grid {
        grid-template-columns:
            repeat(2, minmax(0, 1fr));
    }

    .stat-card {
        min-height: 125px;

        padding: 13px;
    }

    .stat-value {
        font-size: 18px;
    }

    .list-row time {
        display: none;
    }

    .page-header h1 {
        font-size: 25px;
    }

    .page-header p {
        font-size: 11px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .refresh-button,
    .stat-card,
    .view-all {
        transition: none;
    }

    .spin {
        animation: none;
    }
}
</style>
