<script setup>
import {
    onMounted,
    onUnmounted,
    ref,
} from 'vue'

import {
    ClipboardList,
    Eye,
    LoaderCircle,
    RefreshCw,
    Search,
    X,
} from 'lucide-vue-next'

import {
    useAdminProjectRequestsStore,
} from '../../stores/adminProjectRequests'

import ProjectRequestDetails from '../../components/admin/ProjectRequestDetails.vue'

const store =
    useAdminProjectRequestsStore()

const searchInput = ref('')

const selectedRequest = ref(null)
const detailsOpen = ref(false)

let searchTimer = null

const statuses = [
    {
        value: '',
        label: 'همه وضعیت‌ها',
    },
    {
        value: 'pending',
        label: 'در انتظار',
    },
    {
        value: 'reviewing',
        label: 'در حال بررسی',
    },
    {
        value: 'accepted',
        label: 'تأیید شده',
    },
    {
        value: 'rejected',
        label: 'رد شده',
    },
    {
        value: 'completed',
        label: 'تکمیل شده',
    },
    {
        value: 'cancelled',
        label: 'لغو شده',
    },
]

const statusLabel = (status) => {
    return (
        statuses.find(
            (item) =>
                item.value === status
        )?.label ||
        status ||
        'نامشخص'
    )
}

const statusClass = (status) => {
    const classes = {
        pending: 'pending',
        reviewing: 'reviewing',
        accepted: 'accepted',
        rejected: 'rejected',
        completed: 'completed',
        cancelled: 'cancelled',
    }

    return classes[status] || 'neutral'
}

const formatBudget = (
    budget,
    currency
) => {
    if (
        budget === null ||
        budget === undefined ||
        budget === ''
    ) {
        return 'تعیین نشده'
    }

    const numericBudget = Number(budget)

    if (Number.isNaN(numericBudget)) {
        return 'نامشخص'
    }

    return `${new Intl.NumberFormat(
        'en-US'
    ).format(numericBudget)} ${
        currency || ''
    }`.trim()
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

const fetchRequests = () => {
    store.fetchRequests()
}

const handleSearch = () => {
    store.filters.search =
        searchInput.value.trim()

    store.filters.page = 1

    fetchRequests()
}

const handleSearchInput = () => {
    clearTimeout(searchTimer)

    searchTimer = setTimeout(() => {
        handleSearch()
    }, 450)
}

const handleStatusChange = () => {
    store.filters.page = 1

    fetchRequests()
}

const clearSearch = () => {
    searchInput.value = ''

    handleSearch()
}

const resetFilters = () => {
    searchInput.value = ''

    store.filters.search = ''
    store.filters.status = ''
    store.filters.page = 1

    fetchRequests()
}

const openDetails = async (request) => {
    selectedRequest.value = request
    detailsOpen.value = true

    const result =
        await store.fetchRequest(
            request.id
        )

    if (
        result.success &&
        result.request
    ) {
        selectedRequest.value =
            result.request
    }
}

const closeDetails = () => {
    detailsOpen.value = false
    selectedRequest.value = null
}

const handleSaved = (request) => {
    selectedRequest.value = request
}

const goToPage = (page) => {
    if (
        page < 1 ||
        page > store.pagination.lastPage ||
        page === store.pagination.currentPage
    ) {
        return
    }

    store.filters.page = page

    fetchRequests()
}

onMounted(fetchRequests)

onUnmounted(() => {
    clearTimeout(searchTimer)
})
</script>

<template>
    <section
        class="requests-page"
        aria-labelledby="admin-project-requests-title"
    >
        <!-- Header -->
        <header class="page-header">
            <div>
                <span class="kicker">
                    ADMIN / PROJECT REQUESTS
                </span>

                <h1 id="admin-project-requests-title">
                    درخواست‌های پروژه
                </h1>

                <p>
                    درخواست‌های کاربران را بررسی و
                    مدیریت کنید.
                </p>
            </div>

            <button
                type="button"
                class="refresh-button"
                :disabled="store.loading"
                :aria-busy="store.loading"
                @click="fetchRequests"
            >
                <RefreshCw
                    :size="16"
                    :class="{
                        spin: store.loading,
                    }"
                    aria-hidden="true"
                />

                <span>
                    بروزرسانی
                </span>
            </button>
        </header>

        <!-- Filters -->
        <section
            class="filters"
            aria-label="فیلتر درخواست‌های پروژه"
        >
            <div class="search-box">
                <Search
                    :size="16"
                    aria-hidden="true"
                />

                <input
                    v-model="searchInput"
                    type="search"
                    aria-label="جستجو در درخواست‌های پروژه"
                    placeholder="جستجو در عنوان، توضیحات، نام یا ایمیل..."
                    @input="handleSearchInput"
                />

                <button
                    v-if="searchInput"
                    type="button"
                    class="clear-search"
                    aria-label="پاک کردن جستجو"
                    @click="clearSearch"
                >
                    <X
                        :size="14"
                        aria-hidden="true"
                    />
                </button>
            </div>

            <select
                v-model="store.filters.status"
                aria-label="فیلتر وضعیت درخواست"
                @change="handleStatusChange"
            >
                <option
                    v-for="item in statuses"
                    :key="item.value"
                    :value="item.value"
                >
                    {{ item.label }}
                </option>
            </select>

            <button
                type="button"
                class="reset-button"
                aria-label="بازنشانی فیلترها"
                title="بازنشانی فیلترها"
                @click="resetFilters"
            >
                <RefreshCw
                    :size="15"
                    aria-hidden="true"
                />
            </button>
        </section>

        <!-- Error -->
        <div
            v-if="store.error"
            class="error-banner"
            role="alert"
            aria-live="assertive"
        >
            {{ store.error }}
        </div>

        <!-- Loading -->
        <div
            v-if="
                store.loading &&
                !store.requests.length
            "
            class="state"
            role="status"
            aria-live="polite"
        >
            <LoaderCircle
                :size="27"
                class="spin"
                aria-hidden="true"
            />

            <span>
                در حال دریافت درخواست‌ها...
            </span>
        </div>

        <!-- Empty -->
        <div
            v-else-if="store.isEmpty"
            class="state"
        >
            <ClipboardList
                :size="31"
                aria-hidden="true"
            />

            <h2>
                درخواستی پیدا نشد
            </h2>

            <p>
                درخواست دیگری با این مشخصات وجود ندارد.
            </p>
        </div>

        <!-- Requests -->
        <section
            v-else
            class="requests-container"
            aria-label="لیست درخواست‌های پروژه"
        >
            <div
                class="table-header"
                aria-hidden="true"
            >
                <span>
                    درخواست
                </span>

                <span>
                    کاربر
                </span>

                <span>
                    بودجه
                </span>

                <span>
                    وضعیت
                </span>

                <span>
                    تاریخ
                </span>

                <span>
                    عملیات
                </span>
            </div>

            <article
                v-for="request in store.requests"
                :key="request.id"
                class="request-row"
            >
                <!-- Request -->
                <div class="request-main">
                    <span class="request-id">
                        #{{ request.id }}
                    </span>

                    <strong>
                        {{ request.title }}
                    </strong>

                    <span class="description">
                        {{ request.description }}
                    </span>
                </div>

                <!-- User -->
                <div class="user-cell">
                    <span class="user-name">
                        {{ request.user?.name || '-' }}
                    </span>

                    <span class="user-email">
                        {{ request.user?.email || '-' }}
                    </span>
                </div>

                <!-- Budget -->
                <div class="budget">
                    {{
                        formatBudget(
                            request.budget,
                            request.currency
                        )
                    }}
                </div>

                <!-- Status -->
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

                <!-- Date -->
                <time
                    class="date"
                    :datetime="
                        request.created_at || undefined
                    "
                >
                    {{
                        formatDate(
                            request.created_at
                        )
                    }}
                </time>

                <!-- Action -->
                <div class="action">
                    <button
                        type="button"
                        title="مشاهده و مدیریت درخواست"
                        :aria-label="
                            `مشاهده و مدیریت درخواست ${request.title}`
                        "
                        @click="openDetails(request)"
                    >
                        <Eye
                            :size="16"
                            aria-hidden="true"
                        />

                        <span>
                            مدیریت
                        </span>
                    </button>
                </div>
            </article>
        </section>

        <!-- Pagination -->
        <nav
            v-if="
                store.pagination.lastPage > 1
            "
            class="pagination"
            aria-label="صفحه‌بندی درخواست‌های پروژه"
        >
            <button
                type="button"
                :disabled="
                    store.pagination.currentPage <= 1
                "
                @click="
                    goToPage(
                        store.pagination.currentPage - 1
                    )
                "
            >
                قبلی
            </button>

            <button
                v-for="
                    page in store.pagination.lastPage
                "
                :key="page"
                type="button"
                :class="{
                    active:
                        page ===
                        store.pagination.currentPage,
                }"
                :aria-current="
                    page ===
                    store.pagination.currentPage
                        ? 'page'
                        : undefined
                "
                :aria-label="`صفحه ${page}`"
                @click="goToPage(page)"
            >
                {{ page }}
            </button>

            <button
                type="button"
                :disabled="
                    store.pagination.currentPage >=
                    store.pagination.lastPage
                "
                @click="
                    goToPage(
                        store.pagination.currentPage + 1
                    )
                "
            >
                بعدی
            </button>
        </nav>

        <!-- Details Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div
                    v-if="
                        detailsOpen &&
                        selectedRequest
                    "
                    class="modal-overlay"
                    role="presentation"
                    @click.self="closeDetails"
                >
                    <div
                        class="modal"
                        role="dialog"
                        aria-modal="true"
                        aria-label="مدیریت درخواست پروژه"
                    >
                        <ProjectRequestDetails
                            :request="selectedRequest"
                            @saved="handleSaved"
                            @close="closeDetails"
                        />
                    </div>
                </div>
            </Transition>
        </Teleport>
    </section>
</template>

<style scoped>
.requests-page {
    direction: rtl;
}

/* Header */

.page-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 23px;
}

.kicker {
    color: var(--orange);
    direction: ltr;
    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.08em;
    line-height: 1.7;
}

.page-header h1 {
    margin-top: 7px;
    color: var(--text-primary);
    font-size: 28px;
    font-weight: 900;
    line-height: 1.45;
}

.page-header p {
    margin-top: 5px;
    color: var(--text-muted);
    font-size: 12px;
    line-height: 1.9;
}

.refresh-button {
    min-height: 41px;
    padding-inline: 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    color: #999;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: transparent;
    font-family: inherit;
    font-size: 11px;
    font-weight: 600;
    line-height: 1.6;
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
    opacity: 0.55;
    cursor: wait;
}

/* Filters */

.filters {
    padding: 9px;
    display: grid;
    grid-template-columns:
        minmax(220px, 1fr)
        170px
        40px;
    gap: 7px;
    margin-bottom: 11px;
    border: 1px solid var(--border);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.015);
}

.search-box {
    min-height: 40px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding-inline: 10px;
    border: 1px solid var(--border);
    border-radius: 7px;
    background: rgba(0, 0, 0, 0.15);
}

.search-box > svg {
    flex-shrink: 0;
    color: #555;
}

.search-box input {
    width: 100%;
    min-width: 0;
    border: 0;
    outline: 0;
    color: var(--text-primary);
    background: transparent;
    font-family: inherit;
    font-size: 11px;
    line-height: 1.7;
}

.search-box input::placeholder {
    color: #555;
}

.search-box button {
    width: 28px;
    height: 28px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #666;
    border: 0;
    border-radius: 5px;
    background: transparent;
    cursor: pointer;
}

.search-box button:hover {
    color: var(--orange);
}

.filters select {
    min-height: 40px;
    padding-inline: 9px;
    color: #aaa;
    border: 1px solid var(--border);
    border-radius: 7px;
    outline: 0;
    background: #0e0e0e;
    font-family: inherit;
    font-size: 10px;
    line-height: 1.6;
}

.filters select:focus {
    border-color: var(--border-orange);
}

.reset-button {
    width: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #777;
    border: 1px solid var(--border);
    border-radius: 7px;
    background: transparent;
    cursor: pointer;
    transition:
        color var(--transition),
        border-color var(--transition),
        background var(--transition);
}

.reset-button:hover {
    color: var(--orange);
    border-color: var(--border-orange);
    background: var(--orange-soft);
}

/* Error */

.error-banner {
    margin-bottom: 11px;
    padding: 11px 12px;
    color: var(--danger);
    border: 1px solid rgba(233, 122, 122, 0.12);
    border-radius: 8px;
    background: rgba(233, 122, 122, 0.025);
    font-size: 11px;
    line-height: 1.8;
}

/* States */

.state {
    min-height: 380px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #555;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.01);
    font-size: 11px;
    line-height: 1.7;
}

.state h2 {
    color: #aaa;
    font-size: 15px;
    font-weight: 800;
}

.state p {
    color: var(--text-muted);
    font-size: 11px;
}

/* Table */

.requests-container {
    overflow: hidden;
    border: 1px solid var(--border);
    border-radius: 11px;
    background: rgba(255, 255, 255, 0.01);
}

.table-header,
.request-row {
    display: grid;
    grid-template-columns:
        minmax(260px, 2fr)
        minmax(150px, 1fr)
        150px
        110px
        105px
        95px;
    align-items: center;
    gap: 12px;
    padding-inline: 14px;
}

.table-header {
    min-height: 42px;
    color: #555;
    border-bottom: 1px solid rgba(255, 255, 255, 0.045);
    font-size: 10px;
    font-weight: 600;
}

.request-row {
    min-height: 84px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.04);
    transition: background var(--transition);
}

.request-row:last-child {
    border-bottom: 0;
}

.request-row:hover {
    background: rgba(255, 106, 0, 0.015);
}

/* Request */

.request-main {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.request-id {
    color: #555;
    direction: ltr;
    font-family: var(--font-mono);
    font-size: 8px;
    line-height: 1.5;
}

.request-main strong {
    overflow: hidden;
    color: #d7d7d7;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.7;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.description {
    overflow: hidden;
    color: #555;
    font-size: 9px;
    line-height: 1.6;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* User */

.user-cell {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.user-name {
    overflow: hidden;
    color: #aaa;
    font-size: 10px;
    line-height: 1.6;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.user-email {
    overflow: hidden;
    color: #555;
    direction: ltr;
    font-size: 8px;
    line-height: 1.5;
    text-align: right;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Budget */

.budget {
    direction: ltr;
    color: #aaa;
    font-family: var(--font-mono);
    font-size: 9px;
    line-height: 1.6;
    text-align: right;
}

/* Status */

.status {
    width: fit-content;
    padding: 5px 8px;
    border-radius: 5px;
    font-size: 9px;
    font-weight: 700;
    line-height: 1.5;
}

.status.pending {
    color: #dca24d;
    background: rgba(220, 162, 77, 0.055);
}

.status.reviewing {
    color: #65a9e8;
    background: rgba(101, 169, 232, 0.05);
}

.status.accepted {
    color: #47d68a;
    background: rgba(71, 214, 138, 0.05);
}

.status.rejected {
    color: #e87373;
    background: rgba(232, 115, 115, 0.05);
}

.status.completed {
    color: #a477e8;
    background: rgba(164, 119, 232, 0.05);
}

.status.cancelled,
.status.neutral {
    color: #777;
    background: rgba(255, 255, 255, 0.03);
}

/* Date */

.date {
    color: #666;
    font-size: 9px;
    line-height: 1.6;
}

/* Action */

.action button {
    min-height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding-inline: 10px;
    color: #888;
    border: 1px solid var(--border);
    border-radius: 6px;
    background: transparent;
    font-family: inherit;
    font-size: 9px;
    font-weight: 600;
    line-height: 1.5;
    cursor: pointer;
    transition:
        color var(--transition),
        border-color var(--transition),
        background var(--transition);
}

.action button:hover {
    color: var(--orange);
    border-color: var(--border-orange);
    background: var(--orange-soft);
}

/* Pagination */

.pagination {
    margin-top: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

.pagination button {
    min-width: 32px;
    height: 32px;
    padding-inline: 8px;
    color: #777;
    border: 1px solid var(--border);
    border-radius: 6px;
    background: transparent;
    font-family: inherit;
    font-size: 9px;
    line-height: 1.5;
    cursor: pointer;
    transition:
        color var(--transition),
        border-color var(--transition),
        background var(--transition);
}

.pagination button:hover:not(:disabled) {
    color: #fff;
    border-color: var(--border-orange);
}

.pagination button.active {
    color: #fff;
    border-color: var(--orange);
    background: var(--orange-soft);
}

.pagination button:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

/* Modal */

.modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 18px;
    overflow-y: auto;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

.modal {
    width: 100%;
    max-width: 650px;
    max-height: calc(100vh - 36px);
    padding: 21px;
    overflow-y: auto;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 13px;
    background:
        linear-gradient(
            145deg,
            #101010,
            #080808
        );
    box-shadow:
        0 30px 100px rgba(0, 0, 0, 0.5);
}

.modal-enter-active,
.modal-leave-active {
    transition: opacity 180ms ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

/* Focus */

.refresh-button:focus-visible,
.clear-search:focus-visible,
.reset-button:focus-visible,
.action button:focus-visible,
.pagination button:focus-visible,
.filters select:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 3px;
}

/* Animation */

.spin {
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Responsive */

@media (max-width: 950px) {
    .filters {
        grid-template-columns:
            1fr 150px 40px;
    }

    .requests-container {
        overflow-x: auto;
    }

    .table-header,
    .request-row {
        min-width: 850px;
    }
}

@media (max-width: 700px) {
    .page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .refresh-button {
        width: 100%;
    }

    .filters {
        grid-template-columns: 1fr 1fr;
    }

    .search-box {
        grid-column: 1 / -1;
    }

    .reset-button {
        width: 100%;
    }

    .modal-overlay {
        padding: 8px;
        align-items: flex-start;
    }

    .modal {
        max-height: calc(100vh - 16px);
        padding: 16px;
    }
}

@media (max-width: 430px) {
    .filters {
        grid-template-columns: 1fr;
    }

    .search-box {
        grid-column: auto;
    }

    .reset-button {
        width: 100%;
    }

    .page-header h1 {
        font-size: 25px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .refresh-button,
    .reset-button,
    .request-row,
    .action button,
    .pagination button,
    .modal-enter-active,
    .modal-leave-active {
        transition: none;
    }

    .spin {
        animation: none;
    }
}
</style>
