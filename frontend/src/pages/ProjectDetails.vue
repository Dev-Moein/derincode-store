<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'

import {
    ArrowLeft,
    ArrowRight,
    CheckCircle2,
    CreditCard,
    ExternalLink,
    Image as ImageIcon,
    LoaderCircle,
    Tag,
    Wallet,
    Download,
} from 'lucide-vue-next'

import { useAuthStore } from '../stores/auth'
import { usePaymentsStore } from '../stores/payments'
import ProjectRequestForm from '../components/projects/ProjectRequestForm.vue'
import api from '../services/api'
import { useDownloadsStore } from '../stores/downloads'


const downloadsStore = useDownloadsStore()

const downloadLoading = ref(false)
const route = useRoute()
const router = useRouter()

const authStore = useAuthStore()
const paymentsStore = usePaymentsStore()

const project = ref(null)
const loading = ref(true)
const error = ref(null)

const activeImage = ref(0)

const requestModalOpen = ref(false)
const paymentLoading = ref(false)

/*
|--------------------------------------------------------------------------
| Computed
|--------------------------------------------------------------------------
*/


const downloadProject = async () => {
    if (!project.value) {
        return
    }

    if (!authStore.isAuthenticated) {
        router.push({
            name: 'login',
            query: {
                redirect: route.fullPath,
            },
        })

        return
    }

    downloadLoading.value = true

    const result =
        await downloadsStore.downloadProject(
            project.value.id
        )

    downloadLoading.value = false

    if (!result.success) {
        console.error(
            downloadsStore.error
        )
    }
}

const images = computed(() => {
    return project.value?.images || []
})

const currentImage = computed(() => {
    return images.value[activeImage.value] || null
})

const projectPrice = computed(() => {
    if (!project.value?.is_for_sale) {
        return null
    }

    if (
        project.value.price === null ||
        project.value.price === undefined
    ) {
        return null
    }

    return `${project.value.price} ${
        project.value.currency || ''
    }`.trim()
})

/*
|--------------------------------------------------------------------------
| Project
|--------------------------------------------------------------------------
*/

const fetchProject = async () => {
    loading.value = true
    error.value = null

    try {
        const response = await api.get(
            `/projects/${route.params.slug}`
        )

        project.value =
            response?.data?.data || null

        activeImage.value = 0

        if (!project.value) {
            error.value =
                'پروژه موردنظر پیدا نشد.'
        }
    } catch (err) {
        console.error(
            'Failed to fetch project:',
            err
        )

        if (err?.response?.status === 404) {
            error.value =
                'پروژه موردنظر پیدا نشد.'
        } else {
            error.value =
                err?.response?.data?.message ||
                'دریافت اطلاعات پروژه با مشکل مواجه شد.'
        }

        project.value = null
    } finally {
        loading.value = false
    }
}

/*
|--------------------------------------------------------------------------
| Gallery
|--------------------------------------------------------------------------
*/

const selectImage = (index) => {
    if (
        index < 0 ||
        index >= images.value.length
    ) {
        return
    }

    activeImage.value = index
}

const nextImage = () => {
    if (images.value.length <= 1) {
        return
    }

    activeImage.value =
        (activeImage.value + 1) %
        images.value.length
}

const previousImage = () => {
    if (images.value.length <= 1) {
        return
    }

    activeImage.value =
        (activeImage.value - 1 + images.value.length) %
        images.value.length
}

/*
|--------------------------------------------------------------------------
| Navigation
|--------------------------------------------------------------------------
*/

const goBack = () => {
    router.push({
        name: 'home',
        hash: '#projects',
    })
}

/*
|--------------------------------------------------------------------------
| Project Request
|--------------------------------------------------------------------------
*/

const openRequest = () => {
    if (!authStore.isAuthenticated) {
        router.push({
            name: 'login',
            query: {
                redirect: route.fullPath,
            },
        })

        return
    }

    requestModalOpen.value = true
}

const closeRequest = () => {
    requestModalOpen.value = false
}

const handleRequestSuccess = () => {
    requestModalOpen.value = false
}

/*
|--------------------------------------------------------------------------
| Payment
|--------------------------------------------------------------------------
*/

const buyProject = async () => {
    if (!project.value) {
        return
    }

    if (!authStore.isAuthenticated) {
        router.push({
            name: 'login',
            query: {
                redirect: route.fullPath,
            },
        })

        return
    }

    if (!project.value.is_for_sale) {
        openRequest()

        return
    }

    paymentLoading.value = true

    const result =
        await paymentsStore.createPayment({
            projectId: project.value.id,
            gateway: 'zarinpal',
        })

    paymentLoading.value = false

    if (!result.success) {
        return
    }

    if (!result.paymentUrl) {
        console.error(
            'Payment URL is missing.'
        )

        return
    }

    window.location.href =
        result.paymentUrl
}

/*
|--------------------------------------------------------------------------
| Lifecycle
|--------------------------------------------------------------------------
*/

onMounted(fetchProject)
</script>

<template>
    <div class="project-page">

        <!-- ========================================================= -->
        <!-- Header -->
        <!-- ========================================================= -->

        <header class="project-header">
            <div class="container project-header-inner">

                <button
                    type="button"
                    class="back-link"
                    @click="goBack"
                >
                    <ArrowRight :size="15" />

                    <span>
                        بازگشت به نمونه‌کارها
                    </span>
                </button>

                <RouterLink
                    to="/"
                    class="project-logo"
                >
                    <span>Derin</span><strong>Code</strong>
                </RouterLink>

            </div>
        </header>

        <!-- ========================================================= -->
        <!-- Main -->
        <!-- ========================================================= -->

        <main class="container project-main">

            <!-- Loading -->
            <div
                v-if="loading"
                class="page-state"
            >
                <LoaderCircle
                    :size="30"
                    class="loader"
                />

                <p>
                    در حال دریافت اطلاعات پروژه...
                </p>
            </div>

            <!-- Error -->
            <div
                v-else-if="error"
                class="page-state"
            >
                <div class="state-icon">
                    <ImageIcon :size="27" />
                </div>

                <h1>
                    {{ error }}
                </h1>

                <p>
                    ممکن است پروژه حذف شده باشد یا
                    موقتاً در دسترس نباشد.
                </p>

                <button
                    type="button"
                    class="state-button"
                    @click="goBack"
                >
                    <ArrowRight :size="15" />
                    نمونه‌کارها
                </button>
            </div>

            <!-- Project -->
            <template
                v-else-if="project"
            >

                <!-- Breadcrumb -->
                <div class="breadcrumb">
                    <span>
                        نمونه‌کارها
                    </span>

                    <span>/</span>

                    <strong>
                        {{ project.title }}
                    </strong>
                </div>

                <!-- Project Layout -->
                <section class="project-layout">

                    <!-- ================================================= -->
                    <!-- Gallery -->
                    <!-- ================================================= -->

                    <div class="gallery">

                        <div class="main-image">

                            <img
                                v-if="currentImage?.url"
                                :src="currentImage.url"
                                :alt="
                                    currentImage.alt ||
                                    project.title
                                "
                            />

                            <div
                                v-else
                                class="image-placeholder"
                            >
                                <ImageIcon :size="40" />

                                <span>
                                    تصویری برای این پروژه
                                    وجود ندارد
                                </span>
                            </div>

                            <!-- Gallery Controls -->
                            <div
                                v-if="images.length > 1"
                                class="image-counter"
                            >
                                <button
                                    type="button"
                                    aria-label="تصویر بعدی"
                                    @click="nextImage"
                                >
                                    <ArrowLeft :size="15" />
                                </button>

                                <span>
                                    {{ activeImage + 1 }}
                                    /
                                    {{ images.length }}
                                </span>

                                <button
                                    type="button"
                                    aria-label="تصویر قبلی"
                                    @click="previousImage"
                                >
                                    <ArrowRight :size="15" />
                                </button>
                            </div>

                        </div>

                        <!-- Thumbnails -->
                        <div
                            v-if="images.length > 1"
                            class="thumbnails"
                        >
                            <button
                                v-for="(
                                    image,
                                    index
                                ) in images"
                                :key="image.id"
                                type="button"
                                class="thumbnail"
                                :class="{
                                    active:
                                        index ===
                                        activeImage,
                                }"
                                @click="
                                    selectImage(index)
                                "
                            >
                                <img
                                    :src="image.url"
                                    :alt="
                                        image.alt ||
                                        project.title
                                    "
                                />
                            </button>
                        </div>

                    </div>

                    <!-- ================================================= -->
                    <!-- Project Info -->
                    <!-- ================================================= -->

                    <div class="project-info">

                        <!-- Labels -->
                        <div class="project-labels">

                            <span
                                v-if="
                                    project.is_featured
                                "
                                class="label featured"
                            >
                                پروژه منتخب
                            </span>

                            <span
                                v-if="project.status"
                                class="label status"
                            >
                                <i></i>

                                {{ project.status }}
                            </span>

                        </div>

                        <!-- Title -->
                        <h1 class="project-title">
                            {{ project.title }}
                        </h1>

                        <!-- Short Description -->
                        <p
                            v-if="
                                project.short_description
                            "
                            class="project-short"
                        >
                            {{ project.short_description }}
                        </p>

                        <div class="divider"></div>

                        <!-- Description -->
                        <div
                            v-if="project.description"
                            class="project-description"
                        >
                            {{ project.description }}
                        </div>

                        <!-- Details -->
                        <div class="details-grid">

                            <!-- Price -->
                            <div
                                v-if="
                                    project.is_for_sale
                                "
                                class="detail"
                            >
                                <div class="detail-icon">
                                    <Wallet :size="16" />
                                </div>

                                <div>
                                    <span>
                                        قیمت
                                    </span>

                                    <strong>
                                        {{
                                            projectPrice ||
                                            'قابل فروش'
                                        }}
                                    </strong>
                                </div>
                            </div>

                            <!-- File -->
                            <div
                                v-if="
                                    project.file?.name
                                "
                                class="detail"
                            >
                                <div class="detail-icon">
                                    <Tag :size="16" />
                                </div>

                                <div>
                                    <span>
                                        فایل پروژه
                                    </span>

                                    <strong>
                                        {{ project.file.name }}
                                    </strong>

                                    <small
                                        v-if="
                                            project.file?.size
                                        "
                                    >
                                        {{ project.file.size }}
                                    </small>
                                </div>
                            </div>

                            <!-- Type -->
                            <div class="detail">
                                <div class="detail-icon">
                                    <ExternalLink :size="16" />
                                </div>

                                <div>
                                    <span>
                                        نوع پروژه
                                    </span>

                                    <strong>
                                        {{
                                            project.is_for_sale
                                                ? 'محصول آماده'
                                                : 'پروژه اختصاصی'
                                        }}
                                    </strong>
                                </div>
                            </div>

                        </div>

                        <!-- ================================================= -->
                        <!-- Actions -->
                        <!-- ================================================= -->
<div class="project-actions">

    <!-- Download -->
    <button
        v-if="project.is_purchased"
        type="button"
        class="primary-action"
        :disabled="downloadLoading"
        @click="downloadProject"
    >
        <span v-if="!downloadLoading">
            دانلود پروژه
        </span>

        <span v-else>
            در حال آماده‌سازی دانلود...
        </span>

        <LoaderCircle
            v-if="downloadLoading"
            :size="16"
            class="payment-loader"
        />

        <Download
            v-else
            :size="16"
        />
    </button>

    <!-- Buy -->
    <button
        v-else-if="project.is_for_sale"
        type="button"
        class="primary-action"
        :disabled="paymentLoading"
        @click="buyProject"
    >
        <span v-if="!paymentLoading">
            خرید پروژه
        </span>

        <span v-else>
            در حال انتقال به درگاه...
        </span>

        <LoaderCircle
            v-if="paymentLoading"
            :size="16"
            class="payment-loader"
        />

        <CreditCard
            v-else
            :size="16"
        />
    </button>

    <!-- Request -->
    <button
        v-else
        type="button"
        class="primary-action"
        @click="openRequest"
    >
        <span>
            درخواست این پروژه
        </span>

        <ArrowLeft :size="16" />
    </button>

    <button
        type="button"
        class="secondary-action"
        @click="goBack"
    >
        بازگشت
    </button>

</div>

                        <!-- Note -->
                       <div class="project-note">
    <CheckCircle2 :size="15" />

    <span v-if="project.is_purchased">
        این پروژه قبلاً توسط شما خریداری شده و
        می‌توانید فایل آن را دانلود کنید.
    </span>

    <span
        v-else-if="project.is_for_sale"
    >
        با خرید پروژه، فایل قابل دانلود در حساب
        کاربری شما قرار می‌گیرد.
    </span>

    <span v-else>
        برای دریافت اطلاعات بیشتر یا سفارش نسخه
        اختصاصی این پروژه با ما در تماس باشید.
    </span>
</div>

                    </div>
                </section>

            </template>

        </main>

        <!-- ========================================================= -->
        <!-- Project Request Modal -->
        <!-- ========================================================= -->

        <Teleport to="body">

            <Transition name="request-modal">

                <div
                    v-if="requestModalOpen"
                    class="request-modal"
                    @click.self="closeRequest"
                >

                    <div class="request-modal-inner">

                        <button
                            type="button"
                            class="modal-close"
                            aria-label="بستن"
                            @click="closeRequest"
                        >
                            ×
                        </button>

                        <ProjectRequestForm
                            v-if="project"
                            :project="project"
                            @success="
                                handleRequestSuccess
                            "
                            @close="closeRequest"
                        />

                    </div>

                </div>

            </Transition>

        </Teleport>

    </div>
</template>

<style scoped>

/* ================================================================ */
/* Page */
/* ================================================================ */

.project-page {
    min-height: 100vh;

    overflow-x: hidden;

    background:
        radial-gradient(
            circle at 75% 20%,
            rgba(255, 107, 0, 0.035),
            transparent 25rem
        ),
        var(--bg-primary);
}

/* ================================================================ */
/* Header */
/* ================================================================ */

.project-header {
    padding: 18px 0;

    border-bottom: 1px solid var(--border);

    background:
        rgba(5, 6, 7, 0.74);

    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
}

.project-header-inner {
    display: flex;

    align-items: center;
    justify-content: space-between;

    gap: 20px;
}

.project-logo {
    direction: ltr;

    font-family: var(--font-mono);

    font-size: 18px;
    font-weight: 800;
}

.project-logo span {
    color: var(--text-primary);
}

.project-logo strong {
    color: var(--orange);
}

.back-link {
    display: inline-flex;

    align-items: center;

    gap: 7px;

    color: var(--text-muted);

    background: transparent;

    font-size: 10px;

    cursor: pointer;

    transition:
        color var(--transition);
}

.back-link:hover {
    color: var(--orange);
}

/* ================================================================ */
/* Main */
/* ================================================================ */

.project-main {
    padding-top: 30px;
    padding-bottom: 90px;
}

.breadcrumb {
    display: flex;

    align-items: center;

    gap: 8px;

    margin-bottom: 25px;

    color: var(--text-muted);

    font-size: 9px;
}

.breadcrumb strong {
    color: var(--text-secondary);
}

/* ================================================================ */
/* Layout */
/* ================================================================ */

.project-layout {
    display: grid;

    grid-template-columns:
        minmax(0, 1.18fr)
        minmax(330px, 0.82fr);

    gap: 48px;

    align-items: start;
}

/* ================================================================ */
/* Gallery */
/* ================================================================ */

.gallery {
    min-width: 0;
}

.main-image {
    position: relative;

    overflow: hidden;

    aspect-ratio: 16 / 10;

    border: 1px solid var(--border);

    border-radius: 16px;

    background: #090b0d;

    box-shadow:
        0 25px 70px rgba(0, 0, 0, 0.28);
}

.main-image img {
    width: 100%;
    height: 100%;

    object-fit: cover;
}

.image-placeholder {
    width: 100%;
    height: 100%;

    display: flex;

    flex-direction: column;

    align-items: center;
    justify-content: center;

    gap: 12px;

    color: var(--orange);

    text-align: center;
}

.image-placeholder span {
    color: var(--text-muted);

    font-size: 10px;
}

.image-counter {
    position: absolute;

    left: 13px;
    bottom: 13px;

    display: flex;

    align-items: center;

    gap: 6px;

    padding: 5px;

    border: 1px solid rgba(255, 255, 255, 0.1);

    border-radius: 8px;

    background:
        rgba(5, 6, 7, 0.82);

    backdrop-filter: blur(10px);
}

.image-counter button {
    width: 26px;
    height: 26px;

    display: flex;

    align-items: center;
    justify-content: center;

    color: var(--text-primary);

    border-radius: 6px;

    background: transparent;

    cursor: pointer;

    transition:
        color var(--transition),
        background var(--transition);
}

.image-counter button:hover {
    color: #111;

    background: var(--orange);
}

.image-counter span {
    min-width: 28px;

    color: var(--text-secondary);

    direction: ltr;

    text-align: center;

    font-family: var(--font-mono);

    font-size: 8px;
}

.thumbnails {
    display: flex;

    gap: 8px;

    margin-top: 10px;

    overflow-x: auto;

    padding-bottom: 3px;
}

.thumbnail {
    flex: 0 0 78px;

    height: 54px;

    padding: 0;

    overflow: hidden;

    border: 1px solid var(--border);

    border-radius: 7px;

    background: #090b0d;

    opacity: 0.48;

    cursor: pointer;

    transition:
        opacity var(--transition),
        border-color var(--transition);
}

.thumbnail:hover {
    opacity: 0.85;
}

.thumbnail.active {
    opacity: 1;

    border-color: var(--orange);
}

.thumbnail img {
    width: 100%;
    height: 100%;

    object-fit: cover;
}

/* ================================================================ */
/* Information */
/* ================================================================ */

.project-info {
    position: sticky;

    top: 35px;
}

.project-labels {
    display: flex;

    align-items: center;

    flex-wrap: wrap;

    gap: 7px;
}

.label {
    min-height: 23px;

    display: inline-flex;

    align-items: center;

    gap: 6px;

    padding: 0 8px;

    border-radius: 5px;

    font-size: 8px;

    font-weight: 700;
}

.label.featured {
    color: var(--orange);

    border: 1px solid rgba(255, 107, 0, 0.17);

    background:
        rgba(255, 107, 0, 0.04);
}

.label.status {
    color: var(--success);

    border: 1px solid rgba(55, 214, 122, 0.14);

    background:
        rgba(55, 214, 122, 0.025);
}

.label.status i {
    width: 5px;
    height: 5px;

    border-radius: 50%;

    background: var(--success);

    box-shadow:
        0 0 8px rgba(55, 214, 122, 0.45);
}

.project-title {
    margin-top: 18px;

    color: var(--text-primary);

    font-size: clamp(34px, 4vw, 52px);

    line-height: 1.2;

    font-weight: 900;

    letter-spacing: -0.045em;
}

.project-short {
    margin-top: 14px;

    color: var(--text-secondary);

    font-size: 12px;

    line-height: 2.1;
}

.divider {
    width: 100%;
    height: 1px;

    margin: 22px 0;

    background: var(--border);
}

.project-description {
    color: #92989d;

    font-size: 12px;

    line-height: 2.2;

    white-space: pre-line;
}

/* ================================================================ */
/* Details */
/* ================================================================ */

.details-grid {
    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 8px;

    margin-top: 25px;
}

.detail {
    min-height: 62px;

    display: flex;

    align-items: center;

    gap: 10px;

    padding: 10px;

    border: 1px solid var(--border);

    border-radius: 9px;

    background:
        rgba(255, 255, 255, 0.015);
}

.detail-icon {
    width: 33px;
    height: 33px;

    flex-shrink: 0;

    display: flex;

    align-items: center;
    justify-content: center;

    color: var(--orange);

    border: 1px solid rgba(255, 107, 0, 0.16);

    border-radius: 7px;

    background:
        rgba(255, 107, 0, 0.025);
}

.detail > div:last-child {
    min-width: 0;

    display: flex;

    flex-direction: column;
}

.detail span {
    color: var(--text-muted);

    font-size: 7px;
}

.detail strong {
    margin-top: 2px;

    overflow: hidden;

    color: var(--text-secondary);

    font-size: 9px;

    text-overflow: ellipsis;

    white-space: nowrap;
}

.detail small {
    margin-top: 2px;

    color: var(--text-muted);

    font-size: 7px;
}

/* ================================================================ */
/* Actions */
/* ================================================================ */

.project-actions {
    display: flex;

    gap: 8px;

    margin-top: 25px;
}

.primary-action,
.secondary-action {
    min-height: 44px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    border-radius: 8px;

    font-size: 10px;

    font-weight: 700;

    cursor: pointer;

    transition:
        background var(--transition),
        border-color var(--transition),
        transform var(--transition),
        opacity var(--transition);
}

.primary-action {
    flex: 1;

    gap: 8px;

    color: white;

    background: var(--orange);

    box-shadow:
        0 9px 25px rgba(255, 107, 0, 0.14);
}

.primary-action:hover:not(:disabled) {
    background: var(--orange-light);

    transform: translateY(-2px);
}

.primary-action:disabled {
    opacity: 0.7;

    cursor: wait;

    transform: none;
}

.loading-text {
    font-size: 9px;
}

.payment-loader {
    animation:
        spin 0.8s linear infinite;
}

.secondary-action {
    padding-inline: 17px;

    color: var(--text-primary);

    border: 1px solid var(--border-strong);

    background: transparent;
}

.secondary-action:hover {
    border-color: var(--border-orange);

    background: var(--orange-soft);

    transform: translateY(-2px);
}

.project-note {
    margin-top: 13px;

    display: flex;

    align-items: flex-start;

    gap: 7px;

    color: var(--text-muted);

    font-size: 8px;

    line-height: 1.9;
}

.project-note svg {
    flex-shrink: 0;

    margin-top: 2px;

    color: var(--orange);
}

/* ================================================================ */
/* Page States */
/* ================================================================ */

.page-state {
    min-height: 70vh;

    display: flex;

    flex-direction: column;

    align-items: center;
    justify-content: center;

    text-align: center;
}

.loader {
    color: var(--orange);

    animation:
        spin 1s linear infinite;
}

.page-state > p {
    margin-top: 12px;

    color: var(--text-muted);

    font-size: 10px;
}

.page-state h1 {
    margin-top: 17px;

    color: var(--text-primary);

    font-size: 24px;
}

.state-icon {
    width: 56px;
    height: 56px;

    display: flex;

    align-items: center;
    justify-content: center;

    color: var(--orange);

    border: 1px solid var(--border-orange);

    border-radius: 14px;

    background: var(--orange-soft);
}

.state-button {
    min-height: 40px;

    margin-top: 18px;

    padding-inline: 15px;

    display: inline-flex;

    align-items: center;

    gap: 7px;

    color: var(--text-primary);

    border: 1px solid var(--border);

    border-radius: 7px;

    background: transparent;

    font-size: 9px;

    font-weight: 700;

    cursor: pointer;

    transition:
        border-color var(--transition),
        background var(--transition);
}

.state-button:hover {
    border-color: var(--border-orange);

    background: var(--orange-soft);
}

/* ================================================================ */
/* Request Modal */
/* ================================================================ */

.request-modal {
    position: fixed;

    inset: 0;

    z-index: 9999;

    padding: 24px;

    display: flex;

    align-items: center;
    justify-content: center;

    overflow-y: auto;

    background:
        rgba(0, 0, 0, 0.78);

    backdrop-filter: blur(9px);

    -webkit-backdrop-filter: blur(9px);
}

.request-modal-inner {
    position: relative;

    width: 100%;

    max-width: 660px;

    display: flex;

    justify-content: center;

    padding-top: 6px;
}

.modal-close {
    position: absolute;

    z-index: 5;

    top: 16px;
    right: 16px;

    width: 30px;
    height: 30px;

    display: flex;

    align-items: center;
    justify-content: center;

    color: var(--text-secondary);

    border: 1px solid var(--border);

    border-radius: 50%;

    background:
        rgba(5, 6, 7, 0.85);

    font-size: 20px;

    line-height: 1;

    cursor: pointer;

    transition:
        color var(--transition),
        background var(--transition),
        border-color var(--transition);
}

.modal-close:hover {
    color: var(--orange);

    border-color: var(--border-orange);

    background:
        rgba(255, 107, 0, 0.05);
}

/* ================================================================ */
/* Modal Transition */
/* ================================================================ */

.request-modal-enter-active,
.request-modal-leave-active {
    transition:
        opacity 180ms ease;
}

.request-modal-enter-active
.request-modal-inner,
.request-modal-leave-active
.request-modal-inner {
    transition:
        transform 180ms ease;
}

.request-modal-enter-from,
.request-modal-leave-to {
    opacity: 0;
}

.request-modal-enter-from
.request-modal-inner,
.request-modal-leave-to
.request-modal-inner {
    transform:
        translateY(15px)
        scale(0.98);
}

/* ================================================================ */
/* Responsive */
/* ================================================================ */

@media (max-width: 900px) {
    .project-layout {
        grid-template-columns: 1fr;

        gap: 32px;
    }

    .project-info {
        position: static;
    }

    .project-title {
        max-width: 700px;
    }
}

@media (max-width: 650px) {
    .project-page {
        overflow-x: hidden;
    }

    .project-header {
        padding: 14px 0;
    }

    .project-main {
        padding-top: 22px;
        padding-bottom: 70px;
    }

    .breadcrumb {
        margin-bottom: 20px;
    }

    .details-grid {
        grid-template-columns: 1fr;
    }

    .project-actions {
        flex-direction: column;
    }

    .primary-action,
    .secondary-action {
        width: 100%;
    }

    .request-modal {
        padding: 10px;

        align-items: flex-start;
    }

    .request-modal-inner {
        margin-top: 15px;

        padding-top: 0;
    }

    .modal-close {
        top: 10px;
        right: 10px;
    }
}

@media (max-width: 480px) {
    .project-header-inner {
        gap: 12px;
    }

    .project-logo {
        font-size: 16px;
    }

    .back-link {
        font-size: 9px;
    }

    .project-title {
        font-size: 35px;
    }

    .project-short,
    .project-description {
        font-size: 11px;
    }

    .main-image {
        border-radius: 12px;
    }

    .thumbnail {
        flex-basis: 72px;

        height: 50px;
    }

    .request-modal {
        padding: 8px;
    }
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>
