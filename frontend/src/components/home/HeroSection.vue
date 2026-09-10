<script setup>
import {
    ArrowLeft,
    ArrowUpLeft,
    ShieldCheck,
    Gauge,
    Code2,
    Headphones,
} from 'lucide-vue-next'

import { useRouter } from 'vue-router'


const router = useRouter()


const goToSection = async (hash) => {

    await router.push({
        name: 'home',
        hash,
    })

}

</script>


<template>

<section class="hero">


    <!-- ========================================================= -->
    <!-- Background -->
    <!-- ========================================================= -->

    <div class="hero-background">

        <img
            src="../../assets/images/hero.jpg"
            alt=""
            aria-hidden="true"
        />

        <div class="hero-overlay"></div>

        <div class="hero-text-gradient"></div>

    </div>



    <!-- ========================================================= -->
    <!-- Main Content -->
    <!-- ========================================================= -->

    <div class="container hero-container">

        <div class="hero-content">


            <div class="hero-title">

                <h1>

                    ایده‌ات را به یک

                    <span>
                        محصول واقعی
                    </span>

                    تبدیل می‌کنیم.

                </h1>


                <p>

                    ما در DerinCode طراحی، توسعه وب‌سایت،
                    فروشگاه‌های آنلاین و سیستم‌های اختصاصی را
                    با تمرکز بر عملکرد، امنیت و تجربه کاربری
                    توسعه می‌دهیم.

                </p>

            </div>



            <!-- ================================================= -->
            <!-- Actions -->
            <!-- ================================================= -->

            <div class="hero-actions">


                <button
                    type="button"
                    class="hero-btn hero-btn-primary"
                    @click="
                        goToSection('#contact')
                    "
                >

                    <span>
                        شروع یک پروژه
                    </span>


                    <span class="btn-icon">

                        <ArrowLeft
                            :size="16"
                        />

                    </span>

                </button>



                <button
                    type="button"
                    class="hero-btn hero-btn-secondary"
                    @click="
                        goToSection('#projects')
                    "
                >

                    <span>
                        مشاهده نمونه‌کارها
                    </span>


                    <ArrowUpLeft
                        :size="15"
                        class="secondary-icon"
                    />

                </button>

            </div>



            <!-- ================================================= -->
            <!-- Technologies -->
            <!-- ================================================= -->

            <div class="technologies">


                <div class="technology-card">

                    <div
                        class="tech-logo laravel"
                    >
                        <Code2 :size="21" />
                    </div>

                    <span>
                        Laravel
                    </span>

                </div>



                <div class="technology-card">

                    <div
                        class="tech-logo vue"
                    >
                        <span>V</span>
                    </div>

                    <span>
                        Vue.js
                    </span>

                </div>



                <div class="technology-card">

                    <div
                        class="
                            tech-logo
                            javascript
                        "
                    >
                        JS
                    </div>

                    <span>
                        JavaScript
                    </span>

                </div>



                <div class="technology-card">

                    <div
                        class="
                            tech-logo
                            mysql
                        "
                    >
                        <span>
                            SQL
                        </span>
                    </div>

                    <span>
                        MySQL
                    </span>

                </div>



                <div class="technology-card">

                    <div
                        class="
                            tech-logo
                            docker
                        "
                    >
                        <span>◆</span>
                    </div>

                    <span>
                        Docker
                    </span>

                </div>


            </div>

        </div>

    </div>



    <!-- ========================================================= -->
    <!-- Benefits -->
    <!-- ========================================================= -->

    <div class="container hero-benefits">


        <div class="benefit">

            <div class="benefit-icon">
                <Gauge :size="25" />
            </div>


            <div class="benefit-content">

                <h3>
                    عملکرد بالا
                </h3>

                <p>
                    سریع، بهینه و قابل اعتماد
                </p>

            </div>

        </div>



        <div class="benefit">

            <div class="benefit-icon">

                <ShieldCheck
                    :size="25"
                />

            </div>


            <div class="benefit-content">

                <h3>
                    امنیت حرفه‌ای
                </h3>

                <p>
                    استفاده از بهترین روش‌های امنیت
                </p>

            </div>

        </div>



        <div class="benefit">

            <div class="benefit-icon">

                <Code2 :size="25" />

            </div>


            <div class="benefit-content">

                <h3>
                    کدنویسی تمیز
                </h3>

                <p>
                    کد استاندارد، تست‌پذیر و قابل نگهداری
                </p>

            </div>

        </div>



        <div class="benefit">

            <div class="benefit-icon">

                <Headphones
                    :size="25"
                />

            </div>


            <div class="benefit-content">

                <h3>
                    پشتیبانی و همراهی
                </h3>

                <p>
                    همیشه کنار شما در مسیر توسعه
                </p>

            </div>

        </div>


    </div>


</section>

</template>



<style scoped>

.hero {
    position: relative;

    min-height: 720px;

    display: flex;

    flex-direction: column;

    padding:
        55px 0 28px;

    overflow: hidden;
}



/* ========================================================= */
/* Background */
/* ========================================================= */

.hero-background {
    position: absolute;

    inset: 0;

    z-index: 0;

    overflow: hidden;

    background: #050607;
}



.hero-background img {
    position: absolute;

    inset: 0;

    width: 100%;

    height: 100%;

    object-fit: cover;

    object-position: center;

    opacity: 1;
}



.hero-overlay {
    position: absolute;

    inset: 0;

    background:
        linear-gradient(
            180deg,
            rgba(5,6,7,.15) 0%,
            rgba(5,6,7,.04) 40%,
            rgba(5,6,7,.52) 100%
        );
}



.hero-text-gradient {
    position: absolute;

    inset: 0;

    background:
        linear-gradient(
            90deg,
            rgba(5,6,7,.98) 0%,
            rgba(5,6,7,.92) 20%,
            rgba(5,6,7,.70) 38%,
            rgba(5,6,7,.20) 56%,
            rgba(5,6,7,0) 76%
        );
}



/* ========================================================= */
/* Main */
/* ========================================================= */

.hero-container {
    position: relative;

    z-index: 2;

    width:
        min(
            calc(100% - 40px),
            var(--container)
        );

    flex: 1;

    display: flex;

    align-items: center;

    direction: ltr;
}



.hero-content {
    width: 52%;

    max-width: 610px;

    padding-top: 15px;

    direction: rtl;
}



/* ========================================================= */
/* Typography */
/* ========================================================= */

.hero-title h1 {
    margin: 0;

    color: #f7f7f7;

    font-size:
        clamp(42px, 4.6vw, 62px);

    line-height: 1.23;

    font-weight: 900;

    letter-spacing: -.045em;

    text-shadow:
        0 3px 24px
        rgba(0,0,0,.35);
}



.hero-title h1 span {
    color: var(--orange);

    text-shadow:
        0 0 35px
        rgba(255,107,0,.17);
}



.hero-title p {
    max-width: 510px;

    margin-top: 23px;

    color: #b0b3b7;

    font-size: 14px;

    line-height: 2.25;

    text-shadow:
        0 2px 14px
        rgba(0,0,0,.45);
}



/* ========================================================= */
/* Buttons */
/* ========================================================= */

.hero-actions {
    display: flex;

    align-items: center;

    gap: 12px;

    margin-top: 28px;
}



.hero-btn {
    min-height: 45px;

    padding:
        0 13px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 10px;

    border-radius: 8px;

    font-family: inherit;

    font-size: 12px;

    font-weight: 700;

    cursor: pointer;

    transition:
        transform var(--transition),
        background var(--transition),
        border-color var(--transition),
        box-shadow var(--transition),
        color var(--transition);
}



.hero-btn:hover {
    transform:
        translateY(-2px);
}



.hero-btn-primary {
    padding-inline:
        11px 16px;

    color: #fff;

    border:
        1px solid
        transparent;

    background:
        linear-gradient(
            135deg,
            #ff770d,
            #ff5f00
        );

    box-shadow:
        0 10px 30px
        rgba(255,107,0,.2);
}



.hero-btn-primary:hover {
    box-shadow:
        0 14px 35px
        rgba(255,107,0,.3);
}



.btn-icon {
    width: 25px;

    height: 25px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 50%;

    background:
        rgba(255,255,255,.14);
}



.hero-btn-secondary {
    color: #f0f0f0;

    border:
        1px solid
        rgba(255,107,0,.5);

    background:
        rgba(0,0,0,.23);

    backdrop-filter: blur(5px);
}



.hero-btn-secondary:hover {
    border-color:
        var(--orange);

    background:
        rgba(255,107,0,.08);
}



.secondary-icon {
    color: var(--orange);
}



/* ========================================================= */
/* Technologies */
/* ========================================================= */

.technologies {
    display: flex;

    align-items: center;

    gap: 16px;

    margin-top: 30px;
}



.technology-card {
    width: 67px;

    min-height: 67px;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    gap: 5px;

    border:
        1px solid
        rgba(255,255,255,.065);

    border-radius: 9px;

    background:
        rgba(8,10,12,.72);

    backdrop-filter: blur(10px);

    box-shadow:
        0 8px 22px
        rgba(0,0,0,.2);

    transition:
        transform var(--transition),
        border-color var(--transition),
        background var(--transition);
}



.technology-card:hover {
    transform:
        translateY(-4px);

    border-color:
        var(--border-orange);

    background:
        rgba(255,107,0,.055);
}



.tech-logo {
    width: 27px;

    height: 27px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-family:
        var(--font-mono);

    font-size: 12px;

    font-weight: 900;
}



.technology-card > span {
    color: #a3a7ab;

    font-size: 8px;
}



.laravel {
    color: #ff453d;
}



.vue {
    color: #41b883;

    font-size: 21px;
}



.javascript {
    width: 27px;

    height: 27px;

    color: #111;

    background: #f7df1e;

    font-size: 8px;
}



.mysql {
    color: #4ea7d8;

    font-size: 7px;
}



.docker {
    color: #45b8eb;

    font-size: 18px;
}



/* ========================================================= */
/* Benefits */
/* ========================================================= */

.hero-benefits {
    position: relative;

    z-index: 3;

    margin-top: 20px;

    padding:
        18px 22px;

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    border:
        1px solid
        rgba(255,255,255,.085);

    border-radius: 15px;

    background:
        rgba(8,10,12,.75);

    backdrop-filter: blur(16px);

    box-shadow:
        0 18px 50px
        rgba(0,0,0,.28);
}



.benefit {
    min-height: 75px;

    padding-inline: 22px;

    display: flex;

    align-items: center;

    gap: 14px;

    border-left:
        1px solid
        rgba(255,255,255,.07);
}



.benefit:last-child {
    border-left: 0;
}



.benefit-icon {
    width: 42px;

    height: 42px;

    flex-shrink: 0;

    display: flex;

    align-items: center;

    justify-content: center;

    color: var(--orange);

    border:
        1px solid
        rgba(255,107,0,.25);

    border-radius: 11px;

    background:
        rgba(255,107,0,.03);
}



.benefit-content h3 {
    color: #f0f0f0;

    font-size: 12px;

    font-weight: 800;
}



.benefit-content p {
    margin-top: 3px;

    color: #777d82;

    font-size: 8px;

    line-height: 1.8;
}



/* ========================================================= */
/* Responsive */
/* ========================================================= */

@media (max-width: 1050px) {

    .hero {
        min-height: 690px;
    }


    .hero-content {
        width: 56%;
    }


    .hero-title h1 {
        font-size:
            clamp(38px,4.8vw,54px);
    }


    .technology-card {
        width: 61px;
    }


    .hero-benefits {
        padding-inline: 10px;
    }


    .benefit {
        padding-inline: 13px;
    }

}



@media (max-width: 820px) {

    .hero {
        min-height: auto;

        padding:
            42px 0 28px;
    }


    .hero-container {
        min-height: 580px;

        align-items: center;
    }


    .hero-content {
        width: 72%;
    }


    .hero-background img {
        object-position:
            68% center;
    }


    .hero-text-gradient {

        background:

            linear-gradient(
                90deg,
                rgba(5,6,7,.98) 0%,
                rgba(5,6,7,.92) 36%,
                rgba(5,6,7,.63) 68%,
                rgba(5,6,7,.22) 100%
            ),

            linear-gradient(
                180deg,
                rgba(5,6,7,.08),
                rgba(5,6,7,.5)
            );

    }


    .hero-benefits {
        grid-template-columns:
            repeat(2, 1fr);
    }


    .benefit {
        min-height: 76px;

        border-left: 0;

        border-bottom:
            1px solid
            rgba(255,255,255,.07);
    }


    .benefit:nth-child(odd) {
        border-left:
            1px solid
            rgba(255,255,255,.07);
    }


    .benefit:nth-child(3),
    .benefit:nth-child(4) {
        border-bottom: 0;
    }

}



@media (max-width: 600px) {

    .hero {
        padding-top: 30px;
    }


    .hero-container {

        min-height: 590px;

        width:
            min(
                calc(100% - 24px),
                var(--container)
            );

        align-items: flex-start;

        padding-top: 95px;
    }


    .hero-content {
        width: 100%;
    }


    .hero-background img {

        object-position:
            71% center;

        transform:
            scale(1.04);
    }


    .hero-text-gradient {

        background:

            linear-gradient(
                90deg,
                rgba(5,6,7,.99) 0%,
                rgba(5,6,7,.94) 45%,
                rgba(5,6,7,.72) 76%,
                rgba(5,6,7,.38) 100%
            ),

            linear-gradient(
                180deg,
                rgba(5,6,7,.2),
                rgba(5,6,7,.65)
            );

    }


    .hero-title h1 {

        font-size:
            clamp(34px,9.5vw,42px);

        line-height: 1.28;
    }


    .hero-title h1 span {
        white-space: normal;
    }


    .hero-title p {

        max-width: 100%;

        font-size: 12px;

        line-height: 2.1;
    }


    .hero-actions {

        flex-wrap: wrap;
    }


    .hero-btn {

        min-height: 43px;

        font-size: 10px;
    }


    .technologies {

        gap: 7px;

        flex-wrap: wrap;
    }


    .technology-card {

        width: 58px;

        min-height: 61px;
    }


    .hero-benefits {

        grid-template-columns: 1fr;

        margin-top: 12px;

        padding: 8px;
    }


    .benefit,
    .benefit:nth-child(odd) {

        padding:
            15px 12px;

        border-left: 0;

        border-bottom:
            1px solid
            rgba(255,255,255,.07);
    }


    .benefit:last-child {
        border-bottom: 0;
    }

}



@media (max-width: 380px) {

    .hero-container {

        min-height: 600px;

        padding-top: 82px;
    }


    .hero-title h1 {
        font-size: 32px;
    }


    .technology-card {
        width: 54px;
    }


    .benefit-content h3 {
        font-size: 11px;
    }

}

</style>
