<script setup>
import { reactive, ref } from 'vue'
import {
    ArrowLeft,
    CheckCircle2,
    Mail,
    MessageSquare,
    Phone,
    User,
} from 'lucide-vue-next'

import api from '../../services/api'

const form = reactive({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
})

const errors = ref({})
const submitting = ref(false)
const successMessage = ref('')
const generalError = ref('')

const resetMessages = () => {
    errors.value = {}
    successMessage.value = ''
    generalError.value = ''
}

const submitForm = async () => {
    resetMessages()
    submitting.value = true

    try {
        const response = await api.post('/contact', form)

        successMessage.value =
            response?.data?.message ||
            'پیام شما با موفقیت ارسال شد.'

        Object.assign(form, {
            name: '',
            email: '',
            phone: '',
            subject: '',
            message: '',
        })
    } catch (error) {
        if (error?.response?.status === 422) {
            errors.value = error?.response?.data?.errors || {}

            generalError.value =
                error?.response?.data?.message ||
                'اطلاعات واردشده صحیح نیست.'
        } else {
            generalError.value =
                error?.response?.data?.message ||
                'ارسال پیام با مشکل مواجه شد.'
        }
    } finally {
        submitting.value = false
    }
}
</script>

<template>
    <section
        id="contact"
        class="contact-section section"
        aria-labelledby="contact-title"
    >
        <div class="container">
            <div class="contact-wrapper">

                <!-- Intro -->
                <div class="contact-intro">
                    <span class="section-kicker">
                        تماس با ما
                    </span>

                    <h2
                        id="contact-title"
                        class="contact-title"
                    >
                        بیایید درباره
                        <span>پروژه شما</span>
                        صحبت کنیم.
                    </h2>

                    <p class="contact-description">
                        ایده، نیاز یا چالش کسب‌وکارتان را برای ما
                        توضیح دهید. بعد از بررسی، بهترین مسیر
                        فنی و اجرایی را با شما بررسی می‌کنیم.
                    </p>

                    <div class="contact-points">
                        <div class="contact-point">
                            <div class="point-icon" aria-hidden="true">
                                <MessageSquare :size="17" />
                            </div>

                            <div>
                                <strong>
                                    پاسخ‌گویی مستقیم
                                </strong>

                                <span>
                                    پیام شما مستقیماً بررسی می‌شود.
                                </span>
                            </div>
                        </div>

                        <div class="contact-point">
                            <div class="point-icon" aria-hidden="true">
                                <CheckCircle2 :size="17" />
                            </div>

                            <div>
                                <strong>
                                    بررسی نیاز پروژه
                                </strong>

                                <span>
                                    قبل از توسعه، راه‌حل مناسب را مشخص می‌کنیم.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="contact-form-wrapper">
                    <div class="form-header">
                        <div>
                            <span lang="en">Project Inquiry</span>
                            <strong>فرم درخواست همکاری</strong>
                        </div>

                        <div
                            class="form-status"
                            aria-label="وضعیت سامانه: آنلاین"
                        >
                            <i aria-hidden="true"></i>
                            آنلاین
                        </div>
                    </div>

                    <!-- Success -->
                    <div
                        v-if="successMessage"
                        class="form-success"
                        role="status"
                        aria-live="polite"
                    >
                        <CheckCircle2 :size="25" aria-hidden="true" />

                        <div>
                            <strong>
                                پیام با موفقیت ارسال شد
                            </strong>

                            <span>
                                {{ successMessage }}
                            </span>
                        </div>
                    </div>

                    <!-- General Error -->
                    <div
                        v-if="generalError && !successMessage"
                        class="form-error"
                        role="alert"
                        aria-live="assertive"
                    >
                        {{ generalError }}
                    </div>

                    <form
                        class="contact-form"
                        @submit.prevent="submitForm"
                        novalidate
                    >
                        <div class="form-grid">

                            <!-- Name -->
                            <div class="field">
                                <label for="contact-name">
                                    نام و نام خانوادگی
                                </label>

                                <div class="input-wrapper">
                                    <User
                                        :size="16"
                                        aria-hidden="true"
                                    />

                                    <input
                                        id="contact-name"
                                        v-model="form.name"
                                        type="text"
                                        placeholder="مثلاً معین محمودی"
                                        autocomplete="name"
                                        :aria-invalid="!!errors.name"
                                        :disabled="submitting"
                                    />
                                </div>

                                <span
                                    v-if="errors.name"
                                    class="field-error"
                                    role="alert"
                                >
                                    {{ errors.name[0] }}
                                </span>
                            </div>

                            <!-- Email -->
                            <div class="field">
                                <label for="contact-email">
                                    ایمیل
                                </label>

                                <div class="input-wrapper">
                                    <Mail
                                        :size="16"
                                        aria-hidden="true"
                                    />

                                    <input
                                        id="contact-email"
                                        v-model="form.email"
                                        type="email"
                                        placeholder="example@email.com"
                                        autocomplete="email"
                                        dir="ltr"
                                        :aria-invalid="!!errors.email"
                                        :disabled="submitting"
                                    />
                                </div>

                                <span
                                    v-if="errors.email"
                                    class="field-error"
                                    role="alert"
                                >
                                    {{ errors.email[0] }}
                                </span>
                            </div>

                            <!-- Phone -->
                            <div class="field">
                                <label for="contact-phone">
                                    شماره تماس
                                    <small>(اختیاری)</small>
                                </label>

                                <div class="input-wrapper">
                                    <Phone
                                        :size="16"
                                        aria-hidden="true"
                                    />

                                    <input
                                        id="contact-phone"
                                        v-model="form.phone"
                                        type="tel"
                                        placeholder="09xxxxxxxxx"
                                        autocomplete="tel"
                                        dir="ltr"
                                        :aria-invalid="!!errors.phone"
                                        :disabled="submitting"
                                    />
                                </div>

                                <span
                                    v-if="errors.phone"
                                    class="field-error"
                                    role="alert"
                                >
                                    {{ errors.phone[0] }}
                                </span>
                            </div>

                            <!-- Subject -->
                            <div class="field">
                                <label for="contact-subject">
                                    موضوع
                                    <small>(اختیاری)</small>
                                </label>

                                <div class="input-wrapper">
                                    <MessageSquare
                                        :size="16"
                                        aria-hidden="true"
                                    />

                                    <input
                                        id="contact-subject"
                                        v-model="form.subject"
                                        type="text"
                                        placeholder="موضوع پروژه"
                                        :aria-invalid="!!errors.subject"
                                        :disabled="submitting"
                                    />
                                </div>

                                <span
                                    v-if="errors.subject"
                                    class="field-error"
                                    role="alert"
                                >
                                    {{ errors.subject[0] }}
                                </span>
                            </div>

                        </div>

                        <!-- Message -->
                        <div class="field field-full">
                            <label for="contact-message">
                                توضیحات پروژه
                            </label>

                            <div class="input-wrapper textarea-wrapper">
                                <MessageSquare
                                    :size="16"
                                    aria-hidden="true"
                                />

                                <textarea
                                    id="contact-message"
                                    v-model="form.message"
                                    rows="6"
                                    placeholder="کمی درباره پروژه، نیازها و ایده‌تان توضیح دهید..."
                                    :aria-invalid="!!errors.message"
                                    :disabled="submitting"
                                ></textarea>
                            </div>

                            <span
                                v-if="errors.message"
                                class="field-error"
                                role="alert"
                            >
                                {{ errors.message[0] }}
                            </span>
                        </div>

                        <button
                            type="submit"
                            class="submit-button"
                            :disabled="submitting"
                        >
                            <span v-if="!submitting">
                                ارسال درخواست
                            </span>

                            <span v-else>
                                در حال ارسال...
                            </span>

                            <ArrowLeft
                                v-if="!submitting"
                                :size="17"
                                aria-hidden="true"
                            />

                            <span
                                v-else
                                class="submit-loader"
                                aria-hidden="true"
                            ></span>
                        </button>

                        <p class="form-note">
                            اطلاعات شما فقط برای بررسی درخواست
                            پروژه استفاده خواهد شد.
                        </p>
                    </form>
                </div>

            </div>
        </div>
    </section>
</template>

<style scoped>
.contact-section {
    position: relative;
    padding-top: 45px;
    padding-bottom: 110px;
    overflow: hidden;
}

.contact-section::before {
    content: "";

    position: absolute;
    width: 500px;
    height: 500px;

    right: -250px;
    top: 10%;

    border-radius: 50%;

    background: rgba(255, 107, 0, 0.035);
    filter: blur(110px);

    pointer-events: none;
}

.contact-wrapper {
    position: relative;
    z-index: 1;

    display: grid;
    grid-template-columns:
        minmax(300px, 0.78fr)
        minmax(500px, 1.22fr);

    gap: 70px;
    align-items: start;
}

/* Intro */

.contact-intro {
    padding-top: 18px;
}

.section-kicker {
    position: relative;

    display: inline-block;
    padding-bottom: 10px;

    color: var(--text-primary);

    font-size: 21px;
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

    border-radius: 99px;
    background: var(--orange);
}

.contact-title {
    max-width: 460px;
    margin-top: 20px;

    color: var(--text-primary);

    font-size: clamp(34px, 4.2vw, 52px);
    font-weight: 850;
    line-height: 1.35;

    letter-spacing: -0.035em;
}

.contact-title span {
    color: var(--orange);
}

.contact-description {
    max-width: 440px;
    margin-top: 20px;

    color: var(--text-secondary);

    font-size: 14px;
    font-weight: 400;
    line-height: 2.15;
}

.contact-points {
    display: flex;
    flex-direction: column;
    gap: 18px;

    margin-top: 35px;
}

.contact-point {
    display: flex;
    align-items: center;
    gap: 12px;
}

.point-icon {
    width: 38px;
    height: 38px;

    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    color: var(--orange);

    border: 1px solid rgba(255, 107, 0, 0.18);
    border-radius: 9px;

    background: rgba(255, 107, 0, 0.03);
}

.contact-point div:last-child {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.contact-point strong {
    color: var(--text-primary);

    font-size: 12px;
    font-weight: 700;
    line-height: 1.7;
}

.contact-point span {
    color: var(--text-muted);

    font-size: 11px;
    line-height: 1.8;
}

/* Form */

.contact-form-wrapper {
    overflow: hidden;

    border: 1px solid rgba(255, 255, 255, 0.085);
    border-radius: 14px;

    background:
        linear-gradient(
            145deg,
            rgba(255, 255, 255, 0.03),
            rgba(255, 255, 255, 0.008)
        );

    box-shadow:
        0 25px 70px rgba(0, 0, 0, 0.24);
}

.form-header {
    min-height: 66px;
    padding: 0 20px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    border-bottom: 1px solid var(--border);
    background: rgba(255, 255, 255, 0.012);
}

.form-header div:first-child {
    display: flex;
    flex-direction: column;
}

.form-header span {
    color: var(--text-muted);

    direction: ltr;
    font-family: var(--font-mono);

    font-size: 9px;
    line-height: 1.5;
}

.form-header strong {
    margin-top: 3px;

    color: var(--text-primary);

    font-size: 13px;
    font-weight: 700;
    line-height: 1.7;
}

.form-status {
    display: inline-flex !important;
    flex-direction: row !important;
    align-items: center;

    gap: 7px;

    color: var(--success) !important;

    direction: rtl !important;
    font-family: inherit !important;

    font-size: 10px !important;
    font-weight: 600;
}

.form-status i {
    width: 6px;
    height: 6px;

    border-radius: 50%;
    background: var(--success);

    box-shadow:
        0 0 8px rgba(55, 214, 122, 0.5);
}

.contact-form {
    padding: 24px 20px 19px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}

.field {
    display: flex;
    flex-direction: column;
}

.field-full {
    margin-top: 18px;
}

.field label {
    margin-bottom: 8px;

    color: #d4d7da;

    font-size: 11px;
    font-weight: 650;
    line-height: 1.7;
}

.field label small {
    color: var(--text-muted);

    font-size: 10px;
    font-weight: 400;
}

.input-wrapper {
    min-height: 45px;

    display: flex;
    align-items: center;
    gap: 10px;

    padding: 0 12px;

    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 8px;

    background: rgba(5, 6, 7, 0.5);

    transition:
        border-color var(--transition),
        background var(--transition),
        box-shadow var(--transition);
}

.input-wrapper:focus-within {
    border-color: rgba(255, 107, 0, 0.42);

    background: rgba(255, 107, 0, 0.02);

    box-shadow:
        0 0 0 3px rgba(255, 107, 0, 0.045);
}

.input-wrapper svg {
    flex-shrink: 0;
    color: #62696f;
}

.input-wrapper input,
.input-wrapper textarea {
    width: 100%;

    border: 0;
    outline: 0;

    color: var(--text-primary);
    background: transparent;

    font-family: inherit;
    font-size: 12px;
    font-weight: 400;
    line-height: 1.8;
}

.input-wrapper input::placeholder,
.input-wrapper textarea::placeholder {
    color: #555c62;
}

.textarea-wrapper {
    align-items: flex-start;
    padding-top: 12px;
}

.textarea-wrapper svg {
    margin-top: 3px;
}

.input-wrapper textarea {
    min-height: 120px;

    resize: vertical;
    line-height: 2;
}

.field-error {
    margin-top: 6px;

    color: var(--danger);

    font-size: 10px;
    line-height: 1.7;
}

.submit-button {
    width: 100%;
    min-height: 47px;

    margin-top: 20px;

    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;

    border-radius: 8px;

    color: #ffffff;
    background:
        linear-gradient(
            135deg,
            #ff770e,
            #ff5f00
        );

    font-family: inherit;
    font-size: 12px;
    font-weight: 750;

    cursor: pointer;

    box-shadow:
        0 10px 30px rgba(255, 107, 0, 0.15);

    transition:
        transform var(--transition),
        box-shadow var(--transition),
        background var(--transition);
}

.submit-button:hover:not(:disabled) {
    transform: translateY(-2px);

    box-shadow:
        0 14px 35px rgba(255, 107, 0, 0.25);
}

.submit-button:disabled {
    opacity: 0.72;
    cursor: not-allowed;
}

.submit-loader {
    width: 14px;
    height: 14px;

    border: 2px solid rgba(255, 255, 255, 0.35);
    border-top-color: #fff;

    border-radius: 50%;

    animation: spin 0.8s linear infinite;
}

.form-note {
    margin-top: 11px;

    color: var(--text-muted);

    text-align: center;

    font-size: 9px;
    line-height: 1.8;
}

.form-success {
    margin: 16px 20px 0;
    padding: 14px;

    display: flex;
    align-items: flex-start;
    gap: 10px;

    border: 1px solid rgba(55, 214, 122, 0.14);
    border-radius: 8px;

    color: var(--success);
    background: rgba(55, 214, 122, 0.03);
}

.form-success div {
    display: flex;
    flex-direction: column;
}

.form-success strong {
    font-size: 11px;
    line-height: 1.7;
}

.form-success span {
    margin-top: 3px;

    color: var(--text-muted);

    font-size: 10px;
    line-height: 1.8;
}

.form-error {
    margin: 16px 20px 0;
    padding: 11px 13px;

    color: var(--danger);

    border: 1px solid rgba(255, 92, 92, 0.14);
    border-radius: 8px;

    background: rgba(255, 92, 92, 0.025);

    font-size: 10px;
    line-height: 1.8;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Responsive */

@media (max-width: 900px) {
    .contact-wrapper {
        grid-template-columns: 1fr;
        gap: 42px;
    }

    .contact-intro {
        padding-top: 0;
        text-align: center;
    }

    .section-kicker::after {
        left: 50%;
        right: auto;
        transform: translateX(-50%);
    }

    .contact-title,
    .contact-description {
        margin-inline: auto;
    }

    .contact-points {
        width: fit-content;
        margin-inline: auto;
        text-align: right;
    }
}

@media (max-width: 600px) {
    .contact-section {
        padding-bottom: 75px;
    }

    .contact-title {
        font-size: 34px;
    }

    .contact-description {
        font-size: 12px;
        line-height: 2.1;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .field-full {
        margin-top: 16px;
    }

    .contact-form {
        padding: 20px 13px 16px;
    }

    .form-header {
        padding-inline: 13px;
    }

    .form-header strong {
        font-size: 12px;
    }

    .input-wrapper input,
    .input-wrapper textarea {
        font-size: 12px;
    }

    .input-wrapper textarea {
        min-height: 115px;
    }

    .submit-button {
        min-height: 47px;
    }
}

@media (max-width: 380px) {
    .contact-title {
        font-size: 31px;
    }

    .contact-points {
        width: 100%;
    }
}
</style>
