<script setup>
import { onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import {
    Clock3,
    CheckCircle2,
    XCircle,
    MessageSquareText,
    RefreshCw,
    FileText,
} from 'lucide-vue-next'

import { useProjectRequestsStore } from '../../stores/projectRequests'

const requestsStore = useProjectRequestsStore()

const {
    requests,
    loading,
    error,
} = storeToRefs(requestsStore)

const statusMap = {
    pending: {
        label: 'در انتظار بررسی',
        class: 'pending',
        icon: Clock3,
    },

    approved: {
        label: 'تأیید شده',
        class: 'approved',
        icon: CheckCircle2,
    },

    rejected: {
        label: 'رد شده',
        class: 'rejected',
        icon: XCircle,
    },
}

const getStatus = (status) => {
    return (
        statusMap[status] || {
            label: status || 'نامشخص',
            class: 'unknown',
            icon: Clock3,
        }
    )
}

const formatDate = (date) => {
    if (!date) {
        return '-'
    }

    const parsedDate = new Date(date)

    if (Number.isNaN(parsedDate.getTime())) {
        return '-'
    }

    return new Intl.DateTimeFormat(
        'fa-IR',
        {
            dateStyle: 'medium',
        }
    ).format(parsedDate)
}

const formatBudget = (budget, currency) => {
    if (
        budget === null ||
        budget === undefined ||
        budget === ''
    ) {
        return 'تعیین نشده'
    }

    try {
        const formatted = new Intl.NumberFormat(
            'en-US'
        ).format(Number(budget))

        return `${formatted} ${currency || ''}`.trim()
    } catch {
        return `${budget} ${currency || ''}`.trim()
    }
}

onMounted(() => {
    requestsStore.fetchMyRequests()
})
</script>

<template>
    <section class="requests-section">

        <div class="section-header">
            <div>
                <span class="section-kicker">
                    PROJECT REQUESTS
                </span>

                <h2>
                    درخواست‌های من
                </h2>

                <p>
                    وضعیت درخواست‌های ثبت‌شده خود را
                    مشاهده و پیگیری کنید.
                </p>
            </div>

            <div class="request-count">
                {{ requests.length }}
                <span>درخواست</span>
            </div>
        </div>

        <!-- Loading -->
        <div
            v-if="loading"
            class="requests-loading"
        >
            <RefreshCw
                :size="20"
                class="loader"
            />

            <span>
                در حال دریافت درخواست‌ها...
            </span>
        </div>

        <!-- Error -->
        <div
            v-else-if="error"
            class="requests-state"
        >
            <div class="state-icon error">
                <XCircle :size="22" />
            </div>

            <h3>
                دریافت درخواست‌ها انجام نشد
            </h3>

            <p>
                {{ error }}
            </p>

            <button
                type="button"
                class="retry-button"
                @click="requestsStore.fetchMyRequests()"
            >
                تلاش مجدد
            </button>
        </div>

        <!-- Empty -->
        <div
            v-else-if="!requests.length"
            class="requests-state"
        >
            <div class="state-icon">
                <FileText :size="22" />
            </div>

            <h3>
                هنوز درخواستی ثبت نکرده‌اید
            </h3>

            <p>
                بعد از ثبت درخواست پروژه، وضعیت آن در اینجا
                نمایش داده می‌شود.
            </p>
        </div>

        <!-- Requests -->
        <div
            v-else
            class="requests-list"
        >
            <article
                v-for="request in requests"
                :key="request.id"
                class="request-card"
            >
                <div class="request-top">

                    <div class="request-title-area">
                        <span class="request-id">
                            #{{ request.id }}
                        </span>

                        <h3>
                            {{ request.title }}
                        </h3>
                    </div>

                    <div
                        class="request-status"
                        :class="getStatus(request.status).class"
                    >
                        <component
                            :is="
                                getStatus(
                                    request.status
                                ).icon
                            "
                            :size="13"
                        />

                        {{
                            getStatus(
                                request.status
                            ).label
                        }}
                    </div>
                </div>

                <p class="request-description">
                    {{ request.description }}
                </p>

                <div class="request-meta">

                    <div class="meta-item">
                        <span>
                            بودجه
                        </span>

                        <strong>
                            {{
                                formatBudget(
                                    request.budget,
                                    request.currency
                                )
                            }}
                        </strong>
                    </div>

                    <div class="meta-item">
                        <span>
                            تاریخ ثبت
                        </span>

                        <strong>
                            {{ formatDate(request.created_at) }}
                        </strong>
                    </div>

                    <div
                        v-if="request.reviewed_at"
                        class="meta-item"
                    >
                        <span>
                            تاریخ بررسی
                        </span>

                        <strong>
                            {{ formatDate(request.reviewed_at) }}
                        </strong>
                    </div>
                </div>

                <div
                    v-if="request.admin_note"
                    class="admin-note"
                >
                    <MessageSquareText :size="16" />

                    <div>
                        <span>
                            یادداشت تیم
                        </span>

                        <p>
                            {{ request.admin_note }}
                        </p>
                    </div>
                </div>
            </article>
        </div>

    </section>
</template>

<style scoped>
.requests-section {
    margin-top: 15px;
}

.section-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;

    gap: 20px;

    margin-bottom: 18px;
}

.section-kicker {
    color: var(--orange);

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 8px;
}

.section-header h2 {
    margin-top: 6px;

    color: var(--text-primary);

    font-size: 18px;
    font-weight: 900;
}

.section-header p {
    margin-top: 4px;

    color: var(--text-muted);

    font-size: 9px;
}

.request-count {
    padding: 7px 10px;

    color: var(--orange);

    border: 1px solid rgba(255, 107, 0, 0.17);
    border-radius: 7px;

    background: rgba(255, 107, 0, 0.035);

    font-size: 10px;
    font-weight: 800;
}

.request-count span {
    margin-right: 3px;

    color: var(--text-muted);

    font-size: 8px;
}

/* Loading / State */

.requests-loading,
.requests-state {
    min-height: 180px;

    display: flex;
    flex-direction: column;

    align-items: center;
    justify-content: center;

    text-align: center;

    border: 1px solid var(--border);
    border-radius: 12px;

    background: rgba(255, 255, 255, 0.012);
}

.requests-loading {
    flex-direction: row;
    gap: 9px;

    color: var(--text-muted);

    font-size: 9px;
}

.loader {
    color: var(--orange);

    animation:
        spin 0.8s linear infinite;
}

.state-icon {
    width: 48px;
    height: 48px;

    display: flex;
    align-items: center;
    justify-content: center;

    color: var(--orange);

    border: 1px solid var(--border-orange);
    border-radius: 12px;

    background: var(--orange-soft);
}

.state-icon.error {
    color: var(--danger);

    border-color: rgba(255, 92, 92, 0.15);

    background: rgba(255, 92, 92, 0.025);
}

.requests-state h3 {
    margin-top: 13px;

    color: var(--text-primary);

    font-size: 11px;
}

.requests-state p {
    max-width: 390px;

    margin-top: 5px;

    color: var(--text-muted);

    font-size: 8px;
    line-height: 1.8;
}

.retry-button {
    min-height: 34px;

    margin-top: 13px;
    padding-inline: 13px;

    color: var(--text-primary);

    border: 1px solid var(--border);
    border-radius: 7px;

    background: transparent;

    font-size: 8px;
    font-weight: 700;

    cursor: pointer;

    transition:
        border-color var(--transition),
        background var(--transition);
}

.retry-button:hover {
    border-color: var(--border-orange);

    background: var(--orange-soft);
}

/* Request list */

.requests-list {
    display: flex;
    flex-direction: column;

    gap: 9px;
}

.request-card {
    padding: 17px;

    border: 1px solid var(--border);
    border-radius: 11px;

    background:
        linear-gradient(
            145deg,
            rgba(255, 255, 255, 0.025),
            rgba(255, 255, 255, 0.008)
        );

    transition:
        border-color var(--transition),
        transform var(--transition),
        background var(--transition);
}

.request-card:hover {
    border-color: rgba(255, 107, 0, 0.2);

    background:
        linear-gradient(
            145deg,
            rgba(255, 107, 0, 0.025),
            rgba(255, 255, 255, 0.008)
        );

    transform: translateY(-2px);
}

.request-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;

    gap: 15px;
}

.request-id {
    color: #555b61;

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 7px;
}

.request-title-area h3 {
    margin-top: 3px;

    color: var(--text-primary);

    font-size: 13px;
    font-weight: 800;
}

.request-status {
    flex-shrink: 0;

    min-height: 25px;

    display: inline-flex;
    align-items: center;

    gap: 6px;

    padding-inline: 8px;

    border-radius: 6px;

    font-size: 8px;
    font-weight: 700;
}

.request-status.pending {
    color: #e6a74a;

    border: 1px solid rgba(230, 167, 74, 0.14);

    background: rgba(230, 167, 74, 0.025);
}

.request-status.approved {
    color: var(--success);

    border: 1px solid rgba(55, 214, 122, 0.14);

    background: rgba(55, 214, 122, 0.025);
}

.request-status.rejected {
    color: var(--danger);

    border: 1px solid rgba(255, 92, 92, 0.14);

    background: rgba(255, 92, 92, 0.025);
}

.request-status.unknown {
    color: var(--text-muted);

    border: 1px solid var(--border);

    background: transparent;
}

.request-description {
    display: -webkit-box;

    margin-top: 9px;

    overflow: hidden;

    color: var(--text-muted);

    font-size: 9px;
    line-height: 1.9;

    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}

.request-meta {
    display: flex;
    flex-wrap: wrap;

    gap: 9px;

    margin-top: 14px;
}

.meta-item {
    min-width: 105px;

    padding: 7px 9px;

    display: flex;
    flex-direction: column;

    gap: 2px;

    border: 1px solid rgba(255, 255, 255, 0.055);
    border-radius: 7px;

    background: rgba(255, 255, 255, 0.012);
}

.meta-item span {
    color: #5f656a;

    font-size: 7px;
}

.meta-item strong {
    color: var(--text-secondary);

    font-size: 8px;
    font-weight: 700;
}

.admin-note {
    margin-top: 12px;

    padding: 10px;

    display: flex;
    align-items: flex-start;

    gap: 8px;

    border: 1px solid rgba(255, 107, 0, 0.11);
    border-radius: 7px;

    background: rgba(255, 107, 0, 0.02);
}

.admin-note svg {
    flex-shrink: 0;

    margin-top: 2px;

    color: var(--orange);
}

.admin-note span {
    color: var(--orange);

    font-size: 7px;
    font-weight: 700;
}

.admin-note p {
    margin-top: 3px;

    color: var(--text-muted);

    font-size: 8px;
    line-height: 1.8;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@media (max-width: 600px) {
    .section-header {
        align-items: flex-start;

        flex-direction: column;
    }

    .request-count {
        align-self: flex-start;
    }

    .request-top {
        flex-direction: column;
    }

    .request-status {
        align-self: flex-start;
    }

    .request-meta {
        display: grid;

        grid-template-columns: 1fr 1fr;
    }

    .meta-item {
        min-width: 0;
    }
}

@media (max-width: 380px) {
    .request-meta {
        grid-template-columns: 1fr;
    }
}
</style>
