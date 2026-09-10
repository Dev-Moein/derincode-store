<script setup>
import { onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import {
    ArrowLeft,
    ExternalLink,
    FolderCode,
} from 'lucide-vue-next'

import { useProjectsStore } from '../../stores/projects'

const projectsStore = useProjectsStore()

const {
    projects,
    loading,
    error,
} = storeToRefs(projectsStore)

onMounted(() => {
    projectsStore.fetchFeaturedProjects()
})

const getProjectImage = (project) => {
    return project?.images?.[0]?.url || null
}

const getProjectAlt = (project) => {
    return (
        project?.images?.[0]?.alt ||
        project?.title ||
        'تصویر پروژه'
    )
}

const getProjectTags = (project) => {
    const tags = []

    if (project?.is_for_sale) {
        tags.push('قابل فروش')
    }

    if (project?.status) {
        tags.push(project.status)
    }

    return tags
}
</script>

<template>
    <section
        id="projects"
        class="projects-section section"
        aria-labelledby="projects-title"
    >
        <div class="container">

            <!-- Heading -->
            <header class="projects-header">
                <div>
                    <span class="section-kicker">
                        نمونه‌کارهای منتخب
                    </span>

                    <h2
                        id="projects-title"
                        class="projects-title"
                    >
                        پروژه‌هایی که
                        <span>ساخته‌ایم</span>
                    </h2>

                    <p class="projects-description">
                        بخشی از پروژه‌های طراحی و توسعه داده‌شده توسط
                        تیم Derin Code.
                    </p>
                </div>

                <RouterLink
    to="/projects"
    class="all-projects-link"
>
    <span>مشاهده همه نمونه‌کارها</span>
    <ArrowLeft :size="16" aria-hidden="true" />
</RouterLink>
            </header>

            <!-- Loading -->
            <div
                v-if="loading"
                class="projects-grid"
                role="status"
                aria-live="polite"
                aria-label="در حال دریافت پروژه‌ها"
            >
                <article
                    v-for="item in 4"
                    :key="item"
                    class="project-card skeleton-card"
                    aria-hidden="true"
                >
                    <div class="skeleton-image"></div>

                    <div class="skeleton-content">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </article>
            </div>

            <!-- Error -->
            <div
                v-else-if="error"
                class="projects-state"
                role="alert"
            >
                <div
                    class="state-icon"
                    aria-hidden="true"
                >
                    <FolderCode :size="25" />
                </div>

                <h3>
                    دریافت پروژه‌ها انجام نشد
                </h3>

                <p>
                    {{ error }}
                </p>

                <button
                    type="button"
                    class="retry-button"
                    @click="projectsStore.fetchFeaturedProjects()"
                >
                    تلاش مجدد
                </button>
            </div>

            <!-- Empty -->
            <div
                v-else-if="!projects.length"
                class="projects-state"
            >
                <div
                    class="state-icon"
                    aria-hidden="true"
                >
                    <FolderCode :size="25" />
                </div>

                <h3>
                    هنوز پروژه‌ای برای نمایش وجود ندارد
                </h3>

                <p>
                    به‌زودی نمونه‌کارهای جدید اینجا نمایش داده می‌شوند.
                </p>
            </div>

            <!-- Projects -->
            <div
                v-else
                class="projects-grid"
            >
                <article
                    v-for="project in projects"
                    :key="project.id"
                    class="project-card"
                >
                    <!-- Image -->
                    <div class="project-image">
                        <img
                            v-if="getProjectImage(project)"
                            :src="getProjectImage(project)"
                            :alt="getProjectAlt(project)"
                            loading="lazy"
                            decoding="async"
                        />

                        <div
                            v-else
                            class="project-image-placeholder"
                            aria-hidden="true"
                        >
                            <FolderCode :size="36" />
                        </div>

                        <div
                            class="project-image-overlay"
                            aria-hidden="true"
                        ></div>

                        <RouterLink
                            :to="`/projects/${project.slug}`"
                            class="project-open"
                            :aria-label="`مشاهده جزئیات پروژه ${project.title}`"
                        >
                            <ExternalLink
                                :size="16"
                                aria-hidden="true"
                            />
                        </RouterLink>
                    </div>

                    <!-- Content -->
                    <div class="project-content">
                        <div class="project-meta">
                            <span class="project-category">
                                {{ project.is_for_sale ? 'قابل فروش' : 'پروژه اختصاصی' }}
                            </span>

                            <span
                                v-if="project.status"
                                class="project-status"
                            >
                                {{ project.status }}
                            </span>
                        </div>

                        <h3 class="project-title">
                            {{ project.title }}
                        </h3>

                        <p class="project-description">
                            {{ project.short_description }}
                        </p>

                        <div class="project-bottom">
                            <div
                                v-if="getProjectTags(project).length"
                                class="project-tags"
                                aria-label="برچسب‌های پروژه"
                            >
                                <span
                                    v-for="tag in getProjectTags(project)"
                                    :key="tag"
                                >
                                    {{ tag }}
                                </span>
                            </div>

                            <RouterLink
                                :to="`/projects/${project.slug}`"
                                class="project-link"
                                :aria-label="`مشاهده جزئیات پروژه ${project.title}`"
                            >
                                <ArrowLeft
                                    :size="16"
                                    aria-hidden="true"
                                />
                            </RouterLink>
                        </div>
                    </div>
                </article>
            </div>

        </div>
    </section>
</template>

<style scoped>
.projects-section {
    padding-top: 45px;
    padding-bottom: 90px;
}

/* -------------------------------- */
/* Header */
/* -------------------------------- */

.projects-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 30px;

    margin-bottom: 34px;
}

.section-kicker {
    position: relative;

    display: inline-block;

    padding-bottom: 10px;

    color: var(--text-primary);

    font-size: 20px;
    font-weight: 800;
    line-height: 1.6;
}

.section-kicker::after {
    content: "";

    position: absolute;

    right: 0;
    bottom: 0;

    width: 34px;
    height: 3px;

    border-radius: 999px;

    background: var(--orange);
}

.projects-title {
    margin-top: 16px;

    color: var(--text-primary);

    font-size: clamp(30px, 3.5vw, 42px);
    font-weight: 850;
    line-height: 1.35;

    letter-spacing: -0.035em;
}

.projects-title span {
    color: var(--orange);
}

.projects-description {
    max-width: 500px;

    margin-top: 9px;

    color: var(--text-muted);

    font-size: 13px;
    line-height: 2.15;
}

.all-projects-link {
    display: inline-flex;
    align-items: center;
    gap: 7px;

    flex-shrink: 0;

    min-height: 40px;

    padding: 0 2px;

    color: var(--orange);

    font-size: 12px;
    font-weight: 700;
    line-height: 1.7;

    text-decoration: none;

    transition:
        color var(--transition),
        transform var(--transition);
}

.all-projects-link:hover {
    color: var(--orange-light);
    transform: translateX(-3px);
}

.all-projects-link:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 5px;
    border-radius: 4px;
}

/* -------------------------------- */
/* Grid */
/* -------------------------------- */

.projects-grid {
    display: grid;

    grid-template-columns: repeat(4, minmax(0, 1fr));

    gap: 12px;
}

.project-card {
    overflow: hidden;

    border: 1px solid rgba(255, 255, 255, 0.075);
    border-radius: 10px;

    background:
        linear-gradient(
            145deg,
            rgba(255, 255, 255, 0.028),
            rgba(255, 255, 255, 0.008)
        );

    transition:
        transform var(--transition),
        border-color var(--transition),
        background var(--transition),
        box-shadow var(--transition);
}

.project-card:hover {
    transform: translateY(-5px);

    border-color: rgba(255, 107, 0, 0.3);

    background:
        linear-gradient(
            145deg,
            rgba(255, 107, 0, 0.035),
            rgba(255, 255, 255, 0.008)
        );

    box-shadow:
        0 20px 48px rgba(0, 0, 0, 0.25);
}

/* -------------------------------- */
/* Image */
/* -------------------------------- */

.project-image {
    position: relative;

    overflow: hidden;

    aspect-ratio: 16 / 10;

    background: #090b0d;
}

.project-image img {
    width: 100%;
    height: 100%;

    display: block;

    object-fit: cover;

    transition:
        transform 500ms ease,
        filter 500ms ease;
}

.project-card:hover .project-image img {
    transform: scale(1.04);

    filter: brightness(0.88);
}

.project-image-overlay {
    position: absolute;
    inset: 0;

    pointer-events: none;

    background:
        linear-gradient(
            180deg,
            transparent 50%,
            rgba(0, 0, 0, 0.5) 100%
        );
}

.project-image-placeholder {
    width: 100%;
    height: 100%;

    display: flex;
    align-items: center;
    justify-content: center;

    color: var(--orange);

    background:
        radial-gradient(
            circle,
            rgba(255, 107, 0, 0.08),
            transparent 60%
        );
}

/* -------------------------------- */
/* Image action */
/* -------------------------------- */

.project-open {
    position: absolute;

    left: 10px;
    bottom: 10px;

    width: 34px;
    height: 34px;

    display: flex;
    align-items: center;
    justify-content: center;

    color: var(--orange);

    border: 1px solid rgba(255, 107, 0, 0.35);
    border-radius: 7px;

    background: rgba(5, 6, 7, 0.76);

    backdrop-filter: blur(8px);

    opacity: 0;

    transform: translateY(5px);

    transition:
        opacity var(--transition),
        transform var(--transition),
        background var(--transition),
        color var(--transition);
}

.project-card:hover .project-open,
.project-open:focus-visible {
    opacity: 1;
    transform: translateY(0);
}

.project-open:hover {
    color: #111;
    background: var(--orange);
}

.project-open:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 3px;
}

/* -------------------------------- */
/* Content */
/* -------------------------------- */

.project-content {
    padding: 15px 14px 13px;
}

.project-meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 7px;
}

.project-category,
.project-status {
    display: inline-flex;
    align-items: center;

    min-height: 22px;

    padding: 0 7px;

    border: 1px solid rgba(255, 255, 255, 0.07);
    border-radius: 4px;

    color: var(--text-muted);

    font-size: 9px;
    font-weight: 600;
    line-height: 1.6;
}

.project-category {
    color: var(--orange);

    border-color: rgba(255, 107, 0, 0.15);

    background: rgba(255, 107, 0, 0.025);
}

.project-title {
    margin-top: 11px;

    color: var(--text-primary);

    font-size: 14px;
    font-weight: 800;
    line-height: 1.7;
}

.project-description {
    display: -webkit-box;

    margin-top: 6px;

    overflow: hidden;

    color: var(--text-muted);

    font-size: 11px;
    line-height: 2;

    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.project-bottom {
    min-height: 30px;

    margin-top: 14px;

    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}

.project-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.project-tags span {
    padding: 3px 6px;

    color: var(--text-muted);

    border: 1px solid rgba(255, 255, 255, 0.06);
    border-radius: 4px;

    font-size: 9px;
    font-weight: 500;
    line-height: 1.5;
}

.project-link {
    width: 30px;
    height: 30px;

    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    color: var(--orange);

    border: 1px solid rgba(255, 107, 0, 0.2);
    border-radius: 7px;

    background: rgba(255, 107, 0, 0.025);

    transition:
        color var(--transition),
        background var(--transition),
        transform var(--transition);
}

.project-link:hover {
    color: #111;

    background: var(--orange);

    transform: translateX(-2px);
}

.project-link:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 3px;
}

/* -------------------------------- */
/* Loading */
/* -------------------------------- */

.skeleton-card {
    pointer-events: none;
}

.skeleton-image,
.skeleton-content span {
    position: relative;

    overflow: hidden;

    background: rgba(255, 255, 255, 0.035);
}

.skeleton-image::after,
.skeleton-content span::after {
    content: "";

    position: absolute;
    inset: 0;

    transform: translateX(-100%);

    background:
        linear-gradient(
            90deg,
            transparent,
            rgba(255, 255, 255, 0.045),
            transparent
        );

    animation: skeleton 1.3s infinite;
}

.skeleton-image {
    aspect-ratio: 16 / 10;
}

.skeleton-content {
    padding: 15px 14px;
}

.skeleton-content span {
    display: block;

    height: 11px;

    margin-bottom: 9px;

    border-radius: 4px;
}

.skeleton-content span:nth-child(1) {
    width: 35%;
}

.skeleton-content span:nth-child(2) {
    width: 75%;
}

.skeleton-content span:nth-child(3) {
    width: 52%;
}

@keyframes skeleton {
    100% {
        transform: translateX(100%);
    }
}

/* -------------------------------- */
/* State */
/* -------------------------------- */

.projects-state {
    min-height: 260px;

    padding: 40px 20px;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    text-align: center;

    border: 1px dashed rgba(255, 255, 255, 0.1);
    border-radius: 14px;

    background: rgba(255, 255, 255, 0.012);
}

.state-icon {
    width: 52px;
    height: 52px;

    display: flex;
    align-items: center;
    justify-content: center;

    color: var(--orange);

    border: 1px solid rgba(255, 107, 0, 0.2);
    border-radius: 13px;

    background: rgba(255, 107, 0, 0.035);
}

.projects-state h3 {
    margin-top: 16px;

    color: var(--text-primary);

    font-size: 15px;
    font-weight: 750;
    line-height: 1.7;
}

.projects-state p {
    max-width: 420px;

    margin-top: 7px;

    color: var(--text-muted);

    font-size: 11px;
    line-height: 1.9;
}

.retry-button {
    min-height: 38px;

    margin-top: 16px;
    padding: 0 17px;

    color: var(--text-primary);

    border: 1px solid var(--border-orange);
    border-radius: 7px;

    background: rgba(255, 107, 0, 0.05);

    font-family: inherit;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.7;

    cursor: pointer;

    transition:
        color var(--transition),
        background var(--transition),
        transform var(--transition);
}

.retry-button:hover {
    color: #111;
    background: var(--orange);
}

.retry-button:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 3px;
}

.retry-button:active {
    transform: translateY(1px);
}

/* -------------------------------- */
/* Responsive */
/* -------------------------------- */

@media (max-width: 1100px) {
    .projects-grid {
        gap: 10px;
    }

    .project-content {
        padding: 14px 12px 12px;
    }

    .project-description {
        font-size: 10px;
    }
}

@media (max-width: 1000px) {
    .projects-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .project-content {
        padding: 15px 14px 13px;
    }

    .project-description {
        font-size: 11px;
    }
}

@media (max-width: 700px) {
    .projects-section {
        padding-top: 35px;
    }

    .projects-header {
        align-items: flex-start;
        flex-direction: column;
        gap: 17px;
    }

    .projects-title {
        font-size: clamp(30px, 7vw, 36px);
    }

    .projects-description {
        font-size: 12px;
    }

    .all-projects-link {
        font-size: 11px;
    }

    .projects-grid {
        gap: 9px;
    }
}

@media (max-width: 560px) {
    .projects-grid {
        grid-template-columns: 1fr;
    }

    .project-image {
        aspect-ratio: 16 / 9;
    }

    .project-content {
        padding: 15px 14px 13px;
    }

    .project-title {
        font-size: 15px;
    }

    .project-description {
        font-size: 11px;
    }

    .project-tags span {
        font-size: 9px;
    }
}

@media (max-width: 400px) {
    .projects-title {
        font-size: 29px;
    }

    .projects-description {
        font-size: 11px;
    }

    .projects-grid {
        gap: 8px;
    }
}

/* -------------------------------- */
/* Reduced motion */
/* -------------------------------- */

@media (prefers-reduced-motion: reduce) {
    .project-card,
    .project-image img,
    .project-open,
    .project-link,
    .all-projects-link,
    .retry-button {
        transition: none;
    }

    .skeleton-image::after,
    .skeleton-content span::after {
        animation: none;
    }
}
</style>
