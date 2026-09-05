<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import {
    ArrowLeft,
    LockKeyhole,
    CheckCircle2,
    LoaderCircle,
} from 'lucide-vue-next'

import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
    password: '',
    password_confirmation: '',
})

const localError = ref('')
const successMessage = ref('')

const submit = async () => {
    localError.value = ''
    successMessage.value = ''

    if (
        !authStore.forgotEmail ||
        !authStore.verifiedOtp
    ) {
        router.replace({
            name: 'forgot-password',
        })

        return
    }

    const result = await authStore.resetPassword({
        email: authStore.forgotEmail,
        otp: authStore.verifiedOtp,
        password: form.password,
        password_confirmation:
            form.password_confirmation,
    })

    if (!result.success) {
        localError.value =
            authStore.errorMessage ||
            'تغییر رمز عبور انجام نشد.'

        return
    }

    successMessage.value = result.message

    setTimeout(() => {
        router.push({
            name: 'login',
        })
    }, 1200)
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

                <div class="reset-icon">
                    <LockKeyhole :size="24" />
                </div>

                <div class="auth-header">
                    <span class="auth-kicker">
                        NEW PASSWORD
                    </span>

                    <h1>
                        رمز عبور جدید
                    </h1>

                    <p>
                        یک رمز عبور جدید و حداقل ۸ کاراکتری
                        برای حساب خود انتخاب کنید.
                    </p>
                </div>

                <div
                    v-if="localError"
                    class="message message-error"
                >
                    {{ localError }}
                </div>

                <div
                    v-if="successMessage"
                    class="message message-success"
                >
                    <CheckCircle2 :size="15" />
                    {{ successMessage }}
                </div>

                <form
                    class="auth-form"
                    @submit.prevent="submit"
                >
                    <div class="field">
                        <label for="password">
                            رمز عبور جدید
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

                    <div class="field">
                        <label for="password-confirmation">
                            تکرار رمز عبور
                        </label>

                        <div class="input">
                            <LockKeyhole :size="16" />

                            <input
                                id="password-confirmation"
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
                                authStore.errors.password_confirmation
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
                            تغییر رمز عبور
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

    margin: 0 auto 22px;

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
    padding: 27px 28px;

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

.reset-icon {
    width: 52px;
    height: 52px;

    display: flex;
    align-items: center;
    justify-content: center;

    color: var(--orange);

    border: 1px solid var(--border-orange);
    border-radius: 13px;

    background: var(--orange-soft);
}

.auth-header {
    margin-top: 18px;
    margin-bottom: 23px;
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

    font-size: 27px;
    font-weight: 900;
}

.auth-header p {
    margin-top: 7px;

    color: var(--text-muted);

    font-size: 10px;
    line-height: 1.9;
}

.auth-form {
    display: flex;
    flex-direction: column;

    gap: 16px;
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

.submit-button {
    min-height: 46px;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

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

.message {
    margin-bottom: 15px;

    padding: 10px 11px;

    display: flex;
    align-items: center;
    gap: 7px;

    border-radius: 7px;

    font-size: 8px;
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
    .auth-card {
        padding: 22px 18px;
    }
}
</style>
