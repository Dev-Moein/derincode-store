<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import {
    ArrowLeft,
    Mail,
    LoaderCircle,
} from 'lucide-vue-next'

import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
    email: '',
})

const localError = ref('')
const successMessage = ref('')

const submit = async () => {
    localError.value = ''
    successMessage.value = ''

    const result = await authStore.forgotPassword(form.email)

    if (!result.success) {
        localError.value =
            authStore.errorMessage ||
            'ارسال کد انجام نشد.'

        return
    }

    successMessage.value = result.message

    setTimeout(() => {
        router.push({
            name: 'verify-otp',
        })
    }, 900)
}
</script>

<template>
    <main
        class="auth-page"
        aria-labelledby="forgot-password-title"
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

                <button
                    type="button"
                    class="back-link"
                    @click="router.push({ name: 'login' })"
                >
                    <ArrowLeft
                        :size="15"
                        aria-hidden="true"
                    />

                    <span>بازگشت به ورود</span>
                </button>

                <header class="auth-header">
                    <span class="auth-kicker">
                        PASSWORD RECOVERY
                    </span>

                    <h1 id="forgot-password-title">
                        فراموشی رمز عبور
                    </h1>

                    <p>
                        ایمیل حساب خود را وارد کنید تا کد تأیید
                        برای شما ارسال شود.
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

                    <button
                        type="submit"
                        class="submit-button"
                        :disabled="authStore.loading"
                        :aria-busy="authStore.loading"
                    >
                        <span v-if="!authStore.loading">
                            ارسال کد
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

    margin: 0 auto 24px;

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

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 7px;

    padding: 3px 0;

    color: var(--text-muted);

    background: transparent;

    font-family: inherit;
    font-size: 11px;
    font-weight: 500;
    line-height: 1.7;

    cursor: pointer;

    transition: color var(--transition);
}

.back-link:hover {
    color: var(--orange);
}

.back-link:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 5px;
    border-radius: 4px;
}

.auth-header {
    margin-top: 23px;
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
    max-width: 350px;

    margin-top: 8px;

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
}

.input input {
    width: 100%;

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

.input:focus-within svg {
    color: var(--orange);
}

.field-error {
    margin-top: 6px;

    color: var(--danger);

    font-size: 10px;
    line-height: 1.7;
}

.submit-button {
    min-height: 47px;

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

.submit-button:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 4px;
}

.submit-button:disabled {
    opacity: 0.7;
    cursor: wait;
}

.message {
    margin-bottom: 16px;

    padding: 11px 12px;

    border-radius: 7px;

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
        padding: 28px 16px;
    }

    .auth-card {
        padding: 23px 18px;
    }

    .auth-header h1 {
        font-size: 25px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .loader {
        animation: none;
    }

    .back-link,
    .input,
    .submit-button {
        transition: none;
    }

    .submit-button:hover:not(:disabled) {
        transform: none;
    }
}
</style>
