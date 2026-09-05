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
    <main class="auth-page">
        <div class="auth-container">

            <RouterLink
                to="/"
                class="auth-logo"
            >
                <span>Derin</span><strong>Code</strong>
            </RouterLink>

            <div class="auth-card">

                <div class="auth-header">
                    <span class="auth-kicker">
                        CREATE ACCOUNT
                    </span>

                    <h1>
                        ایجاد حساب
                    </h1>

                    <p>
                        برای ارسال درخواست‌ها و مدیریت پروژه‌ها
                        حساب کاربری خود را ایجاد کنید.
                    </p>
                </div>

                <div
                    v-if="localError"
                    class="form-error"
                >
                    {{ localError }}
                </div>

                <form
                    class="auth-form"
                    @submit.prevent="submit"
                >
                    <!-- Name -->
                    <div class="field">
                        <label for="name">
                            نام و نام خانوادگی
                        </label>

                        <div class="input">
                            <User :size="16" />

                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                autocomplete="name"
                                placeholder="نام شما"
                                minlength="2"
                                maxlength="100"
                                required
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
                        <label for="email">
                            ایمیل
                        </label>

                        <div class="input">
                            <Mail :size="16" />

                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                autocomplete="email"
                                placeholder="example@email.com"
                                required
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
                        <label for="phone">
                            شماره تماس
                            <small>(اختیاری)</small>
                        </label>

                        <div class="input">
                            <Phone :size="16" />

                            <input
                                id="phone"
                                v-model="form.phone"
                                type="tel"
                                autocomplete="tel"
                                placeholder="09xxxxxxxxx"
                                maxlength="20"
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
                        <label for="password">
                            رمز عبور
                        </label>

                        <div class="input">
                            <LockKeyhole :size="16" />

                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                autocomplete="new-password"
                                placeholder="حداقل ۸ کاراکتر"
                                minlength="8"
                                required
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
                        <label for="password_confirmation">
                            تکرار رمز عبور
                        </label>

                        <div class="input">
                            <LockKeyhole :size="16" />

                            <input
                                id="password_confirmation"
                                v-model="
                                    form.password_confirmation
                                "
                                type="password"
                                autocomplete="new-password"
                                placeholder="تکرار رمز عبور"
                                minlength="8"
                                required
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

                    <button
                        type="submit"
                        class="submit-button"
                        :disabled="authStore.loading"
                    >
                        <span v-if="!authStore.loading">
                            ایجاد حساب
                        </span>

                        <LoaderCircle
                            v-else
                            :size="17"
                            class="loader"
                        />

                        <ArrowLeft
                            v-if="!authStore.loading"
                            :size="16"
                        />
                    </button>

                    <div class="bottom-link">
                        حساب دارید؟

                        <RouterLink to="/login">
                            وارد شوید
                        </RouterLink>
                    </div>
                </form>
            </div>
        </div>
    </main>
</template>

<style scoped>
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

.auth-logo {
    width: fit-content;

    display: block;

    margin: 0 auto 25px;

    direction: ltr;

    font-family: var(--font-mono);
    font-size: 20px;
    font-weight: 800;
}

.auth-logo span {
    color: var(--text-primary);
}

.auth-logo strong {
    color: var(--orange);
}

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

.auth-header {
    margin-bottom: 25px;
}

.auth-kicker {
    color: var(--orange);

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 8px;
}

.auth-header h1 {
    margin-top: 8px;

    color: var(--text-primary);

    font-size: 28px;
    font-weight: 900;
}

.auth-header p {
    margin-top: 6px;

    color: var(--text-muted);

    font-size: 10px;
    line-height: 1.9;
}

.auth-form {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.field {
    display: flex;
    flex-direction: column;
}

.field label {
    margin-bottom: 7px;

    color: var(--text-secondary);

    font-size: 9px;
    font-weight: 700;
}

.field label small {
    color: var(--text-muted);

    font-size: 8px;
    font-weight: 400;
}

.input {
    min-height: 45px;

    display: flex;
    align-items: center;

    gap: 9px;

    padding-inline: 11px;

    border: 1px solid var(--border);
    border-radius: 8px;

    background: rgba(0, 0, 0, 0.18);

    transition: border-color var(--transition);
}

.input:focus-within {
    border-color: var(--border-orange);
}

.input svg {
    flex-shrink: 0;

    color: #646b71;
}

.input input {
    width: 100%;

    border: 0;
    outline: 0;

    color: var(--text-primary);

    background: transparent;

    font-size: 10px;
}

.input input::placeholder {
    color: #555c62;
}

.field-error {
    margin-top: 5px;

    color: var(--danger);

    font-size: 8px;
}

.form-error {
    margin-bottom: 15px;

    padding: 10px 11px;

    color: var(--danger);

    border: 1px solid rgba(255, 92, 92, 0.15);
    border-radius: 7px;

    background: rgba(255, 92, 92, 0.025);

    font-size: 8px;
}

.submit-button {
    min-height: 46px;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

    margin-top: 2px;

    color: #fff;

    border-radius: 8px;

    background: var(--orange);

    font-size: 10px;
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

.bottom-link {
    margin-top: 4px;

    color: var(--text-muted);

    text-align: center;

    font-size: 8px;
}

.bottom-link a {
    margin-right: 4px;

    color: var(--orange);

    transition: color var(--transition);
}

.bottom-link a:hover {
    color: var(--orange-light);
}

.loader {
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@media (max-width: 480px) {
    .auth-page {
        padding-inline: 14px;
    }

    .auth-card {
        padding: 22px 18px;
    }
}
</style>
