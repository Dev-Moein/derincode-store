<script setup>
import { onMounted, ref } from 'vue'
import { LoaderCircle, RefreshCw, Search } from 'lucide-vue-next'

import { useAdminUsersStore } from '../../stores/adminUsers'
import UserTable from '../../components/admin/UserTable.vue'

const store = useAdminUsersStore()

const search = ref('')

const loadUsers = async () => {
    store.filters.search = search.value.trim()
    store.filters.page = 1

    await store.fetchUsers()
}

const changePage = async (page) => {
    if (
        page === store.pagination.currentPage ||
        store.loading
    ) {
        return
    }

    store.filters.page = page

    await store.fetchUsers()
}

const refreshUsers = async () => {
    await store.fetchUsers()
}

onMounted(() => {
    loadUsers()
})
</script>

<template>
    <main
        class="admin-page"
        aria-labelledby="users-page-title"
    >
        <header class="page-header">
            <div class="page-heading">
                <span class="kicker">
                    ADMIN / USERS
                </span>

                <h1 id="users-page-title">
                    مدیریت کاربران
                </h1>

                <p>
                    مشاهده، جستجو و مدیریت کاربران ثبت‌نام‌شده.
                    تعداد کاربران:
                    <strong>
                        {{ store.userCount }}
                    </strong>
                </p>
            </div>

            <div class="search-box">
                <label
                    for="user-search"
                    class="sr-only"
                >
                    جستجوی کاربران
                </label>

                <div class="search-input">
                    <Search
                        :size="15"
                        aria-hidden="true"
                    />

                    <input
                        id="user-search"
                        v-model="search"
                        type="search"
                        autocomplete="off"
                        placeholder="نام، ایمیل یا شماره..."
                        @keyup.enter="loadUsers"
                    />
                </div>

                <button
                    type="button"
                    class="search-button"
                    :disabled="store.loading"
                    :aria-busy="store.loading"
                    @click="loadUsers"
                >
                    <LoaderCircle
                        v-if="store.loading"
                        :size="14"
                        class="spin"
                        aria-hidden="true"
                    />

                    <Search
                        v-else
                        :size="14"
                        aria-hidden="true"
                    />

                    جستجو
                </button>

                <button
                    type="button"
                    class="refresh-button"
                    :disabled="store.loading"
                    :aria-busy="store.loading"
                    aria-label="به‌روزرسانی لیست کاربران"
                    @click="refreshUsers"
                >
                    <RefreshCw
                        :size="15"
                        :class="{ spin: store.loading }"
                        aria-hidden="true"
                    />
                </button>
            </div>
        </header>

        <!-- Loading -->
        <div
            v-if="store.loading"
            class="state"
            role="status"
            aria-live="polite"
        >
            <LoaderCircle
                :size="23"
                class="spin"
                aria-hidden="true"
            />

            <span>
                در حال دریافت کاربران...
            </span>
        </div>

        <!-- Error -->
        <div
            v-else-if="store.error"
            class="error-state"
            role="alert"
            aria-live="assertive"
        >
            <span>
                {{ store.error }}
            </span>

            <button
                type="button"
                @click="loadUsers"
            >
                تلاش مجدد
            </button>
        </div>

        <!-- Users -->
        <section
            v-else
            class="users-section"
            aria-labelledby="users-list-title"
        >
            <header class="section-header">
                <div>
                    <span class="section-kicker">
                        USERS
                    </span>

                    <h2 id="users-list-title">
                        لیست کاربران
                    </h2>
                </div>

                <span class="result-count">
                    {{ store.userCount }} کاربر
                </span>
            </header>

            <UserTable
                :users="store.users"
                @refresh="loadUsers"
            />

            <!-- Pagination -->
            <nav
                v-if="store.pagination.lastPage > 1"
                class="pagination"
                aria-label="صفحه‌بندی کاربران"
            >
                <button
                    v-for="
                        page in store.pagination.lastPage
                    "
                    :key="page"
                    type="button"
                    :class="{
                        active:
                            page ===
                            store.pagination.currentPage
                    }"
                    :disabled="
                        store.loading ||
                        page ===
                        store.pagination.currentPage
                    "
                    :aria-current="
                        page ===
                        store.pagination.currentPage
                            ? 'page'
                            : undefined
                    "
                    :aria-label="`صفحه ${page}`"
                    @click="changePage(page)"
                >
                    {{ page }}
                </button>
            </nav>
        </section>
    </main>
</template>

<style scoped>
.admin-page {
    direction: rtl;
}

/* Header */

.page-header {
    display: flex;

    align-items: flex-end;
    justify-content: space-between;

    gap: 24px;

    margin-bottom: 24px;
}

.page-heading {
    min-width: 0;
}

.kicker,
.section-kicker {
    display: block;

    color: #ff6a00;

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 10px;

    letter-spacing: 0.08em;
}

.page-heading h1 {
    margin-top: 6px;

    color: #f2f2f2;

    font-size: 29px;

    font-weight: 900;

    line-height: 1.35;
}

.page-heading p {
    margin-top: 6px;

    color: #777;

    font-size: 11px;

    line-height: 1.8;
}

.page-heading strong {
    color: #bbb;

    font-weight: 700;
}

/* Search */

.search-box {
    display: flex;

    align-items: center;

    gap: 7px;
}

.search-input {
    width: 290px;

    min-height: 40px;

    display: flex;

    align-items: center;

    gap: 8px;

    padding-inline: 11px;

    color: #555;

    border:
        1px solid rgba(255, 255, 255, 0.07);

    border-radius: 7px;

    background: #0d0d0d;

    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}

.search-input:focus-within {
    border-color:
        rgba(255, 106, 0, 0.4);

    box-shadow:
        0 0 0 3px rgba(255, 106, 0, 0.06);
}

.search-input input {
    width: 100%;

    min-width: 0;

    padding: 0;

    color: #ddd;

    border: 0;

    outline: 0;

    background: transparent;

    font-family: inherit;

    font-size: 11px;
}

.search-input input::placeholder {
    color: #555;
}

.search-button,
.refresh-button {
    min-height: 40px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 7px;

    color: #fff;

    border: 0;

    border-radius: 7px;

    background: #ff6a00;

    font-family: inherit;

    font-size: 11px;

    font-weight: 700;

    cursor: pointer;

    transition:
        background 0.2s ease,
        opacity 0.2s ease;
}

.search-button {
    padding-inline: 13px;
}

.refresh-button {
    width: 40px;

    color: #aaa;

    border:
        1px solid rgba(255, 255, 255, 0.07);

    background: transparent;
}

.search-button:hover:not(:disabled) {
    background: #ff781b;
}

.refresh-button:hover:not(:disabled) {
    color: #ff6a00;

    border-color:
        rgba(255, 106, 0, 0.2);
}

.search-button:disabled,
.refresh-button:disabled {
    opacity: 0.45;

    cursor: not-allowed;
}

/* Section */

.users-section {
    padding: 18px;

    border:
        1px solid rgba(255, 255, 255, 0.055);

    border-radius: 11px;

    background:
        rgba(255, 255, 255, 0.01);
}

.section-header {
    display: flex;

    align-items: center;
    justify-content: space-between;

    gap: 15px;

    margin-bottom: 16px;
}

.section-kicker {
    margin-bottom: 4px;

    color: #555;

    font-size: 9px;
}

.section-header h2 {
    color: #d7d7d7;

    font-size: 14px;

    font-weight: 800;
}

.result-count {
    color: #666;

    font-size: 10px;
}

/* States */

.state {
    min-height: 300px;

    display: flex;

    flex-direction: column;

    align-items: center;
    justify-content: center;

    gap: 9px;

    color: #666;

    border:
        1px solid rgba(255, 255, 255, 0.055);

    border-radius: 11px;

    background:
        rgba(255, 255, 255, 0.01);

    font-size: 11px;
}

.error-state {
    padding: 14px 16px;

    display: flex;

    align-items: center;
    justify-content: space-between;

    gap: 15px;

    color: #e87878;

    border:
        1px solid rgba(232, 120, 120, 0.12);

    border-radius: 9px;

    background:
        rgba(232, 120, 120, 0.02);

    font-size: 11px;
}

.error-state button {
    min-height: 34px;

    padding-inline: 11px;

    color: #ddd;

    border:
        1px solid rgba(255, 255, 255, 0.07);

    border-radius: 6px;

    background: transparent;

    font-family: inherit;

    font-size: 10px;

    cursor: pointer;
}

/* Pagination */

.pagination {
    margin-top: 18px;

    display: flex;

    align-items: center;
    justify-content: center;

    flex-wrap: wrap;

    gap: 5px;
}

.pagination button {
    min-width: 34px;

    height: 34px;

    padding-inline: 7px;

    color: #777;

    border:
        1px solid rgba(255, 255, 255, 0.06);

    border-radius: 6px;

    background: #171717;

    font-family: inherit;

    font-size: 10px;

    cursor: pointer;

    transition:
        color 0.2s ease,
        border-color 0.2s ease,
        background 0.2s ease;
}

.pagination button:hover:not(:disabled) {
    color: #ddd;

    border-color:
        rgba(255, 106, 0, 0.2);
}

.pagination button.active {
    color: #fff;

    border-color: #ff6a00;

    background: #ff6a00;
}

.pagination button:disabled {
    cursor: default;
}

/* Accessibility */

.sr-only {
    position: absolute;

    width: 1px;
    height: 1px;

    padding: 0;
    margin: -1px;

    overflow: hidden;

    clip: rect(0, 0, 0, 0);

    white-space: nowrap;

    border: 0;
}

button:focus-visible,
input:focus-visible {
    outline: 2px solid #ff6a00;

    outline-offset: 2px;
}

/* Animation */

.spin {
    animation: spin 0.9s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Responsive */

@media (max-width: 850px) {
    .page-header {
        align-items: stretch;

        flex-direction: column;
    }

    .search-box {
        width: 100%;
    }

    .search-input {
        flex: 1;

        width: auto;
    }
}

@media (max-width: 600px) {
    .users-section {
        padding: 13px;
    }

    .page-heading h1 {
        font-size: 25px;
    }

    .search-box {
        flex-wrap: wrap;
    }

    .search-input {
        flex-basis: 100%;
    }

    .search-button {
        flex: 1;
    }

    .refresh-button {
        flex-shrink: 0;
    }

    .error-state {
        align-items: flex-start;

        flex-direction: column;
    }

    .error-state button {
        width: 100%;
    }
}

@media (max-width: 420px) {
    .section-header {
        align-items: flex-start;

        flex-direction: column;
    }

    .pagination {
        gap: 4px;
    }

    .pagination button {
        min-width: 31px;

        height: 31px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .spin {
        animation: none;
    }

    .search-input,
    .search-button,
    .refresh-button,
    .pagination button {
        transition: none;
    }
}
</style>
