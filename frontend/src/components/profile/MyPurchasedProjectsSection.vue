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

const formatAmount = (
    amount,
    currency
) => {
    if (
        amount === null ||
        amount === undefined
    ) {
        return '-'
    }

    try {
        return `${new Intl.NumberFormat(
            'en-US'
        ).format(Number(amount))} ${
            currency || ''
        }`.trim()
    } catch {
        return `${amount} ${
            currency || ''
        }`.trim()
    }
}

const getImageUrl = (project) => {
    return project?.images?.[0]?.url || null
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

        const blob = new Blob(
            [response.data],
            {
                type:
                    response.headers?.[
                        'content-type'
                    ] ||
                    'application/zip',
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
    <section class="purchased-section">

        <div class="section-header">
            <div>
                <span class="section-kicker">
                    PURCHASED PROJECTS
                </span>

                <h2>
                    پروژه‌های خریداری‌شده
                </h2>

                <p>
                    پروژه‌هایی که خریداری کرده‌اید
                    و می‌توانید دانلود کنید.
                </p>
            </div>

            <div class="project-count">
                {{ projects.length }}
                <span>پروژه</span>
            </div>
        </div>

        <div
            v-if="downloadError"
            class="download-error"
        >
            <XCircle :size="15" />

            <span>
                {{ downloadError }}
            </span>
        </div>

        <div
            v-if="!projects.length"
            class="state"
        >
            <FolderArchive :size="25" />

            <h3>
                هنوز پروژه‌ای خریداری نکرده‌اید
            </h3>

            <p>
                بعد از خرید پروژه، آن را در این بخش
                مشاهده و دانلود خواهید کرد.
            </p>
        </div>

        <div
            v-else
            class="projects-list"
        >
            <article
                v-for="project in projects"
                :key="project.payment_id"
                class="project-card"
            >
                <div class="project-image">
                    <img
                        v-if="getImageUrl(project)"
                        :src="getImageUrl(project)"
                        :alt="project.title"
                    />

                    <ImageOff
                        v-else
                        :size="20"
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

                    <strong>
                        {{
                            formatAmount(
                                project.price,
                                project.currency
                            )
                        }}
                    </strong>

                    <small>
                        خرید:
                        {{
                            formatDate(
                                project.purchased_at
                            )
                        }}
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
                        />

                        <Download
                            v-else
                            :size="14"
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

.project-count {
    padding: 7px 10px;

    color: var(--orange);

    border: 1px solid rgba(255, 107, 0, 0.17);
    border-radius: 7px;

    background: rgba(255, 107, 0, 0.035);

    font-size: 10px;
}

.project-count span {
    color: var(--text-muted);

    font-size: 8px;
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

    background:
        rgba(255, 255, 255, 0.012);

    transition:
        border-color 0.2s ease,
        background 0.2s ease;
}

.project-card:hover {
    border-color: var(--border-orange);

    background:
        rgba(255, 255, 255, 0.018);
}

.project-image {
    width: 55px;
    height: 55px;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: hidden;

    color: var(--text-muted);

    border: 1px solid var(--border);

    border-radius: 7px;

    background:
        rgba(255, 255, 255, 0.025);
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

    font-size: 6px;
}

.project-main h3 {
    margin-top: 3px;

    overflow: hidden;

    color: var(--text-primary);

    font-size: 11px;

    text-overflow: ellipsis;
    white-space: nowrap;
}

.project-main p {
    margin-top: 3px;

    overflow: hidden;

    color: var(--text-muted);

    font-size: 7px;

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

    font-size: 7px;
}

.project-info strong {
    color: var(--text-secondary);

    font-size: 9px;
}

.project-action {
    display: flex;

    align-items: center;
    justify-content: flex-end;
}

.project-action button {
    min-height: 30px;

    padding-inline: 11px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 6px;

    color: var(--text-primary);

    border: 1px solid
        rgba(255, 107, 0, 0.25);

    border-radius: 7px;

    background: var(--orange-soft);

    font-size: 8px;
    font-weight: 700;

    cursor: pointer;

    transition:
        border-color 0.2s ease,
        background 0.2s ease,
        opacity 0.2s ease;
}

.project-action button:hover:not(:disabled) {
    border-color: var(--orange);

    background:
        rgba(255, 107, 0, 0.09);
}

.project-action button:disabled {
    opacity: 0.55;

    cursor: not-allowed;
}

.not-downloadable {
    color: var(--text-muted);

    font-size: 7px;
}

.download-error {
    margin-bottom: 8px;

    padding: 9px 11px;

    display: flex;
    align-items: center;

    gap: 7px;

    color: var(--danger);

    border: 1px solid
        rgba(255, 92, 92, 0.15);

    border-radius: 7px;

    background:
        rgba(255, 92, 92, 0.025);

    font-size: 8px;
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

    background:
        rgba(255, 255, 255, 0.01);

    text-align: center;

    font-size: 9px;
}

.state h3 {
    color: var(--text-primary);

    font-size: 11px;
}

.state p {
    max-width: 380px;

    font-size: 8px;
}

.loader {
    animation:
        spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
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

