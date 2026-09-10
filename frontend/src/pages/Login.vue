<script setup>
import { reactive, ref } from 'vue'
import {
    useRoute,
    useRouter,
} from 'vue-router'
import {
    ArrowLeft,
    Mail,
    LockKeyhole,
    LoaderCircle,
} from 'lucide-vue-next'

import { useAuthStore } from '../stores/auth'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const form = reactive({
    email: '',
    password: '',
})

const localError = ref('')

const submit = async () => {
    localError.value = ''

    const result = await authStore.login({
        email: form.email,
        password: form.password,
    })

    if (!result.success) {
        localError.value =
            authStore.errorMessage ||
            'اطلاعات ورود صحیح نیست.'

        return
    }

    const redirectQuery = route.query.redirect

    const redirect =
        typeof redirectQuery === 'string' &&
        redirectQuery.startsWith('/') &&
        !redirectQuery.startsWith('//')
            ? redirectQuery
            : '/'

    await router.push(redirect)
}
</script>

<template>
    <main
        class="auth-page"
        aria-labelledby="login-title"
    >
        <div class="auth-container">

            <RouterLink
                to="/"
                class="auth-logo"
                aria-label="Derin Code - صفحه اصلی"
            >
                <span>Derin</span><strong>Code</strong>
            </RouterLink>

            <section class="auth-card">

                <header class="auth-header">
                    <span class="auth-kicker">
                        Welcome Back
                    </span>

                    <h1 id="login-title">
                        ورود به حساب
                    </h1>

                    <p>
                        برای ادامه وارد حساب کاربری خود شوید.
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
                    <div class="field">
                        <label for="email">
                            ایمیل
                        </label>

                        <div class="input">
                            <Mail
                                :size="17"
                                aria-hidden="true"
                            />

                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                autocomplete="email"
                                inputmode="email"
                                dir="ltr"
                                placeholder="example@email.com"
                                :aria-invalid="
                                    Boolean(authStore.errors.email)
                                "
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

                    <div class="field">
                        <label for="password">
                            رمز عبور
                        </label>

                        <div class="input">
                            <LockKeyhole
                                :size="17"
                                aria-hidden="true"
                            />

                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                autocomplete="current-password"
                                placeholder="رمز عبور"
                                :aria-invalid="
                                    Boolean(authStore.errors.password)
                                "
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

                    <div class="form-options">
                        <RouterLink to="/forgot-password">
                            فراموشی رمز عبور؟
                        </RouterLink>

                        <RouterLink to="/register">
                            ایجاد حساب
                        </RouterLink>
                    </div>

                    <button
                        type="submit"
                        class="submit-button"
                        :disabled="authStore.loading"
                        :aria-busy="authStore.loading"
                    >
                        <span v-if="!authStore.loading">
                            ورود
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
                </form>

            </section>
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

.auth-logo:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 6px;
    border-radius: 4px;
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
    display: inline-block;

    color: var(--orange);

    direction: ltr;

    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 600;
    line-height: 1.7;
    letter-spacing: 0.03em;
}

.auth-header h1 {
    margin-top: 8px;

    color: var(--text-primary);

    font-size: 28px;
    font-weight: 900;
    line-height: 1.45;
    letter-spacing: -0.02em;
}

.auth-header p {
    margin-top: 7px;

    color: var(--text-muted);

    font-size: 12px;
    line-height: 2;
}

.auth-form {
    display: flex;
    flex-direction: column;
    gap: 17px;
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

.input {
    min-height: 47px;

    display: flex;
    align-items: center;

    gap: 10px;

    padding-inline: 12px;

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

    transition: color var(--transition);
}

.input:focus-within svg {
    color: var(--orange);
}

.input input {
    width: 100%;

    outline: none;
    border: 0;

    color: var(--text-primary);

    background: transparent;

    font-family: inherit;
    font-size: 12px;
    line-height: 1.7;
}

.input input::placeholder {
    color: #545b60;
}

.field-error {
    margin-top: 6px;

    color: var(--danger);

    font-size: 10px;
    line-height: 1.7;
}

.form-options {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-top: -3px;
}

.form-options a {
    color: var(--text-muted);

    font-size: 10px;
    font-weight: 500;
    line-height: 1.7;

    text-decoration: none;

    transition: color var(--transition);
}

.form-options a:hover {
    color: var(--orange);
}

.form-options a:last-child {
    color: var(--orange);
}

.form-options a:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 4px;
    border-radius: 3px;
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

.submit-button {
    min-height: 47px;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

    margin-top: 3px;

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

.submit-button:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 4px;
}

.submit-button:disabled {
    opacity: 0.7;

    cursor: wait;
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
        padding: 23px 18px;
    }

    .auth-header h1 {
        font-size: 25px;
    }

    .form-options a {
        font-size: 10px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .loader {
        animation: none;
    }

    .input svg,
    .submit-button {
        transition: none;
    }

    .submit-button:hover:not(:disabled) {
        transform: none;
    }
}
</style>
