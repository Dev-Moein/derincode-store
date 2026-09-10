<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import {
    ArrowLeft,
    Mail,
    LockKeyhole,
    User,
    Phone,
    LoaderCircle,
} from 'lucide-vue-next'

import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
    name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
})

const localError = ref('')

const submit = async () => {
    localError.value = ''

    const result = await authStore.register({
        name: form.name,
        email: form.email,
        phone: form.phone || null,
        password: form.password,
        password_confirmation: form.password_confirmation,
    })

    if (!result.success) {
        localError.value =
            authStore.errorMessage ||
            'ثبت‌نام انجام نشد.'

        return
    }

    router.push({
        name: 'profile',
    })
}
</script>

<template>
    <main
        class="auth-page"
        aria-labelledby="register-title"
    >
        <div class="auth-container">

            <RouterLink
                to="/"
                class="auth-logo"
                aria-label="Derin Code - صفحه اصلی"
            >
                <span>Derin</span><strong>Code</strong>
            </RouterLink>

            <section
                class="auth-card"
                aria-labelledby="register-title"
            >

                <header class="auth-header">
                    <span class="auth-kicker">
                        CREATE ACCOUNT
                    </span>

                    <h1 id="register-title">
                        ایجاد حساب
                    </h1>

                    <p>
                        برای ارسال درخواست‌ها و مدیریت پروژه‌ها
                        حساب کاربری خود را ایجاد کنید.
                    </p>
                </header>

                <div
                    v-if="localError"
                    class="form-error"
                    role="alert"
                    aria-live="assertive"
                >
                    {{ localError }}
                </div>

                <form
                    class="auth-form"
                    @submit.prevent="submit"
                >
                    <!-- Name -->
                    <div class="field">
                        <label for="register-name">
                            نام و نام خانوادگی
                        </label>

                        <div class="input">
                            <User
                                :size="17"
                                aria-hidden="true"
                            />

                            <input
                                id="register-name"
                                v-model="form.name"
                                type="text"
                                autocomplete="name"
                                placeholder="نام شما"
                                minlength="2"
                                maxlength="100"
                                required
                                :aria-invalid="
                                    Boolean(
                                        authStore.errors.name
                                    )
                                "
                            />
                        </div>

                        <span
                            v-if="authStore.errors.name"
                            class="field-error"
                        >
                            {{ authStore.errors.name[0] }}
                        </span>
                    </div>

                    <!-- Email -->
                    <div class="field">
                        <label for="register-email">
                            ایمیل
                        </label>

                        <div class="input">
                            <Mail
                                :size="17"
                                aria-hidden="true"
                            />

                            <input
                                id="register-email"
                                v-model="form.email"
                                type="email"
                                autocomplete="email"
                                inputmode="email"
                                dir="ltr"
                                placeholder="example@email.com"
                                required
                                :aria-invalid="
                                    Boolean(
                                        authStore.errors.email
                                    )
                                "
                            />
                        </div>

                        <span
                            v-if="authStore.errors.email"
                            class="field-error"
                        >
                            {{ authStore.errors.email[0] }}
                        </span>
                    </div>

                    <!-- Phone -->
                    <div class="field">
                        <label for="register-phone">
                            شماره تماس
                            <small>(اختیاری)</small>
                        </label>

                        <div class="input">
                            <Phone
                                :size="17"
                                aria-hidden="true"
                            />

                            <input
                                id="register-phone"
                                v-model="form.phone"
                                type="tel"
                                autocomplete="tel"
                                inputmode="tel"
                                dir="ltr"
                                placeholder="09xxxxxxxxx"
                                maxlength="20"
                                :aria-invalid="
                                    Boolean(
                                        authStore.errors.phone
                                    )
                                "
                            />
                        </div>

                        <span
                            v-if="authStore.errors.phone"
                            class="field-error"
                        >
                            {{ authStore.errors.phone[0] }}
                        </span>
                    </div>

                    <!-- Password -->
                    <div class="field">
                        <label for="register-password">
                            رمز عبور
                        </label>

                        <div class="input">
                            <LockKeyhole
                                :size="17"
                                aria-hidden="true"
                            />

                            <input
                                id="register-password"
                                v-model="form.password"
                                type="password"
                                autocomplete="new-password"
                                placeholder="حداقل ۸ کاراکتر"
                                minlength="8"
                                required
                                :aria-invalid="
                                    Boolean(
                                        authStore.errors.password
                                    )
                                "
                            />
                        </div>

                        <span
                            v-if="authStore.errors.password"
                            class="field-error"
                        >
                            {{ authStore.errors.password[0] }}
                        </span>
                    </div>

                    <!-- Password confirmation -->
                    <div class="field">
                        <label for="register-password-confirmation">
                            تکرار رمز عبور
                        </label>

                        <div class="input">
                            <LockKeyhole
                                :size="17"
                                aria-hidden="true"
                            />

                            <input
                                id="register-password-confirmation"
                                v-model="
                                    form.password_confirmation
                                "
                                type="password"
                                autocomplete="new-password"
                                placeholder="تکرار رمز عبور"
                                minlength="8"
                                required
                                :aria-invalid="
                                    Boolean(
                                        authStore.errors
                                            .password_confirmation
                                    )
                                "
                            />
                        </div>

                        <span
                            v-if="
                                authStore.errors
                                    .password_confirmation
                            "
                            class="field-error"
                        >
                            {{
                                authStore.errors
                                    .password_confirmation[0]
                            }}
                        </span>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        class="submit-button"
                        :disabled="authStore.loading"
                        :aria-busy="authStore.loading"
                    >
                        <span v-if="!authStore.loading">
                            ایجاد حساب
                        </span>

                        <LoaderCircle
                            v-else
                            :size="18"
                            class="loader"
                            aria-hidden="true"
                        />

                        <ArrowLeft
                            v-if="!authStore.loading"
                            :size="17"
                            aria-hidden="true"
                        />
                    </button>

                    <p class="bottom-link">
                        حساب دارید؟

                        <RouterLink to="/login">
                            وارد شوید
                        </RouterLink>
                    </p>
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

    margin: 0 auto 25px;

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
    padding: 28px;

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
/* Header */
/* ================================================================ */

.auth-header {
    margin-bottom: 25px;
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
    margin-top: 8px;

    color: var(--text-primary);

    font-size: 29px;
    font-weight: 900;

    line-height: 1.4;
}

.auth-header p {
    max-width: 350px;

    margin-top: 7px;

    color: var(--text-muted);

    font-size: 12px;

    line-height: 2;
}

/* ================================================================ */
/* Form */
/* ================================================================ */

.auth-form {
    display: flex;

    flex-direction: column;

    gap: 15px;
}

.field {
    display: flex;

    flex-direction: column;
}

.field label {
    margin-bottom: 7px;

    color: var(--text-secondary);

    font-size: 11px;
    font-weight: 700;

    line-height: 1.7;
}

.field label small {
    color: var(--text-muted);

    font-size: 10px;
    font-weight: 400;
}

/* ================================================================ */
/* Input */
/* ================================================================ */

.input {
    min-height: 46px;

    display: flex;

    align-items: center;

    gap: 9px;

    padding-inline: 11px;

    border: 1px solid var(--border);
    border-radius: 8px;

    background: rgba(0, 0, 0, 0.18);

    transition:
        border-color var(--transition);
}

.input:focus-within {
    border-color: var(--border-orange);
}

.input svg {
    flex-shrink: 0;

    color: #646b71;

    transition:
        color var(--transition);
}

.input:focus-within svg {
    color: var(--orange);
}

.input input {
    width: 100%;
    min-width: 0;

    border: 0;
    outline: 0;

    color: var(--text-primary);

    background: transparent;

    font-family: inherit;
    font-size: 12px;

    line-height: 1.7;
}

.input input::placeholder {
    color: #555c62;
}

/* ================================================================ */
/* Errors */
/* ================================================================ */

.field-error {
    margin-top: 6px;

    color: var(--danger);

    font-size: 10px;

    line-height: 1.7;
}

.form-error {
    margin-bottom: 15px;

    padding: 11px 12px;

    color: var(--danger);

    border: 1px solid rgba(255, 92, 92, 0.15);
    border-radius: 7px;

    background: rgba(255, 92, 92, 0.025);

    font-size: 10px;

    line-height: 1.8;
}

/* ================================================================ */
/* Submit */
/* ================================================================ */

.submit-button {
    min-height: 47px;

    display: flex;

    align-items: center;
    justify-content: center;

    gap: 8px;

    margin-top: 2px;

    color: #fff;

    border: 0;
    border-radius: 8px;

    background: var(--orange);

    font-family: inherit;
    font-size: 12px;
    font-weight: 800;

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
    opacity: 0.7;

    cursor: wait;
}

/* ================================================================ */
/* Bottom Link */
/* ================================================================ */

.bottom-link {
    margin-top: 4px;

    color: var(--text-muted);

    text-align: center;

    font-size: 10px;

    line-height: 1.8;
}

.bottom-link a {
    margin-right: 4px;

    color: var(--orange);

    font-weight: 600;

    transition:
        color var(--transition);
}

.bottom-link a:hover {
    color: var(--orange-light);
}

/* ================================================================ */
/* Focus */
/* ================================================================ */

.auth-logo:focus-visible,
.bottom-link a:focus-visible,
.submit-button:focus-visible {
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
        padding: 28px 14px;
    }

    .auth-card {
        padding: 22px 18px;
    }

    .auth-header h1 {
        font-size: 27px;
    }
}

/* ================================================================ */
/* Reduced Motion */
/* ================================================================ */

@media (prefers-reduced-motion: reduce) {
    .input,
    .input svg,
    .submit-button,
    .bottom-link a {
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
