<script setup>
import { computed, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import {
    ArrowLeft,
    ShieldCheck,
    LoaderCircle,
} from 'lucide-vue-next'

import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const otpInput = ref(null)

const form = reactive({
    otp: '',
})

const localError = ref('')
const successMessage = ref('')

const email = computed(() => {
    return authStore.forgotEmail
})

const submit = async () => {
    localError.value = ''
    successMessage.value = ''

    if (!email.value) {
        router.replace({
            name: 'forgot-password',
        })

        return
    }

    const result = await authStore.verifyOtp(
        email.value,
        form.otp
    )

    if (!result.success) {
        localError.value =
            authStore.errorMessage ||
            'کد واردشده معتبر نیست.'

        otpInput.value?.focus()

        return
    }

    successMessage.value =
        result.message ||
        'کد با موفقیت تأیید شد.'

    setTimeout(() => {
        router.replace({
            name: 'reset-password',
        })
    }, 700)
}
</script>

<template>
    <main
        class="auth-page"
        aria-labelledby="verify-otp-title"
    >
        <div class="auth-container">

            <RouterLink
                to="/"
                class="auth-logo"
                aria-label="DerinCode - صفحه اصلی"
            >
                <span>Derin</span><strong>Code</strong>
            </RouterLink>

            <section
                class="auth-card"
                aria-labelledby="verify-otp-title"
            >

                <div
                    class="otp-icon"
                    aria-hidden="true"
                >
                    <ShieldCheck :size="25" />
                </div>

                <header class="auth-header">
                    <span class="auth-kicker">
                        VERIFICATION
                    </span>

                    <h1 id="verify-otp-title">
                        تأیید کد
                    </h1>

                    <p>
                        کد ۶ رقمی ارسال‌شده به
                        <strong dir="ltr">
                            {{ email }}
                        </strong>
                        را وارد کنید.
                    </p>
                </header>

                <div
                    v-if="localError"
                    class="message message-error"
                    role="alert"
                    aria-live="assertive"
                >
                    {{ localError }}
                </div>

                <div
                    v-if="successMessage"
                    class="message message-success"
                    role="status"
                    aria-live="polite"
                >
                    {{ successMessage }}
                </div>

                <form
                    class="auth-form"
                    @submit.prevent="submit"
                >
                    <div class="field">
                        <label for="otp">
                            کد تأیید
                        </label>

                        <input
                            id="otp"
                            ref="otpInput"
                            v-model="form.otp"
                            class="otp-input"
                            type="text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            maxlength="6"
                            autocomplete="one-time-code"
                            placeholder="------"
                            required
                            aria-describedby="otp-hint"
                            :aria-invalid="
                                Boolean(authStore.errors.otp)
                            "
                        />

                        <span
                            id="otp-hint"
                            class="otp-hint"
                        >
                            کد ۶ رقمی ارسال‌شده را وارد کنید.
                        </span>

                        <span
                            v-if="authStore.errors.otp"
                            class="field-error"
                        >
                            {{ authStore.errors.otp[0] }}
                        </span>
                    </div>

                    <button
                        type="submit"
                        class="submit-button"
                        :disabled="
                            authStore.loading ||
                            form.otp.length !== 6
                        "
                        :aria-busy="authStore.loading"
                    >
                        <span v-if="!authStore.loading">
                            تأیید کد
                        </span>

                        <LoaderCircle
                            v-else
                            :size="17"
                            class="loader"
                            aria-hidden="true"
                        />

                        <ArrowLeft
                            v-if="!authStore.loading"
                            :size="16"
                            aria-hidden="true"
                        />
                    </button>

                    <button
                        type="button"
                        class="back-button"
                        @click="
                            router.replace({
                                name: 'forgot-password',
                            })
                        "
                    >
                        تغییر ایمیل
                    </button>
                </form>
            </section>
        </div>
    </main>
</template>

<style scoped>
/* ================================================================ */
/* Page */
/* ================================================================ */

.auth-page {
    min-height: 100vh;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 35px 20px;

    background:
        radial-gradient(
            circle at 50% 0%,
            rgba(255, 107, 0, 0.06),
            transparent 30rem
        ),
        var(--bg-primary);
}

.auth-container {
    width: 100%;
    max-width: 420px;
}

/* ================================================================ */
/* Logo */
/* ================================================================ */

.auth-logo {
    width: fit-content;

    display: block;

    margin: 0 auto 22px;

    direction: ltr;

    color: inherit;

    font-family: var(--font-mono);

    font-size: 22px;
    font-weight: 800;

    line-height: 1;

    text-decoration: none;
}

.auth-logo span {
    color: var(--text-primary);
}

.auth-logo strong {
    color: var(--orange);
}

/* ================================================================ */
/* Card */
/* ================================================================ */

.auth-card {
    padding: 27px 28px;

    text-align: center;

    border: 1px solid var(--border);
    border-radius: 16px;

    background:
        linear-gradient(
            145deg,
            rgba(255, 255, 255, 0.03),
            rgba(255, 255, 255, 0.008)
        );

    box-shadow:
        0 25px 80px rgba(0, 0, 0, 0.28);
}

/* ================================================================ */
/* Icon */
/* ================================================================ */

.otp-icon {
    width: 52px;
    height: 52px;

    margin: 0 auto;

    display: flex;
    align-items: center;
    justify-content: center;

    color: var(--orange);

    border: 1px solid var(--border-orange);
    border-radius: 13px;

    background: var(--orange-soft);
}

/* ================================================================ */
/* Header */
/* ================================================================ */

.auth-header {
    margin-top: 19px;
    margin-bottom: 23px;
}

.auth-kicker {
    display: inline-block;

    color: var(--orange);

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 10px;
    font-weight: 600;

    line-height: 1.7;
}

.auth-header h1 {
    margin-top: 7px;

    color: var(--text-primary);

    font-size: 28px;
    font-weight: 900;

    line-height: 1.35;
}

.auth-header p {
    margin-top: 7px;

    color: var(--text-muted);

    font-size: 12px;

    line-height: 1.9;
}

.auth-header strong {
    color: var(--text-secondary);

    font-weight: 600;
}

/* ================================================================ */
/* Form */
/* ================================================================ */

.auth-form {
    display: flex;
    flex-direction: column;

    gap: 14px;

    text-align: right;
}

.field {
    display: flex;
    flex-direction: column;
}

.field label {
    margin-bottom: 8px;

    color: var(--text-secondary);

    font-size: 11px;
    font-weight: 700;

    line-height: 1.7;
}

/* ================================================================ */
/* OTP Input */
/* ================================================================ */

.otp-input {
    width: 100%;
    height: 58px;

    padding-inline: 14px;

    border: 1px solid var(--border);
    border-radius: 10px;

    outline: none;

    color: var(--text-primary);

    background: rgba(0, 0, 0, 0.18);

    direction: ltr;
    text-align: center;

    font-family: var(--font-mono);

    font-size: 25px;
    font-weight: 800;

    line-height: 1;

    letter-spacing: 10px;

    transition:
        border-color var(--transition),
        box-shadow var(--transition);
}

.otp-input:focus {
    border-color: var(--border-orange);

    box-shadow:
        0 0 0 3px rgba(255, 107, 0, 0.08);
}

.otp-input::placeholder {
    color: #34393e;

    letter-spacing: 9px;
}

.otp-hint {
    margin-top: 6px;

    color: var(--text-muted);

    font-size: 9px;

    line-height: 1.7;
}

.field-error {
    margin-top: 5px;

    color: var(--danger);

    font-size: 10px;

    line-height: 1.7;
}

/* ================================================================ */
/* Messages */
/* ================================================================ */

.message {
    margin-bottom: 14px;

    padding: 10px;

    border-radius: 7px;

    text-align: right;

    font-size: 10px;

    line-height: 1.8;
}

.message-error {
    color: var(--danger);

    border: 1px solid rgba(255, 92, 92, 0.15);

    background: rgba(255, 92, 92, 0.025);
}

.message-success {
    color: var(--success);

    border: 1px solid rgba(55, 214, 122, 0.15);

    background: rgba(55, 214, 122, 0.025);
}

/* ================================================================ */
/* Buttons */
/* ================================================================ */

.submit-button {
    min-height: 46px;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

    color: #fff;

    border: 0;
    border-radius: 8px;

    background: var(--orange);

    font-family: inherit;

    font-size: 12px;
    font-weight: 800;

    line-height: 1.7;

    cursor: pointer;

    transition:
        background var(--transition),
        transform var(--transition);
}

.submit-button:hover:not(:disabled) {
    background: var(--orange-light);

    transform: translateY(-2px);
}

.submit-button:disabled {
    opacity: 0.45;

    cursor: not-allowed;
}

.back-button {
    min-height: 38px;

    color: var(--text-muted);

    border: 0;

    background: transparent;

    font-family: inherit;

    font-size: 10px;
    font-weight: 600;

    cursor: pointer;

    transition:
        color var(--transition);
}

.back-button:hover {
    color: var(--orange);
}

.auth-logo:focus-visible,
.submit-button:focus-visible,
.back-button:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 4px;
}

/* ================================================================ */
/* Loader */
/* ================================================================ */

.loader {
    animation:
        spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* ================================================================ */
/* Responsive */
/* ================================================================ */

@media (max-width: 480px) {
    .auth-page {
        padding-inline: 14px;
    }

    .auth-card {
        padding: 22px 18px;
    }

    .otp-input {
        height: 54px;

        font-size: 22px;

        letter-spacing: 8px;
    }

    .otp-input::placeholder {
        letter-spacing: 7px;
    }
}

/* ================================================================ */
/* Reduced Motion */
/* ================================================================ */

@media (prefers-reduced-motion: reduce) {
    .otp-input,
    .submit-button,
    .back-button {
        transition: none;
    }

    .submit-button:hover:not(:disabled) {
        transform: none;
    }

    .loader {
        animation: none;
    }
}
</style>
