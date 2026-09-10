<script setup>
import { computed, ref } from 'vue'
import { storeToRefs } from 'pinia'
import {
    Download,
    FolderArchive,
    ImageOff,
    RefreshCw,
    XCircle,
} from 'lucide-vue-next'

import { useAuthStore } from '../../stores/auth'
import api from '../../services/api'

const authStore = useAuthStore()

const {
    purchasedProjects,
} = storeToRefs(authStore)

const downloadingProjectId = ref(null)
const downloadError = ref('')

const projects = computed(() => {
    return purchasedProjects.value || []
})

const formatDate = (date) => {
    if (!date) {
        return '-'
    }

    const parsed = new Date(date)

    if (Number.isNaN(parsed.getTime())) {
        return '-'
    }

    return new Intl.DateTimeFormat(
        'fa-IR',
        {
            dateStyle: 'medium',
        }
    ).format(parsed)
}

const formatAmount = (amount, currency) => {
    if (
        amount === null ||
        amount === undefined ||
        amount === ''
    ) {
        return '-'
    }

    const numericAmount = Number(amount)

    if (Number.isNaN(numericAmount)) {
        return `${amount} ${currency || ''}`.trim()
    }

    return `${new Intl.NumberFormat('en-US').format(
        numericAmount
    )} ${currency || ''}`.trim()
}

const getImageUrl = (project) => {
    return project?.images?.[0]?.url || null
}

const getImageAlt = (project) => {
    return (
        project?.images?.[0]?.alt ||
        project?.title ||
        'تصویر پروژه'
    )
}

const getProjectKey = (project) => {
    return (
        project?.payment_id ||
        project?.id
    )
}

const downloadProject = async (project) => {
    if (
        !project?.id ||
        downloadingProjectId.value !== null
    ) {
        return
    }

    downloadError.value = ''
    downloadingProjectId.value = project.id

    try {
        const response = await api.get(
            `/downloads/projects/${project.id}`,
            {
                responseType: 'blob',
            }
        )

        const contentType =
            response.headers?.['content-type'] ||
            'application/zip'

        const blob = new Blob(
            [response.data],
            {
                type: contentType,
            }
        )

        const url =
            window.URL.createObjectURL(blob)

        const link =
            document.createElement('a')

        link.href = url

        link.download =
            project.file?.name ||
            'project.zip'

        document.body.appendChild(link)

        link.click()

        link.remove()

        window.URL.revokeObjectURL(url)
    } catch (error) {
        downloadError.value =
            error?.response?.data?.message ||
            'دانلود پروژه انجام نشد.'
    } finally {
        downloadingProjectId.value = null
    }
}
</script>

<template>
    <section
        class="purchased-section"
        aria-labelledby="purchased-projects-title"
    >
        <header class="section-header">
            <div>
                <span class="section-kicker">
                    PURCHASED PROJECTS
                </span>

                <h2 id="purchased-projects-title">
                    پروژه‌های خریداری‌شده
                </h2>

                <p>
                    پروژه‌هایی که خریداری کرده‌اید
                    و می‌توانید دانلود کنید.
                </p>
            </div>

            <div
                class="project-count"
                aria-label="تعداد پروژه‌های خریداری‌شده"
            >
                {{ projects.length }}
                <span>پروژه</span>
            </div>
        </header>

        <div
            v-if="downloadError"
            class="download-error"
            role="alert"
            aria-live="assertive"
        >
            <XCircle
                :size="15"
                aria-hidden="true"
            />

            <span>
                {{ downloadError }}
            </span>
        </div>

        <!-- Empty state -->
        <div
            v-if="!projects.length"
            class="state"
        >
            <FolderArchive
                :size="25"
                aria-hidden="true"
            />

            <h3>
                هنوز پروژه‌ای خریداری نکرده‌اید
            </h3>

            <p>
                بعد از خرید پروژه، آن را در این بخش
                مشاهده و دانلود خواهید کرد.
            </p>
        </div>

        <!-- Projects -->
        <div
            v-else
            class="projects-list"
        >
            <article
                v-for="project in projects"
                :key="getProjectKey(project)"
                class="project-card"
            >
                <div class="project-image">
                    <img
                        v-if="getImageUrl(project)"
                        :src="getImageUrl(project)"
                        :alt="getImageAlt(project)"
                        loading="lazy"
                        decoding="async"
                    />

                    <ImageOff
                        v-else
                        :size="20"
                        aria-hidden="true"
                    />
                </div>

                <div class="project-main">
                    <span class="project-label">
                        PURCHASED PROJECT
                    </span>

                    <h3>
                        {{ project.title }}
                    </h3>

                    <p>
                        {{
                            project.short_description ||
                            'بدون توضیحات'
                        }}
                    </p>
                </div>

                <div class="project-info">
                    <span>
                        مبلغ
                    </span>

                    <strong dir="ltr">
                        {{
                            formatAmount(
                                project.price,
                                project.currency
                            )
                        }}
                    </strong>

                    <small>
                        خرید:
                        <time
                            :datetime="
                                project.purchased_at ||
                                undefined
                            "
                        >
                            {{
                                formatDate(
                                    project.purchased_at
                                )
                            }}
                        </time>
                    </small>
                </div>

                <div class="project-action">
                    <button
                        v-if="project.downloadable"
                        type="button"
                        :disabled="
                            downloadingProjectId ===
                            project.id
                        "
                        :aria-busy="
                            downloadingProjectId ===
                            project.id
                        "
                        :aria-label="
                            downloadingProjectId ===
                            project.id
                                ? `در حال دانلود ${project.title}`
                                : `دانلود ${project.title}`
                        "
                        @click="
                            downloadProject(project)
                        "
                    >
                        <RefreshCw
                            v-if="
                                downloadingProjectId ===
                                project.id
                            "
                            :size="14"
                            class="loader"
                            aria-hidden="true"
                        />

                        <Download
                            v-else
                            :size="14"
                            aria-hidden="true"
                        />

                        <span>
                            {{
                                downloadingProjectId ===
                                project.id
                                    ? 'در حال دانلود...'
                                    : 'دانلود'
                            }}
                        </span>
                    </button>

                    <span
                        v-else
                        class="not-downloadable"
                    >
                        فایل موجود نیست
                    </span>
                </div>
            </article>
        </div>
    </section>
</template>

<style scoped>
.purchased-section {
    margin-top: 25px;
}

.section-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;

    margin-bottom: 15px;
}

.section-kicker {
    color: var(--orange);

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 9px;
    font-weight: 600;
}

.section-header h2 {
    margin-top: 6px;

    color: var(--text-primary);

    font-family: inherit;

    font-size: 20px;
    font-weight: 850;
}

.section-header p {
    margin-top: 5px;

    color: var(--text-muted);

    font-family: inherit;

    font-size: 11px;
    line-height: 1.7;
}

.project-count {
    padding: 7px 10px;

    color: var(--orange);

    border: 1px solid rgba(255, 107, 0, 0.17);
    border-radius: 7px;

    background: rgba(255, 107, 0, 0.035);

    font-size: 11px;
    font-weight: 700;
}

.project-count span {
    margin-inline-start: 3px;

    color: var(--text-muted);

    font-size: 9px;
    font-weight: 400;
}

.projects-list {
    display: flex;
    flex-direction: column;

    gap: 8px;
}

.project-card {
    min-height: 86px;

    padding: 10px;

    display: grid;

    grid-template-columns:
        55px
        minmax(0, 1.5fr)
        0.8fr
        auto;

    align-items: center;

    gap: 12px;

    border: 1px solid var(--border);

    border-radius: 9px;

    background: rgba(255, 255, 255, 0.012);

    transition:
        border-color var(--transition),
        background var(--transition);
}

.project-card:hover {
    border-color: var(--border-orange);

    background: rgba(255, 255, 255, 0.018);
}

.project-image {
    width: 55px;
    height: 55px;

    display: flex;
    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    overflow: hidden;

    color: var(--text-muted);

    border: 1px solid var(--border);

    border-radius: 7px;

    background: rgba(255, 255, 255, 0.025);
}

.project-image img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;
}

.project-main {
    min-width: 0;
}

.project-label {
    color: #5f656a;

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 7px;
    font-weight: 600;
}

.project-main h3 {
    margin-top: 3px;

    overflow: hidden;

    color: var(--text-primary);

    font-family: inherit;

    font-size: 13px;
    font-weight: 750;

    text-overflow: ellipsis;
    white-space: nowrap;
}

.project-main p {
    margin-top: 3px;

    overflow: hidden;

    color: var(--text-muted);

    font-family: inherit;

    font-size: 10px;
    line-height: 1.6;

    text-overflow: ellipsis;
    white-space: nowrap;
}

.project-info {
    display: flex;
    flex-direction: column;

    align-items: flex-end;

    gap: 2px;
}

.project-info span,
.project-info small {
    color: var(--text-muted);

    font-family: inherit;

    font-size: 9px;
}

.project-info strong {
    color: var(--text-secondary);

    font-family: inherit;

    font-size: 10px;
    font-weight: 700;
}

.project-action {
    display: flex;

    align-items: center;
    justify-content: flex-end;
}

.project-action button {
    min-height: 32px;

    padding-inline: 11px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 6px;

    color: var(--text-primary);

    border: 1px solid rgba(255, 107, 0, 0.25);

    border-radius: 7px;

    background: var(--orange-soft);

    font-family: inherit;

    font-size: 10px;
    font-weight: 700;

    cursor: pointer;

    transition:
        border-color var(--transition),
        background var(--transition),
        opacity var(--transition);
}

.project-action button:hover:not(:disabled) {
    border-color: var(--orange);

    background: rgba(255, 107, 0, 0.09);
}

.project-action button:focus-visible {
    outline: 2px solid var(--orange);

    outline-offset: 3px;
}

.project-action button:disabled {
    opacity: 0.55;

    cursor: wait;
}

.not-downloadable {
    color: var(--text-muted);

    font-family: inherit;

    font-size: 9px;
}

.download-error {
    margin-bottom: 8px;

    padding: 9px 11px;

    display: flex;
    align-items: center;

    gap: 7px;

    color: var(--danger);

    border: 1px solid rgba(255, 92, 92, 0.15);

    border-radius: 7px;

    background: rgba(255, 92, 92, 0.025);

    font-family: inherit;

    font-size: 10px;
    line-height: 1.7;
}

.state {
    min-height: 160px;

    display: flex;
    flex-direction: column;

    align-items: center;
    justify-content: center;

    gap: 8px;

    color: var(--text-muted);

    border: 1px solid var(--border);

    border-radius: 11px;

    background: rgba(255, 255, 255, 0.01);

    text-align: center;

    font-family: inherit;

    font-size: 10px;
}

.state h3 {
    color: var(--text-primary);

    font-family: inherit;

    font-size: 13px;
    font-weight: 750;
}

.state p {
    max-width: 380px;

    font-size: 10px;
    line-height: 1.7;
}

.loader {
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@media (prefers-reduced-motion: reduce) {
    .project-card,
    .project-action button,
    .loader {
        transition: none;
        animation: none;
    }
}

@media (max-width: 800px) {
    .project-card {
        grid-template-columns:
            55px
            minmax(0, 1fr)
            auto;
    }

    .project-info {
        grid-column: 2;

        align-items: flex-start;
    }

    .project-action {
        grid-column: 3;

        grid-row: 1 / span 2;
    }
}

@media (max-width: 520px) {
    .section-header {
        align-items: flex-start;

        flex-direction: column;

        gap: 12px;
    }

    .project-card {
        grid-template-columns: 50px 1fr;
    }

    .project-image {
        width: 50px;
        height: 50px;
    }

    .project-main {
        grid-column: 2;
    }

    .project-info {
        grid-column: 1 / -1;

        padding-top: 4px;

        align-items: flex-start;
    }

    .project-action {
        grid-column: 1 / -1;

        grid-row: auto;

        justify-content: stretch;
    }

    .project-action button {
        width: 100%;
    }
}
</style>
