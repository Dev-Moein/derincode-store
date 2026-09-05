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

        return
    }

    successMessage.value = result.message

    setTimeout(() => {
        router.push({
            name: 'reset-password',
        })
    }, 700)
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

                <div class="otp-icon">
                    <ShieldCheck :size="25" />
                </div>

                <div class="auth-header">
                    <span class="auth-kicker">
                        VERIFICATION
                    </span>

                    <h1>
                        تأیید کد
                    </h1>

                    <p>
                        کد ۶ رقمی ارسال‌شده به
                        <strong>{{ email }}</strong>
                        را وارد کنید.
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
                            maxlength="6"
                            autocomplete="one-time-code"
                            placeholder="------"
                            required
                        />

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
                    >
                        <span v-if="!authStore.loading">
                            تأیید کد
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

                    <button
                        type="button"
                        class="back-button"
                        @click="
                            router.push({
                                name: 'forgot-password',
                            })
                        "
                    >
                        تغییر ایمیل
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

.auth-header {
    margin-top: 19px;
    margin-bottom: 23px;
}

.auth-kicker {
    color: var(--orange);

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 8px;
}

.auth-header h1 {
    margin-top: 7px;

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

.auth-header strong {
    color: var(--text-secondary);
    direction: ltr;
}

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

    font-size: 9px;
    font-weight: 700;
}

.otp-input {
    width: 100%;

    height: 58px;

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

    letter-spacing: 10px;

    transition: border-color var(--transition);
}

.otp-input:focus {
    border-color: var(--border-orange);
}

.otp-input::placeholder {
    color: #34393e;
    letter-spacing: 9px;
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
    opacity: 0.45;
    cursor: not-allowed;
}

.back-button {
    min-height: 38px;

    color: var(--text-muted);

    background: transparent;

    font-size: 9px;

    cursor: pointer;

    transition: color var(--transition);
}

.back-button:hover {
    color: var(--orange);
}

.message {
    margin-bottom: 14px;

    padding: 10px;

    border-radius: 7px;

    text-align: right;

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

    .otp-input {
        height: 54px;
        font-size: 22px;
    }
}
</style>
