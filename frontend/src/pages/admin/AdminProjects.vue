<script setup>
import {
    computed,
    onMounted,
    onUnmounted,
    ref,
} from 'vue'

import {
    Edit3,
    Eye,
    FolderPlus,
    Image as ImageIcon,
    LoaderCircle,
    RefreshCw,
    Search,
    Trash2,
    X,
} from 'lucide-vue-next'

import { useRouter } from 'vue-router'

import { useAdminProjectsStore } from '../../stores/adminProjects'
import ProjectForm from '../../components/admin/ProjectForm.vue'

const router = useRouter()
const store = useAdminProjectsStore()

const showForm = ref(false)
const editingProject = ref(null)
const searchInput = ref('')

let searchTimer = null

const totalPages = computed(() => {
    return Math.max(1, store.pagination.lastPage)
})

const openCreate = () => {
    editingProject.value = null
    showForm.value = true
}

const openEdit = (project) => {
    editingProject.value = project
    showForm.value = true
}

const closeForm = () => {
    showForm.value = false
    editingProject.value = null
}

const handleSaved = async () => {
    closeForm()
    await store.fetchProjects()
}

const search = () => {
    store.filters.search = searchInput.value.trim()
    store.filters.page = 1

    store.fetchProjects()
}

const handleSearchInput = () => {
    clearTimeout(searchTimer)

    searchTimer = setTimeout(() => {
        search()
    }, 450)
}

const filterProjects = () => {
    store.filters.page = 1
    store.fetchProjects()
}

const resetFilters = () => {
    searchInput.value = ''

    store.resetFilters()
    store.fetchProjects()
}

const clearSearch = () => {
    searchInput.value = ''
    search()
}

const goToPage = (page) => {
    if (
        page < 1 ||
        page > totalPages.value ||
        page === store.pagination.currentPage
    ) {
        return
    }

    store.filters.page = page
    store.fetchProjects()
}

const deleteProject = async (project) => {
    const confirmed = window.confirm(
        `آیا از حذف پروژه «${project.title}» مطمئن هستید؟`
    )

    if (!confirmed) {
        return
    }

    const result = await store.deleteProject(project.slug)

    if (!result.success) {
        return
    }

    await store.fetchProjects()
}

const viewProject = (project) => {
    router.push({
        name: 'project-details',
        params: {
            slug: project.slug,
        },
    })
}

const statusLabel = (status) => {
    const labels = {
        draft: 'پیش‌نویس',
        published: 'منتشر شده',
        archived: 'آرشیو',
    }

    return labels[status] || status || 'نامشخص'
}

const statusClass = (status) => {
    return {
        draft: 'draft',
        published: 'published',
        archived: 'archived',
    }[status] || 'draft'
}

const formatPrice = (price, currency) => {
    if (
        price === null ||
        price === undefined ||
        price === ''
    ) {
        return 'رایگان / تعیین نشده'
    }

    const numericPrice = Number(price)

    if (Number.isNaN(numericPrice)) {
        return 'نامشخص'
    }

    return `${new Intl.NumberFormat('en-US').format(
        numericPrice
    )} ${currency || ''}`.trim()
}

const imageUrl = (project) => {
    return project?.images?.[0]?.url || null
}

onMounted(() => {
    store.fetchProjects()
})

onUnmounted(() => {
    clearTimeout(searchTimer)
})
</script>

<template>
    <section
        class="projects-page"
        aria-labelledby="admin-projects-title"
    >
        <!-- Page Header -->
        <header class="page-header">
            <div>
                <span class="kicker">
                    ADMIN / PROJECTS
                </span>

                <h1 id="admin-projects-title">
                    مدیریت پروژه‌ها
                </h1>

                <p>
                    ایجاد، ویرایش، انتشار و مدیریت
                    فایل‌ها و تصاویر پروژه‌ها.
                </p>
            </div>

            <button
                type="button"
                class="create-button"
                @click="openCreate"
            >
                <FolderPlus
                    :size="17"
                    aria-hidden="true"
                />

                <span>
                    پروژه جدید
                </span>
            </button>
        </header>

        <!-- Filters -->
        <section
            class="filters-card"
            aria-label="فیلتر و جستجوی پروژه‌ها"
        >
            <div class="search-box">
                <Search
                    :size="16"
                    aria-hidden="true"
                />

                <input
                    v-model="searchInput"
                    type="search"
                    aria-label="جستجوی پروژه"
                    placeholder="جستجوی پروژه..."
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
                aria-label="فیلتر وضعیت پروژه"
                @change="filterProjects"
            >
                <option value="">
                    همه وضعیت‌ها
                </option>

                <option value="published">
                    منتشر شده
                </option>

                <option value="draft">
                    پیش‌نویس
                </option>

                <option value="archived">
                    آرشیو
                </option>
            </select>

            <select
                v-model="store.filters.is_for_sale"
                aria-label="فیلتر وضعیت فروش"
                @change="filterProjects"
            >
                <option value="">
                    فروش
                </option>

                <option value="1">
                    قابل فروش
                </option>

                <option value="0">
                    غیرقابل فروش
                </option>
            </select>

            <select
                v-model="store.filters.is_featured"
                aria-label="فیلتر پروژه‌های منتخب"
                @change="filterProjects"
            >
                <option value="">
                    Featured
                </option>

                <option value="1">
                    منتخب
                </option>

                <option value="0">
                    عادی
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
                !store.projects.length
            "
            class="loading-state"
            role="status"
            aria-live="polite"
        >
            <LoaderCircle
                :size="26"
                class="spin"
                aria-hidden="true"
            />

            <span>
                در حال دریافت پروژه‌ها...
            </span>
        </div>

        <!-- Empty -->
        <div
            v-else-if="store.isEmpty"
            class="empty-state"
        >
            <FolderPlus
                :size="30"
                aria-hidden="true"
            />

            <h2>
                پروژه‌ای پیدا نشد
            </h2>

            <p>
                فیلترها را تغییر دهید یا یک پروژه جدید
                ایجاد کنید.
            </p>

            <button
                type="button"
                class="create-button small"
                @click="openCreate"
            >
                <FolderPlus
                    :size="15"
                    aria-hidden="true"
                />

                پروژه جدید
            </button>
        </div>

        <!-- Projects -->
        <section
            v-else
            class="projects-container"
            aria-label="لیست پروژه‌ها"
        >
            <div
                class="table-header"
                aria-hidden="true"
            >
                <span>
                    پروژه
                </span>

                <span>
                    وضعیت
                </span>

                <span>
                    فروش
                </span>

                <span>
                    قیمت
                </span>

                <span>
                    عملیات
                </span>
            </div>

            <article
                v-for="project in store.projects"
                :key="project.id"
                class="project-row"
            >
                <!-- Project -->
                <div class="project-cell">
                    <div class="project-image">
                        <img
                            v-if="imageUrl(project)"
                            :src="imageUrl(project)"
                            :alt="`تصویر ${project.title}`"
                            loading="lazy"
                            decoding="async"
                        />

                        <ImageIcon
                            v-else
                            :size="19"
                            aria-hidden="true"
                        />
                    </div>

                    <div class="project-name">
                        <strong>
                            {{ project.title }}
                        </strong>

                        <span>
                            /{{ project.slug }}
                        </span>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <span
                        class="status"
                        :class="
                            statusClass(
                                project.status
                            )
                        "
                    >
                        {{
                            statusLabel(
                                project.status
                            )
                        }}
                    </span>
                </div>

                <!-- Sale -->
                <div>
                    <span
                        class="sale-status"
                        :class="{
                            active:
                                project.is_for_sale,
                        }"
                    >
                        {{
                            project.is_for_sale
                                ? 'قابل فروش'
                                : 'غیرفعال'
                        }}
                    </span>
                </div>

                <!-- Price -->
                <div class="price">
                    {{
                        formatPrice(
                            project.price,
                            project.currency
                        )
                    }}
                </div>

                <!-- Actions -->
                <div class="actions">
                    <button
                        type="button"
                        title="مشاهده پروژه"
                        :aria-label="`مشاهده پروژه ${project.title}`"
                        @click="viewProject(project)"
                    >
                        <Eye
                            :size="16"
                            aria-hidden="true"
                        />
                    </button>

                    <button
                        type="button"
                        title="ویرایش پروژه"
                        :aria-label="`ویرایش پروژه ${project.title}`"
                        @click="openEdit(project)"
                    >
                        <Edit3
                            :size="16"
                            aria-hidden="true"
                        />
                    </button>

                    <button
                        type="button"
                        class="danger"
                        title="حذف پروژه"
                        :aria-label="`حذف پروژه ${project.title}`"
                        :disabled="store.deleting"
                        @click="deleteProject(project)"
                    >
                        <Trash2
                            :size="16"
                            aria-hidden="true"
                        />
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
            aria-label="صفحه‌بندی پروژه‌ها"
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
                v-for="page in totalPages"
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

        <!-- Project Form Modal -->
        <Teleport to="body">
            <Transition name="modal">
                <div
                    v-if="showForm"
                    class="form-overlay"
                    role="presentation"
                    @click.self="closeForm"
                >
                    <div
                        class="form-modal"
                        role="dialog"
                        aria-modal="true"
                        aria-label="
                            editingProject
                                ? 'ویرایش پروژه'
                                : 'ایجاد پروژه جدید'
                        "
                    >
                        <ProjectForm
                            :project="editingProject"
                            @saved="handleSaved"
                            @cancel="closeForm"
                        />
                    </div>
                </div>
            </Transition>
        </Teleport>
    </section>
</template>

<style scoped>
.projects-page {
    direction: rtl;
}

/* Header */

.page-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 24px;
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

.create-button {
    min-height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding-inline: 15px;
    color: #fff;
    border: 0;
    border-radius: 8px;
    background: var(--orange);
    font-family: inherit;
    font-size: 11px;
    font-weight: 800;
    line-height: 1.6;
    cursor: pointer;
    transition:
        background var(--transition),
        transform var(--transition);
}

.create-button:hover {
    background: var(--orange-light);
    transform: translateY(-1px);
}

.create-button.small {
    margin-top: 15px;
}

/* Filters */

.filters-card {
    padding: 9px;
    display: grid;
    grid-template-columns:
        minmax(220px, 1fr)
        145px
        130px
        130px
        40px;
    gap: 7px;
    margin-bottom: 11px;
    border: 1px solid var(--border);
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.018);
}

.search-box {
    min-height: 40px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding-inline: 10px;
    border: 1px solid var(--border);
    border-radius: 7px;
    background: rgba(0, 0, 0, 0.16);
}

.search-box > svg {
    flex-shrink: 0;
    color: #666;
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

.clear-search {
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

.clear-search:hover {
    color: var(--orange);
}

.filters-card select {
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

.filters-card select:focus {
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
    border: 1px solid rgba(232, 122, 122, 0.14);
    border-radius: 8px;
    background: rgba(232, 122, 122, 0.025);
    font-size: 11px;
    line-height: 1.8;
}

/* Loading */

.loading-state {
    min-height: 420px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 11px;
    color: #666;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.01);
    font-size: 11px;
}

/* Empty */

.empty-state {
    min-height: 360px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #555;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.01);
    text-align: center;
}

.empty-state h2 {
    margin-top: 12px;
    color: #bdbdbd;
    font-size: 15px;
    font-weight: 800;
}

.empty-state p {
    margin-top: 5px;
    color: var(--text-muted);
    font-size: 11px;
    line-height: 1.8;
}

/* Projects */

.projects-container {
    overflow: hidden;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.012);
}

.table-header,
.project-row {
    display: grid;
    grid-template-columns:
        minmax(270px, 2fr)
        130px
        110px
        160px
        120px;
    align-items: center;
    gap: 15px;
    padding-inline: 15px;
}

.table-header {
    min-height: 42px;
    color: #5c5c5c;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    background: rgba(255, 255, 255, 0.012);
    font-size: 10px;
    font-weight: 600;
}

.project-row {
    min-height: 78px;
    color: #aaa;
    border-bottom: 1px solid rgba(255, 255, 255, 0.045);
    transition: background var(--transition);
}

.project-row:last-child {
    border-bottom: 0;
}

.project-row:hover {
    background: rgba(255, 106, 0, 0.018);
}

/* Project */

.project-cell {
    min-width: 0;
    display: flex;
    align-items: center;
    gap: 11px;
}

.project-image {
    width: 52px;
    height: 40px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    color: #555;
    border: 1px solid var(--border);
    border-radius: 6px;
    background: #090909;
}

.project-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.project-name {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.project-name strong {
    overflow: hidden;
    color: #dedede;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.7;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.project-name span {
    overflow: hidden;
    color: #555;
    direction: ltr;
    text-align: right;
    font-family: var(--font-mono);
    font-size: 8px;
    line-height: 1.5;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Status */

.status {
    width: fit-content;
    display: inline-flex;
    align-items: center;
    padding: 5px 8px;
    border-radius: 5px;
    font-size: 9px;
    font-weight: 700;
    line-height: 1.5;
}

.status.published {
    color: #4ad58a;
    background: rgba(74, 213, 138, 0.05);
}

.status.draft {
    color: #dca24d;
    background: rgba(220, 162, 77, 0.05);
}

.status.archived {
    color: #888;
    background: rgba(255, 255, 255, 0.035);
}

.sale-status {
    color: #666;
    font-size: 9px;
    line-height: 1.6;
}

.sale-status.active {
    color: #ff8b42;
}

/* Price */

.price {
    direction: ltr;
    color: #aaa;
    font-family: var(--font-mono);
    font-size: 9px;
    line-height: 1.6;
    text-align: right;
}

/* Actions */

.actions {
    display: flex;
    align-items: center;
    gap: 5px;
}

.actions button {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #686868;
    border: 1px solid var(--border);
    border-radius: 6px;
    background: transparent;
    cursor: pointer;
    transition:
        color var(--transition),
        border-color var(--transition),
        background var(--transition);
}

.actions button:hover:not(:disabled) {
    color: var(--orange);
    border-color: var(--border-orange);
    background: var(--orange-soft);
}

.actions button.danger:hover:not(:disabled) {
    color: #e87171;
    border-color: rgba(232, 113, 113, 0.18);
    background: rgba(232, 113, 113, 0.025);
}

.actions button:disabled {
    opacity: 0.5;
    cursor: wait;
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
    opacity: 0.35;
    cursor: not-allowed;
}

/* Modal */

.form-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 18px;
    overflow-y: auto;
    background: rgba(0, 0, 0, 0.78);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

.form-modal {
    width: 100%;
    max-width: 760px;
    max-height: calc(100vh - 36px);
    padding: 22px;
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
        0 30px 100px rgba(0, 0, 0, 0.45);
}

.modal-enter-active,
.modal-leave-active {
    transition: opacity 180ms ease;
}

.modal-enter-active .form-modal,
.modal-leave-active .form-modal {
    transition: transform 180ms ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-from .form-modal,
.modal-leave-to .form-modal {
    transform: translateY(14px) scale(0.98);
}

/* Focus */

.create-button:focus-visible,
.clear-search:focus-visible,
.reset-button:focus-visible,
.actions button:focus-visible,
.pagination button:focus-visible,
.filters-card select:focus-visible {
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

@media (max-width: 1100px) {
    .filters-card {
        grid-template-columns:
            1fr 130px 120px 120px 40px;
    }

    .table-header,
    .project-row {
        grid-template-columns:
            minmax(230px, 2fr)
            110px
            90px
            135px
            110px;
    }
}

@media (max-width: 850px) {
    .filters-card {
        grid-template-columns: 1fr 1fr;
        gap: 7px;
    }

    .search-box {
        grid-column: 1 / -1;
    }

    .reset-button {
        width: 100%;
    }

    .projects-container {
        overflow-x: auto;
    }

    .table-header,
    .project-row {
        min-width: 760px;
    }
}

@media (max-width: 650px) {
    .page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .create-button {
        width: 100%;
    }

    .page-header h1 {
        font-size: 25px;
    }

    .form-overlay {
        padding: 8px;
    }

    .form-modal {
        padding: 16px;
        max-height: calc(100vh - 16px);
        border-radius: 10px;
    }
}

@media (max-width: 430px) {
    .filters-card {
        grid-template-columns: 1fr;
    }

    .search-box {
        grid-column: auto;
    }

    .reset-button {
        width: 100%;
    }
}

@media (prefers-reduced-motion: reduce) {
    .create-button,
    .reset-button,
    .project-row,
    .actions button,
    .pagination button,
    .modal-enter-active,
    .modal-leave-active,
    .modal-enter-active .form-modal,
    .modal-leave-active .form-modal {
        transition: none;
    }

    .create-button:hover {
        transform: none;
    }

    .spin {
        animation: none;
    }
}
</style>
