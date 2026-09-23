<script setup>
import PublicLayout from '../layouts/PublicLayout.vue'
import {
    ArrowLeft,
    ArrowRight,
    ArrowDown,
    Code2,
    Database,
    Server,
    Palette,
    LayoutDashboard,
    ShoppingCart,
    Boxes,
    Rocket,
    ShieldCheck,
    Gauge,
    Users,
    Lightbulb,
    GitBranch,
    CheckCircle2,
    Layers3,
    Terminal,
    Cpu,
    Sparkles,
} from 'lucide-vue-next'

const services = [
    {
        title: 'توسعه وب‌سایت',
        description: 'طراحی و توسعه وب‌سایت‌های مدرن، سریع و واکنش‌گرا برای برندها و کسب‌وکارها.',
        icon: Code2,
    },
    {
        title: 'پنل‌های مدیریتی',
        description: 'ساخت داشبوردهای حرفه‌ای و قابل توسعه برای مدیریت کاربران، سفارش‌ها و داده‌ها.',
        icon: LayoutDashboard,
    },
    {
        title: 'سیستم‌های اختصاصی',
        description: 'توسعه راهکارهای سفارشی متناسب با فرآیند واقعی و نیازهای خاص هر کسب‌وکار.',
        icon: Boxes,
    },
    {
        title: 'فروشگاه آنلاین',
        description: 'ساخت تجربه خرید آنلاین با تمرکز روی عملکرد، امنیت و مدیریت ساده.',
        icon: ShoppingCart,
    },
]

const technologies = [
    { name: 'Laravel', icon: Terminal },
    { name: 'PHP', icon: Code2 },
    { name: 'Vue.js', icon: Cpu },
    { name: 'JavaScript', icon: Code2 },
    { name: 'MySQL', icon: Database },
    { name: 'Redis', icon: Server },
    { name: 'Docker', icon: Boxes },
    { name: 'Git', icon: GitBranch },
]

const principles = [
    {
        title: 'کدنویسی قابل نگهداری',
        description: 'ساختار پروژه باید طوری باشد که توسعه و تغییرات آینده ساده و قابل پیش‌بینی بماند.',
        icon: Layers3,
    },
    {
        title: 'سرعت بدون قربانی کردن کیفیت',
        description: 'تحویل به‌موقع مهم است، اما نه با هزینه ایجاد بدهی فنی و مشکلات آینده.',
        icon: Gauge,
    },
    {
        title: 'امنیت از ابتدا',
        description: 'امنیت را بخشی از معماری و توسعه می‌دانیم، نه کاری که در پایان پروژه انجام شود.',
        icon: ShieldCheck,
    },
    {
        title: 'تمرکز روی نیاز واقعی',
        description: 'قبل از ساخت، مسئله واقعی کسب‌وکار را می‌فهمیم و بعد برای آن راه‌حل طراحی می‌کنیم.',
        icon: Lightbulb,
    },
]

const processSteps = [
    { title: 'ایده', icon: Lightbulb },
    { title: 'تحلیل', icon: Sparkles },
    { title: 'طراحی', icon: Palette },
    { title: 'توسعه', icon: Code2 },
    { title: 'تست', icon: CheckCircle2 },
    { title: 'انتشار', icon: Rocket },
]

const scrollToSection = (id) => {
    document.getElementById(id)?.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
    })
}
</script>

<template>
    <PublicLayout>
        <main class="about-page">
            <!-- Hero -->
            <section class="about-hero section-shell">
                <div class="hero-grid">
                    <div class="hero-content">
                        <span class="eyebrow">درباره ما</span>

                        <h1>
                            ما
                            <span>Derin Code</span>
                            هستیم
                        </h1>

                        <p class="hero-lead">
                            یک تیم دو نفره برای طراحی و ساخت محصولات دیجیتال مدرن.
                        </p>

                        <p class="hero-text">
                            ما با ترکیب تجربه، طراحی و مهندسی نرم‌افزار تلاش می‌کنیم
                            ایده‌ها را به وب‌سایت‌ها، سیستم‌ها و محصولات واقعی و قابل استفاده تبدیل کنیم.
                        </p>

                        <div class="hero-actions">
                            <button
                                type="button"
                                class="primary-action"
                                @click="scrollToSection('team')"
                            >
                                <span>آشنایی با تیم</span>
                                <ArrowLeft :size="17" />
                            </button>

                            <button
                                type="button"
                                class="ghost-action"
                                @click="scrollToSection('story')"
                            >
                                <span>داستان ما</span>
                                <ArrowDown :size="16" />
                            </button>
                        </div>
                    </div>

                    <!-- Abstract visual — no personal photo -->
                    <div class="hero-visual" aria-hidden="true">
                        <div class="visual-orbit orbit-one"></div>
                        <div class="visual-orbit orbit-two"></div>
                        <div class="visual-node node-one"></div>
                        <div class="visual-node node-two"></div>
                        <div class="visual-node node-three"></div>

                        <div class="code-stack">
                            <div class="code-window window-back">
                                <div class="window-bar">
                                    <span></span><span></span><span></span>
                                </div>
                                <div class="code-lines">
                                    <i></i><i></i><i></i><i></i>
                                </div>
                            </div>

                            <div class="code-window window-main">
                                <div class="window-bar">
                                    <span></span><span></span><span></span>
                                </div>
                                <div class="code-editor">
                                    <div class="editor-line short"></div>
                                    <div class="editor-line medium"></div>
                                    <div class="editor-line long"></div>
                                    <div class="editor-line medium accent"></div>
                                    <div class="editor-line long"></div>
                                    <div class="editor-line short accent"></div>
                                </div>
                                <div class="terminal-badge">
                                    <Terminal :size="20" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Story -->
            <section id="story" class="story-section section-shell section-anchor">
                <div class="story-copy">
                    <span class="eyebrow">داستان ما</span>
                    <h2>از یک ایده مشترک تا ساختن محصولات واقعی</h2>

                    <p>
                        Derin Code از یک همکاری ساده بین دو دوست شکل گرفت؛ دو نفری که
                        علاقه مشترکشان ساختن و حل کردن مسئله‌های واقعی با تکنولوژی بود.
                    </p>

                    <p>
                        از همان ابتدا هدف ما فقط ساختن چند صفحه وب نبود. می‌خواستیم
                        محصولاتی بسازیم که واقعاً استفاده شوند، قابل توسعه باشند و برای
                        صاحبان کسب‌وکار ارزش ایجاد کنند.
                    </p>

                    <p>
                        امروز مسیر ما بر پایه یادگیری مداوم، همکاری و ساختن پروژه‌های
                        واقعی ادامه پیدا می‌کند؛ از وب‌سایت‌های ساده تا سیستم‌های پیچیده‌تر
                        و محصولات دیجیتال آینده.
                    </p>
                </div>

                <div class="story-visual" aria-hidden="true">
                    <div class="story-panel panel-one">
                        <Code2 :size="30" />
                        <span>Build</span>
                    </div>
                    <div class="story-panel panel-two">
                        <Users :size="30" />
                        <span>Together</span>
                    </div>
                    <div class="story-panel panel-three">
                        <Rocket :size="30" />
                        <span>Launch</span>
                    </div>
                </div>
            </section>

            <!-- Services -->
            <section class="services-section section-shell section-anchor" id="services">
                <div class="section-heading centered">
                    <span class="eyebrow">چه چیزی می‌سازیم؟</span>
                    <h2>راهکارهایی برای ایده‌های واقعی</h2>
                    <p>
                        تمرکز ما روی محصولاتی است که هم از نظر ظاهر حرفه‌ای باشند و هم
                        از نظر فنی پایه‌ای درست برای رشد آینده داشته باشند.
                    </p>
                </div>

                <div class="services-grid">
                    <article
                        v-for="service in services"
                        :key="service.title"
                        class="service-card"
                    >
                        <div class="card-icon">
                            <component :is="service.icon" :size="24" />
                        </div>
                        <h3>{{ service.title }}</h3>
                        <p>{{ service.description }}</p>
                        <span class="card-arrow" aria-hidden="true">
                            <ArrowLeft :size="16" />
                        </span>
                    </article>
                </div>
            </section>

            <!-- Team -->
            <section id="team" class="team-section section-shell section-anchor">
                <div class="section-heading centered">
                    <span class="eyebrow">تیم ما</span>
                    <h2>دو نفر، یک مسیر</h2>
                    <p>
                        هرکدام از ما روی بخشی از محصول تمرکز می‌کنیم و در نهایت یک تجربه یکپارچه می‌سازیم.
                    </p>
                </div>

                <div class="team-grid">
                    <article class="member-card backend-member">
                        <div class="member-head">
                            <div class="member-symbol" aria-hidden="true">
                                <div class="symbol-ring"></div>
                                <span>{ }</span>
                            </div>
                            <div>
                                <span class="member-role">Backend Developer</span>
                                <h3>معین</h3>
                            </div>
                        </div>

                        <p>
                            تمرکز روی Backend، معماری و ساخت سیستم‌هایی که پایدار، امن و قابل توسعه باشند.
                        </p>

                        <div class="member-tags">
                            <span>PHP</span>
                            <span>Laravel</span>
                            <span>REST API</span>
                            <span>MySQL</span>
                            <span>Redis</span>
                            <span>Docker</span>
                            <span>Architecture</span>
                        </div>
                    </article>

                    <article class="member-card frontend-member">
                        <div class="member-head">
                            <div class="member-symbol" aria-hidden="true">
                                <div class="symbol-grid"></div>
                                <span>&lt;/&gt;</span>
                            </div>
                            <div>
                                <span class="member-role">Frontend Developer</span>
                                <h3>علیرضا</h3>
                            </div>
                        </div>

                        <p>
                            تمرکز روی رابط کاربری مدرن، تجربه کاربری روان و تبدیل طراحی به رابط‌های تمیز و واکنش‌گرا.
                        </p>

                        <div class="member-tags">
                            <span>JavaScript</span>
                            <span>Vue.js</span>
                            <span>React</span>
                            <span>Alpine.js</span>
                            <span>UI Development</span>
                            <span>Responsive Design</span>
                        </div>
                    </article>
                </div>
            </section>

            <!-- Technology -->
            <section class="technology-section section-shell">
                <div class="technology-grid">
                    <div class="technology-copy">
                        <span class="eyebrow">ابزارهای ما</span>
                        <h2>فناوری‌هایی که با آن‌ها کار می‌کنیم</h2>
                        <p>
                            ابزار فقط وسیله است؛ انتخاب ما بر اساس نیاز پروژه، مقیاس سیستم
                            و تجربه‌ای است که باید برای کاربر ساخته شود.
                        </p>
                    </div>

                    <div class="technology-list">
                        <div
                            v-for="technology in technologies"
                            :key="technology.name"
                            class="technology-item"
                        >
                            <component :is="technology.icon" :size="19" />
                            <span>{{ technology.name }}</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Principles -->
            <section class="principles-section section-shell section-anchor" id="principles">
                <div class="section-heading centered">
                    <span class="eyebrow">اصول ما</span>
                    <h2>چطور کار می‌کنیم؟</h2>
                </div>

                <div class="principles-grid">
                    <article
                        v-for="principle in principles"
                        :key="principle.title"
                        class="principle-card"
                    >
                        <div class="principle-icon">
                            <component :is="principle.icon" :size="21" />
                        </div>
                        <div>
                            <h3>{{ principle.title }}</h3>
                            <p>{{ principle.description }}</p>
                        </div>
                    </article>
                </div>
            </section>

            <!-- Process -->
            <section class="process-section section-shell section-anchor" id="process">
                <div class="process-box">
                    <div class="process-heading">
                        <span class="eyebrow">فرآیند همکاری</span>
                        <h2>از ایده تا انتشار، مرحله‌به‌مرحله</h2>
                        <p>
                            شفافیت در مسیر توسعه باعث می‌شود هر دو طرف بدانند پروژه در چه مرحله‌ای قرار دارد.
                        </p>
                    </div>

                    <div class="process-flow">
                        <template
                            v-for="(step, index) in processSteps"
                            :key="step.title"
                        >
                            <div class="process-step">
                                <div class="process-icon">
                                    <component :is="step.icon" :size="20" />
                                </div>
                                <span>{{ step.title }}</span>
                            </div>

                            <ArrowLeft
                                v-if="index < processSteps.length - 1"
                                class="process-arrow"
                                :size="18"
                                aria-hidden="true"
                            />
                        </template>
                    </div>
                </div>
            </section>

            <!-- CTA -->
            <section class="about-cta section-shell section-anchor" id="start-project">
                <div class="cta-glow"></div>
                <div class="cta-content">
                    <span class="eyebrow">یک ایده دارید؟</span>
                    <h2>بیایید آن را به یک محصول واقعی تبدیل کنیم.</h2>
                    <p>
                        درباره ایده، نیاز و هدف پروژه‌تان با ما صحبت کنید تا بهترین مسیر ساخت آن را پیدا کنیم.
                    </p>

                    <RouterLink
                        :to="{ name: 'request-project' }"
                        class="primary-action cta-button"
                    >
                        <span>شروع یک پروژه</span>
                        <ArrowLeft :size="17" />
                    </RouterLink>
                </div>
            </section>
        </main>
    </PublicLayout>
</template>

<style scoped>
.about-page {
    --about-bg: #050b10;
    --about-panel: rgba(10, 19, 27, 0.78);
    --about-panel-strong: rgba(12, 23, 32, 0.94);
    --about-border: rgba(114, 154, 185, 0.2);
    --about-border-strong: rgba(255, 117, 19, 0.42);
    --about-text: #f4f7fa;
    --about-muted: #9aaab7;
    --about-orange: #ff7413;
    --about-orange-soft: rgba(255, 116, 19, 0.13);
    --about-blue: #27a7ff;
    --about-radius: 22px;
    color: var(--about-text);
    background:
        radial-gradient(circle at 15% 0%, rgba(255, 116, 19, 0.06), transparent 25%),
        radial-gradient(circle at 85% 25%, rgba(39, 167, 255, 0.045), transparent 28%),
        var(--about-bg);
    overflow: hidden;
}

.section-shell {
    width: min(1180px, calc(100% - 40px));
    margin: 0 auto;
}

.section-anchor {
    scroll-margin-top: 100px;
}

.eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--about-orange);
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.01em;
}

.eyebrow::after {
    content: '';
    width: 30px;
    height: 2px;
    background: var(--about-orange);
    border-radius: 20px;
}

h1,
h2,
h3,
p {
    margin: 0;
}

.about-hero {
    padding: 100px 0 90px;
    min-height: 690px;
    display: flex;
    align-items: center;
}

.hero-grid,
.story-section,
.technology-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 74px;
    align-items: center;
}

.hero-content {
    max-width: 590px;
    position: relative;
    z-index: 2;
}

.hero-content h1 {
    margin-top: 17px;
    font-size: clamp(2.7rem, 5vw, 5rem);
    line-height: 1.15;
    font-weight: 900;
    letter-spacing: -0.04em;
}

.hero-content h1 span {
    color: var(--about-orange);
}

.hero-lead {
    margin-top: 24px;
    max-width: 520px;
    color: #d9e1e8;
    font-size: clamp(1.12rem, 2vw, 1.45rem);
    line-height: 2;
    font-weight: 700;
}

.hero-text {
    margin-top: 16px;
    max-width: 560px;
    color: var(--about-muted);
    font-size: 1rem;
    line-height: 2.15;
}

.hero-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.primary-action,
.ghost-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    min-height: 46px;
    padding: 0 20px;
    border-radius: 12px;
    font: inherit;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    transition: transform 180ms ease, border-color 180ms ease, background 180ms ease;
}

.primary-action {
    color: #0b0e10;
    background: linear-gradient(135deg, #ff8c2a, var(--about-orange));
    border: 1px solid transparent;
    box-shadow: 0 14px 35px rgba(255, 116, 19, 0.18);
}

.ghost-action {
    color: #d4dce3;
    background: rgba(12, 22, 30, 0.72);
    border: 1px solid var(--about-border);
}

.primary-action:hover,
.ghost-action:hover {
    transform: translateY(-2px);
}

.primary-action:hover {
    box-shadow: 0 18px 42px rgba(255, 116, 19, 0.25);
}

.ghost-action:hover {
    border-color: rgba(255, 116, 19, 0.3);
}

/* Abstract hero artwork */
.hero-visual {
    min-height: 500px;
    position: relative;
    display: grid;
    place-items: center;
}

.visual-orbit {
    position: absolute;
    border: 1px solid rgba(255, 116, 19, 0.3);
    border-radius: 50%;
    transform: rotate(-24deg);
}

.orbit-one {
    width: 510px;
    height: 220px;
}

.orbit-two {
    width: 360px;
    height: 430px;
    border-color: rgba(39, 167, 255, 0.15);
    transform: rotate(52deg);
}

.visual-node {
    position: absolute;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--about-orange);
    box-shadow: 0 0 26px rgba(255, 116, 19, 0.8);
}

.node-one { top: 18%; right: 20%; }
.node-two { bottom: 20%; left: 17%; }
.node-three { top: 34%; left: 8%; width: 7px; height: 7px; background: var(--about-blue); }

.code-stack {
    position: relative;
    width: min(100%, 500px);
    height: 360px;
}

.code-window {
    position: absolute;
    width: min(430px, 90%);
    height: 265px;
    border: 1px solid rgba(111, 157, 191, 0.26);
    background: linear-gradient(145deg, rgba(15, 28, 38, 0.97), rgba(7, 14, 20, 0.96));
    border-radius: 22px;
    box-shadow: 0 35px 70px rgba(0, 0, 0, 0.38);
    overflow: hidden;
}

.window-back {
    top: 25px;
    left: 2%;
    transform: rotate(-7deg);
    opacity: 0.72;
}

.window-main {
    right: 2%;
    bottom: 0;
    transform: rotate(3deg);
    border-color: rgba(255, 116, 19, 0.42);
}

.window-bar {
    height: 34px;
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 0 13px;
    border-bottom: 1px solid rgba(111, 157, 191, 0.15);
}

.window-bar span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(181, 197, 208, 0.35);
}

.window-bar span:nth-child(2) {
    background: rgba(255, 255, 255, 0.16);
}

.code-lines {
    display: grid;
    gap: 16px;
    padding: 32px 30px;
}

.code-lines i,
.editor-line {
    display: block;
    height: 7px;
    border-radius: 10px;
    background: rgba(131, 166, 191, 0.32);
}

.code-lines i:nth-child(1) { width: 75%; }
.code-lines i:nth-child(2) { width: 55%; }
.code-lines i:nth-child(3) { width: 84%; }
.code-lines i:nth-child(4) { width: 40%; }

.code-editor {
    display: grid;
    gap: 15px;
    padding: 28px 28px 0;
}

.editor-line.short { width: 26%; }
.editor-line.medium { width: 53%; }
.editor-line.long { width: 79%; }
.editor-line.accent {
    background: rgba(255, 116, 19, 0.72);
}

.terminal-badge {
    position: absolute;
    left: 25px;
    bottom: 22px;
    width: 46px;
    height: 46px;
    display: grid;
    place-items: center;
    border-radius: 14px;
    color: var(--about-orange);
    border: 1px solid rgba(255, 116, 19, 0.45);
    background: rgba(255, 116, 19, 0.08);
    box-shadow: 0 12px 32px rgba(255, 116, 19, 0.12);
}

/* Shared sections */
.story-section,
.services-section,
.team-section,
.technology-section,
.principles-section,
.process-section,
.about-cta {
    padding-top: 92px;
    padding-bottom: 92px;
}

.story-copy h2,
.technology-copy h2,
.section-heading h2,
.process-heading h2,
.about-cta h2 {
    margin-top: 13px;
    font-size: clamp(2rem, 4vw, 3rem);
    line-height: 1.45;
    font-weight: 900;
    letter-spacing: -0.03em;
}

.story-copy p,
.technology-copy p,
.section-heading p,
.process-heading p,
.about-cta p {
    color: var(--about-muted);
    line-height: 2.15;
}

.story-copy p {
    margin-top: 17px;
    max-width: 600px;
}

.story-visual {
    min-height: 380px;
    position: relative;
    border: 1px solid var(--about-border);
    border-radius: 28px;
    background:
        linear-gradient(145deg, rgba(12, 24, 34, 0.96), rgba(5, 12, 18, 0.94)),
        radial-gradient(circle at 50% 50%, rgba(255, 116, 19, 0.09), transparent 60%);
    overflow: hidden;
}

.story-visual::before,
.story-visual::after {
    content: '';
    position: absolute;
    border: 1px dashed rgba(255, 116, 19, 0.18);
    border-radius: 50%;
}

.story-visual::before {
    width: 340px;
    height: 340px;
    inset: 20px auto auto 80px;
}

.story-visual::after {
    width: 230px;
    height: 230px;
    inset: 75px 140px auto auto;
    border-color: rgba(39, 167, 255, 0.16);
}

.story-panel {
    position: absolute;
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 15px 17px;
    color: #e6edf2;
    background: rgba(10, 21, 29, 0.95);
    border: 1px solid var(--about-border);
    border-radius: 16px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
}

.story-panel svg {
    color: var(--about-orange);
}

.panel-one { top: 48px; right: 64px; }
.panel-two { top: 155px; left: 62px; }
.panel-three { right: 120px; bottom: 56px; }

.centered {
    text-align: center;
    max-width: 780px;
    margin: 0 auto 36px;
}

.centered .eyebrow {
    justify-content: center;
}

.centered p {
    margin-top: 12px;
}

.services-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
}

.service-card,
.member-card,
.principle-card {
    position: relative;
    border: 1px solid var(--about-border);
    border-radius: var(--about-radius);
    background: linear-gradient(145deg, rgba(11, 24, 33, 0.92), rgba(7, 14, 20, 0.84));
    transition: transform 180ms ease, border-color 180ms ease, box-shadow 180ms ease;
}

.service-card:hover,
.member-card:hover,
.principle-card:hover {
    transform: translateY(-4px);
    border-color: rgba(255, 116, 19, 0.34);
    box-shadow: 0 24px 65px rgba(0, 0, 0, 0.24);
}

.service-card {
    padding: 24px 22px 27px;
    min-height: 255px;
}

.card-icon,
.principle-icon {
    width: 46px;
    height: 46px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(255, 116, 19, 0.28);
    border-radius: 14px;
    color: var(--about-orange);
    background: rgba(255, 116, 19, 0.08);
}

.service-card h3,
.principle-card h3,
.member-card h3 {
    margin-top: 18px;
    font-size: 1.12rem;
    line-height: 1.8;
}

.service-card p,
.principle-card p,
.member-card p {
    margin-top: 9px;
    color: var(--about-muted);
    line-height: 2;
    font-size: 0.92rem;
}

.card-arrow {
    position: absolute;
    left: 21px;
    bottom: 20px;
    color: var(--about-orange);
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
}

.member-card {
    padding: 26px;
    min-height: 310px;
}

.member-head {
    display: flex;
    align-items: center;
    gap: 16px;
}

.member-symbol {
    position: relative;
    width: 84px;
    height: 84px;
    flex: 0 0 84px;
    display: grid;
    place-items: center;
    border-radius: 22px;
    color: #e9eff5;
    font-family: Consolas, monospace;
    font-size: 1.18rem;
    font-weight: 700;
    overflow: hidden;
}

.backend-member .member-symbol {
    border: 1px solid rgba(255, 116, 19, 0.45);
    background: radial-gradient(circle at 45% 35%, rgba(255, 116, 19, 0.28), rgba(255, 116, 19, 0.03));
}

.frontend-member .member-symbol {
    border: 1px solid rgba(39, 167, 255, 0.4);
    background: radial-gradient(circle at 55% 35%, rgba(39, 167, 255, 0.25), rgba(39, 167, 255, 0.03));
}

.symbol-ring,
.symbol-grid {
    position: absolute;
    inset: 12px;
    border: 1px dashed rgba(255, 116, 19, 0.4);
    border-radius: 50%;
    transform: rotate(18deg);
}

.symbol-grid {
    border-radius: 18px;
    border-color: rgba(39, 167, 255, 0.38);
    transform: rotate(-12deg);
}

.member-role {
    display: block;
    color: var(--about-orange);
    font-size: 0.85rem;
    font-weight: 700;
    margin-bottom: 4px;
}

.frontend-member .member-role {
    color: var(--about-blue);
}

.member-card p {
    max-width: 680px;
    margin-top: 18px;
    font-size: 0.96rem;
}

.member-tags {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 23px;
}

.member-tags span {
    display: inline-flex;
    align-items: center;
    min-height: 33px;
    padding: 0 11px;
    border: 1px solid rgba(112, 150, 178, 0.17);
    border-radius: 10px;
    color: #bccbd7;
    background: rgba(255, 255, 255, 0.025);
    font-size: 0.8rem;
}

.technology-grid {
    padding: 38px;
    border: 1px solid var(--about-border);
    border-radius: 28px;
    background: linear-gradient(145deg, rgba(10, 21, 29, 0.88), rgba(7, 14, 20, 0.88));
}

.technology-copy p {
    margin-top: 13px;
    max-width: 550px;
}

.technology-list {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 10px;
}

.technology-item {
    min-height: 68px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    color: #d6e1ea;
    border: 1px solid rgba(112, 150, 178, 0.16);
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.025);
    transition: border-color 180ms ease, transform 180ms ease;
}

.technology-item svg {
    color: var(--about-orange);
}

.technology-item:hover {
    transform: translateY(-2px);
    border-color: rgba(255, 116, 19, 0.3);
}

.principles-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}

.principle-card {
    display: flex;
    gap: 16px;
    padding: 23px;
}

.principle-card h3 {
    margin-top: 0;
}

.process-box {
    padding: 42px 34px;
    border: 1px solid var(--about-border);
    border-radius: 28px;
    background:
        linear-gradient(145deg, rgba(12, 24, 33, 0.95), rgba(7, 14, 20, 0.92)),
        radial-gradient(circle at 10% 20%, rgba(255, 116, 19, 0.08), transparent 30%);
}

.process-heading {
    max-width: 700px;
}

.process-heading p {
    margin-top: 13px;
}

.process-flow {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-top: 38px;
}

.process-step {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #d7e0e8;
    white-space: nowrap;
}

.process-icon {
    width: 44px;
    height: 44px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(255, 116, 19, 0.28);
    border-radius: 50%;
    color: var(--about-orange);
    background: rgba(255, 116, 19, 0.06);
}

.process-arrow {
    color: rgba(157, 175, 190, 0.35);
}

.about-cta {
    position: relative;
    padding-bottom: 112px;
}

.about-cta::before {
    content: '';
    position: absolute;
    inset: 28px 0 34px;
    border-top: 1px solid rgba(255, 116, 19, 0.09);
    border-bottom: 1px solid rgba(255, 116, 19, 0.09);
    pointer-events: none;
}

.cta-content {
    position: relative;
    z-index: 1;
    max-width: 760px;
    margin: 0 auto;
    text-align: center;
}

.cta-content .eyebrow {
    justify-content: center;
}

.cta-content p {
    max-width: 650px;
    margin: 13px auto 0;
}

.cta-button {
    margin-top: 27px;
}

.cta-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 360px;
    height: 160px;
    transform: translate(-50%, -50%);
    background: radial-gradient(circle, rgba(255, 116, 19, 0.16), transparent 68%);
    filter: blur(30px);
    pointer-events: none;
}

@media (max-width: 1024px) {
    .hero-grid,
    .story-section,
    .technology-grid {
        grid-template-columns: 1fr;
        gap: 46px;
    }

    .hero-content {
        max-width: 760px;
    }

    .hero-visual {
        min-height: 410px;
    }

    .services-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .technology-grid {
        padding: 30px;
    }
}

@media (max-width: 760px) {
    .section-shell {
        width: min(100% - 28px, 680px);
    }

    .about-hero {
        padding-top: 72px;
        padding-bottom: 65px;
        min-height: auto;
    }

    .hero-content h1 {
        font-size: clamp(2.35rem, 11vw, 3.5rem);
    }

    .hero-visual {
        min-height: 335px;
    }

    .orbit-one {
        width: 330px;
        height: 160px;
    }

    .orbit-two {
        width: 250px;
        height: 320px;
    }

    .code-stack {
        height: 280px;
    }

    .code-window {
        height: 218px;
    }

    .window-back {
        left: 0;
    }

    .window-main {
        right: 0;
    }

    .story-visual {
        min-height: 320px;
    }

    .story-visual::before {
        width: 260px;
        height: 260px;
        left: 30px;
    }

    .story-visual::after {
        width: 180px;
        height: 180px;
        right: 55px;
        top: 80px;
    }

    .panel-one { top: 30px; right: 24px; }
    .panel-two { top: 132px; left: 22px; }
    .panel-three { right: 55px; bottom: 30px; }

    .team-grid,
    .principles-grid {
        grid-template-columns: 1fr;
    }

    .technology-list {
        grid-template-columns: repeat(2, 1fr);
    }

    .process-flow {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px 12px;
    }

    .process-arrow {
        display: none;
    }

    .process-step {
        white-space: normal;
    }

    .story-section,
    .services-section,
    .team-section,
    .technology-section,
    .principles-section,
    .process-section,
    .about-cta {
        padding-top: 65px;
        padding-bottom: 65px;
    }
}

@media (max-width: 520px) {
    .services-grid {
        grid-template-columns: 1fr;
    }

    .technology-grid {
        padding: 24px 18px;
    }

    .member-card {
        padding: 21px;
    }

    .member-head {
        align-items: flex-start;
    }

    .member-symbol {
        width: 66px;
        height: 66px;
        flex-basis: 66px;
        border-radius: 17px;
    }

    .technology-item {
        min-height: 60px;
        font-size: 0.88rem;
    }

    .process-box {
        padding: 28px 19px;
    }
}
</style>
