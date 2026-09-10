<script setup>
import { computed, onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'

import {
    ArrowLeft,
    ChevronLeft,
    ChevronRight,
    Code2,
    ExternalLink,
    FolderCode,
    Search,
    ShoppingBag,
    Sparkles,
} from 'lucide-vue-next'

import { useProjectsStore } from '../stores/projects'


/*
|--------------------------------------------------------------------------
| Store
|--------------------------------------------------------------------------
*/

const projectsStore = useProjectsStore()

const {
    projects,
    loading,
    error,
    pagination,
} = storeToRefs(projectsStore)



/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const activeFilter = ref('all')

const searchQuery = ref('')



/*
|--------------------------------------------------------------------------
| Fetch Projects
|--------------------------------------------------------------------------
*/

const loadProjects = async (page = 1) => {

    await projectsStore.fetchProjects({
        page,
        per_page: 12,
    })

}



onMounted(() => {

    loadProjects()

})



/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

const getProjectImage = (project) => {

    return project?.images?.[0]?.url ?? null

}



const getProjectAlt = (project) => {

    return (
        project?.images?.[0]?.alt ??
        project?.title ??
        'تصویر پروژه'
    )

}



const getProjectTags = (project) => {

    const tags = []


    tags.push(
        project?.is_for_sale
            ? 'قابل فروش'
            : 'اختصاصی'
    )


    if (project?.status) {

        tags.push(project.status)

    }


    if (project?.is_featured) {

        tags.push('منتخب')

    }


    return tags

}



const formatPrice = (project) => {

    if (!project?.is_for_sale) {

        return 'پروژه اختصاصی'

    }


    if (
        project?.price === null ||
        project?.price === undefined ||
        project?.price === ''
    ) {

        return 'قیمت توافقی'

    }


    const price = Number(project.price)


    if (Number.isNaN(price)) {

        return 'قیمت توافقی'

    }


    return `${price.toLocaleString('fa-IR')} ${project.currency ?? ''}`.trim()

}



/*
|--------------------------------------------------------------------------
| Filtering
|--------------------------------------------------------------------------
*/

const filteredProjects = computed(() => {


    let result = Array.isArray(projects.value)
        ? [...projects.value]
        : []



    switch (activeFilter.value) {


        case 'sale':

            result = result.filter(
                project => project?.is_for_sale
            )

            break



        case 'custom':

            result = result.filter(
                project => !project?.is_for_sale
            )

            break



        case 'featured':

            result = result.filter(
                project => project?.is_featured
            )

            break


    }



    const query =
        searchQuery.value
            .trim()
            .toLowerCase()



    if (query) {


        result = result.filter(project => {


            const title =
                project?.title
                    ?.toLowerCase() ?? ''



            const description =
                project?.short_description
                    ?.toLowerCase() ?? ''



            return (
                title.includes(query) ||
                description.includes(query)
            )


        })


    }



    return result


})



/*
|--------------------------------------------------------------------------
| Statistics
|--------------------------------------------------------------------------
*/

const totalProjects = computed(() => {


    return (
        pagination.value?.total ??
        projects.value?.length ??
        0
    )


})



const saleProjects = computed(() => {


    return (
        projects.value?.filter(
            project => project?.is_for_sale
        ).length ?? 0
    )


})



/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

const changePage = (page) => {


    const current =
        pagination.value?.currentPage ?? 1


    const last =
        pagination.value?.lastPage ?? 1



    if (
        page < 1 ||
        page > last ||
        page === current ||
        loading.value
    ) {

        return

    }


    loadProjects(page)

}




const visiblePages = computed(() => {


    const current =
        pagination.value?.currentPage ?? 1


    const last =
        pagination.value?.lastPage ?? 1



    if (last <= 7) {


        return Array.from(
            {
                length: last
            },
            (_, index) => index + 1
        )


    }



    const pages = new Set([

        1,

        last,

        current,

        current - 1,

        current + 1,

    ])



    return Array.from(pages)

        .filter(
            page =>
                page >= 1 &&
                page <= last
        )

        .sort(
            (a,b) => a - b
        )


})



/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filters = [

    {
        id: 'all',
        label: 'همه پروژه‌ها',
    },

    {
        id: 'sale',
        label: 'قابل فروش',
    },

    {
        id: 'custom',
        label: 'پروژه اختصاصی',
    },

    {
        id: 'featured',
        label: 'منتخب',
    },

]



const setFilter = (filter) => {

    activeFilter.value = filter

}

</script>
<template>

<main
    class="projects-page"
    aria-labelledby="projects-page-title"
>


<!-- ======================================================
     HERO
====================================================== -->

<section
    class="projects-hero"
    aria-labelledby="projects-page-title"
>

    <div class="hero-glow hero-glow-one"></div>
    <div class="hero-glow hero-glow-two"></div>


    <div class="container hero-container">


        <div class="hero-copy">


            <span class="hero-kicker">
                PROJECT ARCHIVE
            </span>



            <h1
                id="projects-page-title"
                class="hero-title"
            >

                پروژه‌هایی که با
                <span>
                    عشق
                </span>
                ساخته‌ایم

            </h1>



            <p class="hero-description">

                مجموعه‌ای از پروژه‌های طراحی و توسعه داده‌شده
                توسط Derin Code؛ از وب‌سایت‌ها و فروشگاه‌ها
                تا سیستم‌های اختصاصی و محصولات دیجیتال.

            </p>


        </div>



        <div
            class="hero-stats"
            aria-label="آمار پروژه‌ها"
        >


            <div class="stat-item">


                <div class="stat-icon">

                    <FolderCode
                        :size="20"
                        aria-hidden="true"
                    />

                </div>


                <strong>
                    {{ totalProjects }}+
                </strong>


                <span>
                    پروژه توسعه داده شده
                </span>


            </div>




            <div class="stat-divider"></div>




            <div class="stat-item">


                <div class="stat-icon">

                    <ShoppingBag
                        :size="20"
                        aria-hidden="true"
                    />

                </div>


                <strong>
                    {{ saleProjects }}
                </strong>


                <span>
                    پروژه قابل فروش
                </span>


            </div>




            <div class="stat-divider"></div>




            <div class="stat-item">


                <div class="stat-icon">

                    <Code2
                        :size="20"
                        aria-hidden="true"
                    />

                </div>


                <strong>
                    Laravel
                </strong>


                <span>
                    Backend اصلی
                </span>


            </div>


        </div>


    </div>


</section>





<!-- ======================================================
     PROJECT CONTENT
====================================================== -->


<section
    class="projects-content"
    aria-labelledby="projects-list-title"
>


<div class="container">



<!-- Toolbar -->


<div class="projects-toolbar">


<div
    class="filter-list"
    role="tablist"
>

<button
    v-for="filter in filters"
    :key="filter.id"
    type="button"
    class="filter-button"
    :class="{
        active: activeFilter === filter.id
    }"
    @click="setFilter(filter.id)"
>

    {{ filter.label }}

</button>


</div>





<label
    class="search-box"
    for="project-search"
>


<Search
    :size="15"
/>


<input
    id="project-search"
    v-model="searchQuery"
    type="search"
    placeholder="جستجوی پروژه..."
/>


</label>


</div>





<header class="projects-list-header">


<div>

<span class="section-kicker">
    DERINCODE / WORK
</span>


<h2 id="projects-list-title">
    پروژه‌های ما
</h2>


</div>




<span
    v-if="!loading"
    class="project-count"
>

{{ filteredProjects.length }}
پروژه

</span>


</header>
            <div
                v-if="loading"
                class="projects-grid"
                role="status"
                aria-live="polite"
                aria-label="در حال دریافت پروژه‌ها"
            >

                <article
                    v-for="item in 8"
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


            <!-- ================================================= -->
            <!-- Error -->
            <!-- ================================================= -->

            <div
                v-else-if="error"
                class="projects-state"
                role="alert"
            >

                <div class="state-icon">
                    <FolderCode
                        :size="26"
                        aria-hidden="true"
                    />
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
                    @click="loadProjects()"
                >
                    تلاش مجدد
                </button>

            </div>



            <!-- ================================================= -->
            <!-- Empty -->
            <!-- ================================================= -->

            <div
                v-else-if="!filteredProjects.length"
                class="projects-state"
            >

                <div class="state-icon">

                    <FolderCode
                        :size="26"
                        aria-hidden="true"
                    />

                </div>


                <h3>
                    پروژه‌ای پیدا نشد
                </h3>


                <p>
                    فیلتر یا عبارت جستجو را تغییر دهید.
                </p>


                <button
                    type="button"
                    class="retry-button"
                    @click="
                        activeFilter = 'all';
                        searchQuery = ''
                    "
                >

                    نمایش همه پروژه‌ها

                </button>

            </div>



            <!-- ================================================= -->
            <!-- Projects Grid -->
            <!-- ================================================= -->

            <div
                v-else
                class="projects-grid"
            >


                <article
                    v-for="project in filteredProjects"
                    :key="project.id"
                    class="project-card"
                >


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
                        >

                            <FolderCode
                                :size="38"
                                aria-hidden="true"
                            />

                        </div>



                        <div
                            class="project-image-overlay"
                            aria-hidden="true"
                        ></div>



                        <span
                            v-if="project.is_for_sale"
                            class="sale-badge"
                        >

                            قابل فروش

                        </span>



                        <RouterLink
                            :to="`/projects/${project.slug}`"
                            class="project-open"
                            :aria-label="
                                `مشاهده جزئیات پروژه ${project.title}`
                            "
                        >

                            <ExternalLink
                                :size="16"
                                aria-hidden="true"
                            />

                        </RouterLink>


                    </div>



                    <div class="project-content">


                        <div class="project-meta">


                            <span class="project-category">

                                {{
                                    project.is_for_sale
                                        ? 'پروژه قابل فروش'
                                        : 'پروژه اختصاصی'
                                }}

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

                            {{
                                project.short_description ||
                                'توضیحاتی برای این پروژه ثبت نشده است.'
                            }}

                        </p>



                        <div class="project-tags">

                            <span
                                v-for="tag in getProjectTags(project)"
                                :key="tag"
                            >

                                {{ tag }}

                            </span>

                        </div>



                        <div class="project-bottom">


                            <div class="project-price">

                                {{ formatPrice(project) }}

                            </div>



                            <RouterLink
                                :to="`/projects/${project.slug}`"
                                class="project-link"
                            >

                                <span>
                                    مشاهده
                                </span>


                                <ArrowLeft
                                    :size="15"
                                    aria-hidden="true"
                                />

                            </RouterLink>


                        </div>


                    </div>


                </article>


            </div>
            <!-- ================================================= -->
            <!-- Pagination -->
            <!-- ================================================= -->

            <nav
                v-if="
                    !loading &&
                    !error &&
                    pagination.lastPage > 1
                "
                class="pagination"
                aria-label="صفحه‌بندی پروژه‌ها"
            >


                <button
                    type="button"
                    class="pagination-button arrow"
                    :disabled="
                        pagination.currentPage === 1
                    "
                    aria-label="صفحه قبلی"
                    @click="
                        changePage(
                            pagination.currentPage - 1
                        )
                    "
                >

                    <ChevronRight
                        :size="16"
                        aria-hidden="true"
                    />

                </button>



                <button
                    v-for="page in visiblePages"
                    :key="page"
                    type="button"
                    class="pagination-button"
                    :class="{
                        active:
                            page === pagination.currentPage
                    }"
                    :aria-current="
                        page === pagination.currentPage
                            ? 'page'
                            : undefined
                    "
                    @click="changePage(page)"
                >

                    {{ page }}

                </button>



                <button
                    type="button"
                    class="pagination-button arrow"
                    :disabled="
                        pagination.currentPage ===
                        pagination.lastPage
                    "
                    aria-label="صفحه بعدی"
                    @click="
                        changePage(
                            pagination.currentPage + 1
                        )
                    "
                >

                    <ChevronLeft
                        :size="16"
                        aria-hidden="true"
                    />

                </button>


            </nav>


        </div>

    </section>



    <!-- ====================================================== -->
    <!-- TECHNOLOGY -->
    <!-- ====================================================== -->


    <section
        class="technology-section"
        aria-labelledby="technology-title"
    >

        <div class="container technology-container">


            <div class="technology-copy">


                <span class="section-kicker">
                    TECHNOLOGY STACK
                </span>


                <h2 id="technology-title">

                    با بهترین ابزارها
                    <span>
                        کار می‌کنیم
                    </span>

                </h2>


                <p>

                    از تکنولوژی‌های مدرن و ابزارهای قابل اعتماد
                    برای ساخت پروژه‌های سریع، امن و قابل توسعه استفاده می‌کنیم.

                </p>


            </div>



            <div class="technology-list">


                <div class="technology-item">

                    <i class="devicon-laravel-plain colored"></i>

                    <strong>
                        Laravel
                    </strong>

                </div>



                <div class="technology-item">

                    <i class="devicon-vuejs-plain colored"></i>

                    <strong>
                        Vue.js
                    </strong>

                </div>



                <div class="technology-item">

                    <i class="devicon-react-original colored"></i>

                    <strong>
                        React
                    </strong>

                </div>



                <div class="technology-item">

                    <i class="devicon-nodejs-plain colored"></i>

                    <strong>
                        Node.js
                    </strong>

                </div>



                <div class="technology-item">

                    <i class="devicon-docker-plain colored"></i>

                    <strong>
                        Docker
                    </strong>

                </div>



                <div class="technology-item">

                    <i class="devicon-mysql-plain colored"></i>

                    <strong>
                        MySQL
                    </strong>

                </div>


            </div>


        </div>


    </section>





    <!-- ====================================================== -->
    <!-- CTA -->
    <!-- ====================================================== -->


    <section
        class="projects-cta"
        aria-labelledby="projects-cta-title"
    >


        <div class="container">


            <div class="cta-inner">


                <div class="cta-icon">

                    <Sparkles
                        :size="24"
                        aria-hidden="true"
                    />

                </div>



                <div class="cta-copy">


                    <span>
                        PROJECT WITH US
                    </span>


                    <h2 id="projects-cta-title">

                        پروژه‌ای دارید؟

                    </h2>


                    <p>

                        ایده خودتان را با ما مطرح کنید
                        تا با هم آن را به یک محصول واقعی تبدیل کنیم.

                    </p>


                </div>



                <RouterLink
                    to="/request-project"
                    class="cta-button"
                >

                    شروع همکاری


                    <ArrowLeft
                        :size="16"
                        aria-hidden="true"
                    />


                </RouterLink>



            </div>


        </div>


    </section>


</main>


</template>
<style scoped>

/* ========================================================= */
/* PAGE */
/* ========================================================= */


.projects-page {

    overflow:hidden;

    background:var(--bg-primary);

    color:var(--text-primary);

}





/* ========================================================= */
/* HERO */
/* ========================================================= */


.projects-hero {


    position:relative;

    overflow:hidden;


    padding:70px 0 62px;



    border-bottom:

        1px solid rgba(255,255,255,.055);



    background:


        radial-gradient(

            circle at 82% 35%,

            rgba(255,107,0,.12),

            transparent 30%

        ),


        radial-gradient(

            circle at 8% 85%,

            rgba(255,107,0,.06),

            transparent 32%

        ),


        linear-gradient(

            135deg,

            #090b0d,

            #0b0d10 55%,

            #090b0d

        );


}




.projects-hero::before {


    content:"";


    position:absolute;


    inset:0;



    opacity:.3;



    background-image:


        linear-gradient(

            rgba(255,255,255,.025) 1px,

            transparent 1px

        ),


        linear-gradient(

            90deg,

            rgba(255,255,255,.025) 1px,

            transparent 1px

        );



    background-size:55px 55px;



    mask-image:

        linear-gradient(

            to bottom,

            black,

            transparent

        );



    pointer-events:none;


}




.hero-glow {


    position:absolute;


    width:420px;


    height:420px;


    border-radius:50%;



    background:

        rgba(255,107,0,.08);



    filter:blur(90px);


}




.hero-glow-one {


    top:-260px;


    right:-120px;


}



.hero-glow-two {


    bottom:-300px;


    left:-150px;


}




.hero-container {


    position:relative;


    z-index:1;



    display:grid;



    grid-template-columns:


        minmax(0,1.2fr)

        minmax(360px,.8fr);



    align-items:center;



    gap:70px;


}





.hero-copy {


    max-width:700px;


}





.hero-kicker {


    display:inline-block;


    color:var(--orange);



    font-family:var(--font-mono);



    font-size:10px;


    font-weight:700;



    letter-spacing:.08em;


}





.hero-title {


    max-width:650px;



    margin-top:14px;



    color:var(--text-primary);



    font-size:

        clamp(34px,4.4vw,54px);



    font-weight:900;



    line-height:1.35;



    letter-spacing:-.04em;


}





.hero-title span {


    color:var(--orange);


}





.hero-description {


    max-width:590px;



    margin-top:15px;



    color:var(--text-muted);



    font-size:13px;



    line-height:2.2;


}





/* ========================================================= */
/* STATS */
/* ========================================================= */


.hero-stats {


    display:grid;



    grid-template-columns:


        1fr auto 1fr auto 1fr;



    align-items:center;



    min-height:145px;



    padding:20px;



    border:


        1px solid rgba(255,255,255,.075);



    border-radius:15px;



    background:


        rgba(7,9,11,.78);



    backdrop-filter:blur(14px);



    box-shadow:


        0 25px 70px rgba(0,0,0,.25);


}





.stat-item {


    display:flex;



    flex-direction:column;



    align-items:center;



    text-align:center;


}





.stat-icon {


    width:38px;


    height:38px;



    display:flex;



    align-items:center;



    justify-content:center;



    color:var(--orange);



    border:


        1px solid rgba(255,107,0,.18);



    border-radius:9px;



    background:


        rgba(255,107,0,.035);


}





.stat-item strong {


    margin-top:9px;



    color:var(--text-primary);



    font-family:var(--font-mono);



    font-size:17px;



    font-weight:800;


}





.stat-item span {


    margin-top:4px;



    color:var(--text-muted);



    font-size:9px;



    line-height:1.7;


}





.stat-divider {


    width:1px;


    height:65px;



    background:


        rgba(255,255,255,.07);


}
/* ========================================================= */
/* PROJECT CONTENT */
/* ========================================================= */


.projects-content {

    padding:55px 0 85px;

}




.projects-toolbar {


    display:flex;


    align-items:center;


    justify-content:space-between;



    gap:20px;



    margin-bottom:38px;


}





.filter-list {


    display:flex;


    align-items:center;



    flex-wrap:wrap;



    gap:7px;


}





.filter-button {


    min-height:36px;



    padding:0 14px;



    color:var(--text-muted);



    border:


        1px solid rgba(255,255,255,.075);



    border-radius:8px;



    background:


        rgba(255,255,255,.012);



    font-family:inherit;



    font-size:10px;



    font-weight:650;



    cursor:pointer;



    transition:.3s;


}





.filter-button:hover {


    color:var(--text-primary);



    border-color:


        rgba(255,107,0,.25);


}





.filter-button.active {


    color:#111;



    border-color:var(--orange);



    background:var(--orange);


}





.search-box {


    width:220px;



    height:38px;



    flex-shrink:0;



    display:flex;



    align-items:center;



    gap:8px;



    padding:0 12px;



    color:var(--text-muted);



    border:


        1px solid rgba(255,255,255,.075);



    border-radius:9px;



    background:


        rgba(255,255,255,.018);


}





.search-box:focus-within {


    border-color:


        rgba(255,107,0,.35);



    box-shadow:


        0 0 0 3px rgba(255,107,0,.05);


}





.search-box input {


    width:100%;



    border:0;



    outline:0;



    color:var(--text-primary);



    background:transparent;



    font-family:inherit;



    font-size:10px;


}





.search-box input::placeholder {


    color:var(--text-muted);


}





.projects-list-header {


    display:flex;



    align-items:flex-end;



    justify-content:space-between;



    margin-bottom:20px;


}





.section-kicker {


    color:var(--orange);



    font-family:var(--font-mono);



    font-size:9px;



    font-weight:700;



    letter-spacing:.05em;


}





.projects-list-header h2 {


    margin-top:8px;



    color:var(--text-primary);



    font-size:25px;



    font-weight:850;


}





.project-count {


    color:var(--text-muted);



    font-size:10px;


}





/* ========================================================= */
/* GRID */
/* ========================================================= */


.projects-grid {


    display:grid;



    grid-template-columns:


        repeat(4,minmax(0,1fr));



    gap:13px;


}





.project-card {


    overflow:hidden;



    border:


        1px solid rgba(255,255,255,.075);



    border-radius:11px;



    background:


        linear-gradient(

            145deg,

            rgba(255,255,255,.025),

            rgba(255,255,255,.007)

        );



    transition:.3s;


}





.project-card:hover {


    transform:translateY(-5px);



    border-color:


        rgba(255,107,0,.28);



    box-shadow:


        0 22px 50px rgba(0,0,0,.24);


}





/* ========================================================= */
/* IMAGE */
/* ========================================================= */


.project-image {


    position:relative;



    overflow:hidden;



    aspect-ratio:16 / 10;



    background:#090b0d;


}





.project-image img {


    width:100%;



    height:100%;



    display:block;



    object-fit:cover;



    transition:.5s;


}





.project-card:hover .project-image img {


    transform:scale(1.045);



    filter:brightness(.88);


}





.project-image-overlay {


    position:absolute;



    inset:0;



    background:


        linear-gradient(

            180deg,

            transparent 42%,

            rgba(0,0,0,.62)

        );


}





.project-image-placeholder {


    width:100%;



    height:100%;



    display:flex;



    align-items:center;



    justify-content:center;



    color:var(--orange);



    background:


        radial-gradient(

            circle,

            rgba(255,107,0,.09),

            transparent 60%

        );


}





.sale-badge {


    position:absolute;



    top:10px;



    right:10px;



    padding:5px 8px;



    color:#111;



    border-radius:5px;



    background:var(--orange);



    font-size:8px;



    font-weight:800;


}





.project-open {


    position:absolute;



    left:10px;



    bottom:10px;



    width:33px;



    height:33px;



    display:flex;



    align-items:center;



    justify-content:center;



    color:var(--orange);



    border:


        1px solid rgba(255,107,0,.35);



    border-radius:7px;



    background:


        rgba(5,6,7,.78);



    opacity:0;



    transform:translateY(5px);



    transition:.3s;


}





.project-card:hover .project-open {


    opacity:1;



    transform:translateY(0);


}
/* ========================================================= */
/* TECHNOLOGY */
/* ========================================================= */


.technology-section {

    padding: 0 0 80px;

}



.technology-container {

    display: grid;

    grid-template-columns:
        minmax(260px, .75fr)
        minmax(0, 1.25fr);

    align-items: center;

    gap: 45px;

    padding: 35px;


    border:

        1px solid rgba(255,255,255,.07);


    border-radius:18px;


    background:

        linear-gradient(
            145deg,
            rgba(255,255,255,.035),
            rgba(255,255,255,.01)
        );


    box-shadow:

        0 25px 70px rgba(0,0,0,.25);

}





.technology-copy h2 {

    margin-top:12px;

    color:var(--text-primary);

    font-size:
        clamp(26px,3vw,34px);

    font-weight:900;

    line-height:1.4;

}




.technology-copy h2 span {

    color:var(--orange);

}




.technology-copy p {

    max-width:420px;

    margin-top:14px;

    color:var(--text-muted);

    font-size:12px;

    line-height:2.2;

}




/* Technology Grid */


.technology-list {

    display:grid;

    grid-template-columns:
        repeat(3,minmax(0,1fr));

    gap:12px;

}




.technology-item {


    min-height:85px;


    display:flex;

    align-items:center;


    gap:12px;


    padding:15px;


    border-radius:12px;


    border:

        1px solid rgba(255,255,255,.07);


    background:

        rgba(255,255,255,.018);



    transition:

        transform .3s ease,
        border-color .3s ease,
        background .3s ease;


}





.technology-item:hover {


    transform:translateY(-4px);


    border-color:

        rgba(255,107,0,.35);



    background:

        rgba(255,107,0,.04);


}




.technology-item i {


    width:42px;

    height:42px;


    display:flex;


    align-items:center;

    justify-content:center;


    font-size:34px;


}




.technology-item strong {


    color:var(--text-primary);


    font-size:12px;


}





/* ========================================================= */
/* CTA */
/* ========================================================= */



.projects-cta {

    padding:

        0 0 90px;

}





.cta-inner {


    position:relative;


    overflow:hidden;


    display:flex;


    align-items:center;


    justify-content:space-between;


    gap:30px;


    padding:35px 40px;



    border-radius:18px;



    border:

        1px solid rgba(255,107,0,.2);



    background:


        radial-gradient(

            circle at 90% 50%,

            rgba(255,107,0,.18),

            transparent 35%

        ),


        linear-gradient(

            135deg,

            #111,

            #090909

        );



}





.cta-inner::before {


    content:"";


    position:absolute;


    inset:0;



    background-image:


        linear-gradient(

            rgba(255,255,255,.03) 1px,

            transparent 1px

        ),


        linear-gradient(

            90deg,

            rgba(255,255,255,.03) 1px,

            transparent 1px

        );



    background-size:40px 40px;


    opacity:.25;


}





.cta-icon {


    position:relative;


    z-index:1;


    width:55px;


    height:55px;


    flex-shrink:0;


    display:flex;


    align-items:center;


    justify-content:center;



    color:var(--orange);



    border-radius:14px;



    border:

        1px solid rgba(255,107,0,.3);



    background:

        rgba(255,107,0,.08);


}





.cta-copy {


    position:relative;


    z-index:1;


    flex:1;


}





.cta-copy span {


    color:var(--orange);


    font-family:var(--font-mono);


    font-size:10px;


    font-weight:700;


}





.cta-copy h2 {


    margin-top:8px;


    color:white;


    font-size:28px;


    font-weight:900;


}





.cta-copy p {


    margin-top:8px;


    color:var(--text-muted);


    font-size:12px;


    line-height:2;


}





.cta-button {


    position:relative;


    z-index:1;



    display:flex;


    align-items:center;


    gap:8px;



    min-height:45px;



    padding:0 22px;



    border-radius:9px;



    color:#111;



    background:var(--orange);



    font-size:12px;



    font-weight:800;



    text-decoration:none;



    transition:.3s;


}




.cta-button:hover {


    transform:translateX(-4px);


    background:var(--orange-light);


}






/* ========================================================= */
/* Responsive */
/* ========================================================= */



@media(max-width:900px){


    .hero-container {

        grid-template-columns:1fr;

        gap:35px;

    }



    .projects-grid {

        grid-template-columns:

            repeat(2,minmax(0,1fr));

    }



    .technology-container {

        grid-template-columns:1fr;

    }



    .technology-list {

        grid-template-columns:

            repeat(2,1fr);

    }



    .cta-inner {

        flex-direction:column;

        align-items:flex-start;

    }



}





@media(max-width:550px){


    .projects-grid {

        grid-template-columns:1fr;

    }



    .projects-toolbar {

        flex-direction:column;

        align-items:stretch;

    }



    .search-box {

        width:100%;

    }



    .hero-stats {

        grid-template-columns:1fr;

        gap:20px;

    }



    .stat-divider {

        width:100%;

        height:1px;

    }



    .technology-list {

        grid-template-columns:1fr;

    }



    .cta-inner {

        padding:25px;

    }



    .cta-copy h2 {

        font-size:23px;

    }


}
/* ========================================================= */
/* CARD CONTENT */
/* ========================================================= */


.project-content {


    padding:14px 13px 13px;


}




.project-meta {


    display:flex;



    align-items:center;



    flex-wrap:wrap;



    gap:5px;


}





.project-category,
.project-status {


    min-height:21px;



    display:inline-flex;



    align-items:center;



    padding:0 6px;



    border:


        1px solid rgba(255,255,255,.07);



    border-radius:4px;



    color:var(--text-muted);



    font-size:8px;



    font-weight:600;


}





.project-category {


    color:var(--orange);



    border-color:


        rgba(255,107,0,.15);



    background:


        rgba(255,107,0,.025);


}





.project-title {


    margin-top:10px;



    color:var(--text-primary);



    font-size:14px;



    font-weight:800;



    line-height:1.7;


}





.project-description {


    display:-webkit-box;



    min-height:42px;



    margin-top:5px;



    overflow:hidden;



    color:var(--text-muted);



    font-size:10px;



    line-height:2;



    -webkit-line-clamp:2;



    -webkit-box-orient:vertical;


}





.project-tags {


    display:flex;



    flex-wrap:wrap;



    gap:5px;



    min-height:22px;



    margin-top:10px;


}





.project-tags span {


    padding:3px 6px;



    color:var(--text-muted);



    border:


        1px solid rgba(255,255,255,.06);



    border-radius:4px;



    font-size:8px;


}





.project-bottom {


    display:flex;



    align-items:center;



    justify-content:space-between;



    gap:10px;



    margin-top:13px;



    padding-top:11px;



    border-top:


        1px solid rgba(255,255,255,.055);


}





.project-price {


    max-width:55%;



    overflow:hidden;



    color:var(--text-secondary);



    font-size:9px;



    font-weight:700;



    white-space:nowrap;



    text-overflow:ellipsis;


}





.project-link {


    min-height:29px;



    display:inline-flex;



    align-items:center;



    gap:5px;



    padding:0 8px;



    color:var(--orange);



    border:


        1px solid rgba(255,107,0,.18);



    border-radius:6px;



    background:


        rgba(255,107,0,.025);



    font-size:9px;



    font-weight:700;



    text-decoration:none;



    transition:.3s;


}





.project-link:hover {


    color:#111;



    background:var(--orange);



    transform:translateX(-2px);


}






/* ========================================================= */
/* SKELETON */
/* ========================================================= */


.skeleton-card {


    pointer-events:none;


}





.skeleton-image,
.skeleton-content span {


    position:relative;



    overflow:hidden;



    background:


        rgba(255,255,255,.035);


}





.skeleton-image::after,
.skeleton-content span::after {


    content:"";



    position:absolute;



    inset:0;



    transform:translateX(-100%);



    background:


        linear-gradient(

            90deg,

            transparent,

            rgba(255,255,255,.045),

            transparent

        );



    animation:skeleton 1.3s infinite;


}





.skeleton-image {


    aspect-ratio:16 / 10;


}





.skeleton-content {


    padding:15px 13px;


}





.skeleton-content span {


    display:block;



    height:10px;



    margin-bottom:9px;



    border-radius:4px;


}





.skeleton-content span:nth-child(1){

    width:34%;

}


.skeleton-content span:nth-child(2){

    width:76%;

}


.skeleton-content span:nth-child(3){

    width:52%;

}





@keyframes skeleton {


    100%{

        transform:translateX(100%);

    }


}






/* ========================================================= */
/* STATES */
/* ========================================================= */


.projects-state {


    min-height:280px;



    padding:45px 20px;



    display:flex;



    flex-direction:column;



    align-items:center;



    justify-content:center;



    text-align:center;



    border:


        1px dashed rgba(255,255,255,.1);



    border-radius:14px;



    background:


        rgba(255,255,255,.012);


}





.state-icon {


    width:55px;



    height:55px;



    display:flex;



    align-items:center;



    justify-content:center;



    color:var(--orange);



    border:


        1px solid rgba(255,107,0,.2);



    border-radius:13px;



    background:


        rgba(255,107,0,.035);


}





.projects-state h3 {


    margin-top:16px;



    color:var(--text-primary);



    font-size:15px;



    font-weight:750;


}





.projects-state p {


    max-width:420px;



    margin-top:7px;



    color:var(--text-muted);



    font-size:11px;



    line-height:1.9;


}





.retry-button {


    min-height:38px;



    margin-top:17px;



    padding:0 17px;



    color:var(--text-primary);



    border:


        1px solid var(--border-orange);



    border-radius:7px;



    background:


        rgba(255,107,0,.05);



    font-family:inherit;



    font-size:10px;



    font-weight:700;



    cursor:pointer;



    transition:.3s;


}





.retry-button:hover {


    color:#111;



    background:var(--orange);


}
/* ========================================================= */
/* PAGINATION */
/* ========================================================= */


.pagination {


    display:flex;



    align-items:center;



    justify-content:center;



    gap:6px;



    margin-top:35px;


}





.pagination-button {


    width:34px;



    height:34px;



    display:flex;



    align-items:center;



    justify-content:center;



    color:var(--text-muted);



    border:


        1px solid rgba(255,255,255,.075);



    border-radius:7px;



    background:


        rgba(255,255,255,.012);



    font-family:inherit;



    font-size:9px;



    font-weight:700;



    cursor:pointer;



    transition:.3s;


}





.pagination-button:hover:not(:disabled){


    color:var(--text-primary);



    border-color:


        rgba(255,107,0,.25);


}





.pagination-button.active {


    color:#111;



    border-color:var(--orange);



    background:var(--orange);


}





.pagination-button:disabled {


    opacity:.35;



    cursor:not-allowed;


}





.pagination-button:focus-visible,
.filter-button:focus-visible,
.project-link:focus-visible {


    outline:2px solid var(--orange);



    outline-offset:3px;


}





/* ========================================================= */
/* RESPONSIVE */
/* ========================================================= */


@media(max-width:1100px){


    .hero-container {


        grid-template-columns:1fr;



        gap:45px;


    }



    .hero-copy {


        max-width:100%;


    }



    .projects-grid {


        grid-template-columns:


            repeat(3,minmax(0,1fr));


    }


}





@media(max-width:900px){


    .projects-toolbar {


        flex-direction:column;



        align-items:stretch;


    }



    .search-box {


        width:100%;


    }



    .projects-grid {


        grid-template-columns:


            repeat(2,minmax(0,1fr));


    }



    .hero-stats {


        grid-template-columns:1fr;



        gap:20px;


    }



    .stat-divider {


        width:100%;



        height:1px;


    }



}





@media(max-width:550px){


    .projects-hero {


        padding:45px 0;


    }



    .hero-title {


        font-size:32px;


    }



    .projects-content {


        padding:40px 0 60px;


    }



    .projects-grid {


        grid-template-columns:1fr;


    }



    .filter-list {


        width:100%;


    }



    .filter-button {


        flex:1;


    }



    .projects-list-header {


        align-items:flex-start;



        flex-direction:column;


    }



    .project-bottom {


        flex-direction:column;



        align-items:flex-start;


    }



    .project-price {


        max-width:100%;


    }



    .pagination-button {


        width:32px;



        height:32px;


    }


}


</style>
