<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import {
    CheckCircle2,
    LockKeyhole,
    Mail,
    Phone,
    Save,
    UserRound,
    LoaderCircle,
    LogOut,
} from 'lucide-vue-next'

import { useAuthStore } from '../stores/auth'
import MyRequestsSection from '../components/profile/MyRequestsSection.vue'
import MyPurchasedProjectsSection from '../components/profile/MyPurchasedProjectsSection.vue'

const router = useRouter()
const authStore = useAuthStore()

const profileForm = reactive({
    name: '',
    email: '',
    phone: '',
})

const passwordForm = reactive({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const profileMessage = ref('')
const passwordMessage = ref('')

const profileError = ref('')
const passwordError = ref('')

const loadProfile = async () => {
    if (!authStore.isAuthenticated) {
        router.replace({
            name: 'login',
        })

        return
    }

    const result = await authStore.fetchProfile()

    if (!result.success) {
        router.replace({
            name: 'login',
        })

        return
    }

    profileForm.name = authStore.user?.name || ''
    profileForm.email = authStore.user?.email || ''
    profileForm.phone = authStore.user?.phone || ''
}

const updateProfile = async () => {
    profileMessage.value = ''
    profileError.value = ''

    const result = await authStore.updateProfile({
        name: profileForm.name,
        email: profileForm.email,
        phone: profileForm.phone || null,
    })

    if (!result.success) {
        profileError.value =
            authStore.errorMessage ||
            'به‌روزرسانی پروفایل انجام نشد.'

        return
    }

    profileMessage.value =
        result.message ||
        'پروفایل با موفقیت به‌روزرسانی شد.'
}

const changePassword = async () => {
    passwordMessage.value = ''
    passwordError.value = ''

    const result = await authStore.changePassword({
        current_password: passwordForm.current_password,
        password: passwordForm.password,
        password_confirmation:
            passwordForm.password_confirmation,
    })

    if (!result.success) {
        passwordError.value =
            authStore.errorMessage ||
            'تغییر رمز عبور انجام نشد.'

        return
    }

    passwordMessage.value =
        result.message ||
        'رمز عبور با موفقیت تغییر کرد.'

    authStore.clearAuth()

    setTimeout(() => {
        router.replace({
            name: 'login',
        })
    }, 900)
}

const logout = async () => {
    await authStore.logout()

    router.push({
        name: 'home',
    })
}

onMounted(loadProfile)
</script>

<template>
    <main
        class="profile-page"
        aria-labelledby="profile-title"
    >
        <div class="container">

            <!-- Header -->
            <header class="profile-header">
                <div>
                    <span class="profile-kicker">
                        ACCOUNT
                    </span>

                    <h1 id="profile-title">
                        پروفایل کاربری
                    </h1>

                    <p>
                        اطلاعات حساب و تنظیمات امنیتی خود را
                        مدیریت کنید.
                    </p>
                </div>

                <button
                    type="button"
                    class="logout-button"
                    @click="logout"
                >
                    <LogOut
                        :size="16"
                        aria-hidden="true"
                    />

                    <span>خروج</span>
                </button>
            </header>

            <!-- Account Settings -->
            <div class="profile-grid">

                <!-- Personal Information -->
                <section
                    class="profile-card"
                    aria-labelledby="personal-info-title"
                >
                    <header class="card-header">
                        <div
                            class="card-icon"
                            aria-hidden="true"
                        >
                            <UserRound :size="18" />
                        </div>

                        <div>
                            <h2 id="personal-info-title">
                                اطلاعات شخصی
                            </h2>

                            <p>
                                اطلاعات حساب خود را به‌روزرسانی کنید.
                            </p>
                        </div>
                    </header>

                    <div
                        v-if="profileMessage"
                        class="message success"
                        role="status"
                        aria-live="polite"
                    >
                        <CheckCircle2
                            :size="16"
                            aria-hidden="true"
                        />

                        <span>
                            {{ profileMessage }}
                        </span>
                    </div>

                    <div
                        v-if="profileError"
                        class="message error"
                        role="alert"
                        aria-live="assertive"
                    >
                        {{ profileError }}
                    </div>

                    <form
                        class="profile-form"
                        @submit.prevent="updateProfile"
                    >
                        <!-- Name -->
                        <div class="field">
                            <label for="profile-name">
                                نام و نام خانوادگی
                            </label>

                            <div class="input">
                                <UserRound
                                    :size="16"
                                    aria-hidden="true"
                                />

                                <input
                                    id="profile-name"
                                    v-model="profileForm.name"
                                    type="text"
                                    autocomplete="name"
                                    required
                                    :aria-invalid="
                                        Boolean(authStore.errors.name)
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
                            <label for="profile-email">
                                ایمیل
                            </label>

                            <div class="input">
                                <Mail
                                    :size="16"
                                    aria-hidden="true"
                                />

                                <input
                                    id="profile-email"
                                    v-model="profileForm.email"
                                    type="email"
                                    autocomplete="email"
                                    inputmode="email"
                                    dir="ltr"
                                    required
                                    :aria-invalid="
                                        Boolean(authStore.errors.email)
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
                            <label for="profile-phone">
                                شماره تماس
                                <small>(اختیاری)</small>
                            </label>

                            <div class="input">
                                <Phone
                                    :size="16"
                                    aria-hidden="true"
                                />

                                <input
                                    id="profile-phone"
                                    v-model="profileForm.phone"
                                    type="tel"
                                    autocomplete="tel"
                                    inputmode="tel"
                                    dir="ltr"
                                    maxlength="30"
                                    :aria-invalid="
                                        Boolean(authStore.errors.phone)
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

                        <button
                            type="submit"
                            class="save-button"
                            :disabled="authStore.loading"
                            :aria-busy="authStore.loading"
                        >
                            <span v-if="!authStore.loading">
                                ذخیره تغییرات
                            </span>

                            <LoaderCircle
                                v-else
                                :size="18"
                                class="loader"
                                aria-hidden="true"
                            />

                            <Save
                                v-if="!authStore.loading"
                                :size="16"
                                aria-hidden="true"
                            />
                        </button>
                    </form>
                </section>

                <!-- Password -->
                <section
                    class="profile-card"
                    aria-labelledby="password-title"
                >
                    <header class="card-header">
                        <div
                            class="card-icon"
                            aria-hidden="true"
                        >
                            <LockKeyhole :size="18" />
                        </div>

                        <div>
                            <h2 id="password-title">
                                تغییر رمز عبور
                            </h2>

                            <p>
                                رمز عبور حساب خود را تغییر دهید.
                            </p>
                        </div>
                    </header>

                    <div
                        v-if="passwordMessage"
                        class="message success"
                        role="status"
                        aria-live="polite"
                    >
                        <CheckCircle2
                            :size="16"
                            aria-hidden="true"
                        />

                        <span>
                            {{ passwordMessage }}
                        </span>
                    </div>

                    <div
                        v-if="passwordError"
                        class="message error"
                        role="alert"
                        aria-live="assertive"
                    >
                        {{ passwordError }}
                    </div>

                    <form
                        class="profile-form"
                        @submit.prevent="changePassword"
                    >
                        <!-- Current Password -->
                        <div class="field">
                            <label for="current-password">
                                رمز عبور فعلی
                            </label>

                            <div class="input">
                                <LockKeyhole
                                    :size="16"
                                    aria-hidden="true"
                                />

                                <input
                                    id="current-password"
                                    v-model="
                                        passwordForm.current_password
                                    "
                                    type="password"
                                    autocomplete="current-password"
                                    required
                                    :aria-invalid="
                                        Boolean(
                                            authStore.errors
                                                .current_password
                                        )
                                    "
                                />
                            </div>

                            <span
                                v-if="
                                    authStore.errors.current_password
                                "
                                class="field-error"
                            >
                                {{
                                    authStore.errors
                                        .current_password[0]
                                }}
                            </span>
                        </div>

                        <!-- New Password -->
                        <div class="field">
                            <label for="new-password">
                                رمز عبور جدید
                            </label>

                            <div class="input">
                                <LockKeyhole
                                    :size="16"
                                    aria-hidden="true"
                                />

                                <input
                                    id="new-password"
                                    v-model="
                                        passwordForm.password
                                    "
                                    type="password"
                                    autocomplete="new-password"
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
                                v-if="
                                    authStore.errors.password
                                "
                                class="field-error"
                            >
                                {{
                                    authStore.errors.password[0]
                                }}
                            </span>
                        </div>

                        <!-- Confirmation -->
                        <div class="field">
                            <label for="password-confirmation">
                                تکرار رمز عبور جدید
                            </label>

                            <div class="input">
                                <LockKeyhole
                                    :size="16"
                                    aria-hidden="true"
                                />

                                <input
                                    id="password-confirmation"
                                    v-model="
                                        passwordForm.password_confirmation
                                    "
                                    type="password"
                                    autocomplete="new-password"
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

                        <button
                            type="submit"
                            class="save-button"
                            :disabled="authStore.loading"
                            :aria-busy="authStore.loading"
                        >
                            <span v-if="!authStore.loading">
                                تغییر رمز عبور
                            </span>

                            <LoaderCircle
                                v-else
                                :size="18"
                                class="loader"
                                aria-hidden="true"
                            />

                            <LockKeyhole
                                v-if="!authStore.loading"
                                :size="16"
                                aria-hidden="true"
                            />
                        </button>
                    </form>
                </section>

            </div>

            <!-- Requests & Purchased Projects -->
            <div class="profile-sections">
                <MyRequestsSection />
                <MyPurchasedProjectsSection />
            </div>

        </div>
    </main>
</template>

<style scoped>
.profile-page {
    min-height: 100vh;

    padding: 65px 0 100px;

    background:
        radial-gradient(
            circle at 80% 0%,
            rgba(255, 107, 0, 0.045),
            transparent 28rem
        ),
        var(--bg-primary);
}

.profile-header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;

    gap: 25px;

    margin-bottom: 35px;
}

.profile-kicker {
    display: inline-block;

    color: var(--orange);

    direction: ltr;

    font-family: var(--font-mono);
    font-size: 10px;
    font-weight: 600;
    line-height: 1.7;
    letter-spacing: 0.03em;
}

.profile-header h1 {
    margin-top: 8px;

    color: var(--text-primary);

    font-size: clamp(30px, 4vw, 45px);
    font-weight: 900;
    line-height: 1.35;
    letter-spacing: -0.025em;
}

.profile-header p {
    max-width: 500px;

    margin-top: 8px;

    color: var(--text-muted);

    font-size: 12px;
    line-height: 2;
}

.logout-button {
    min-height: 41px;

    display: inline-flex;
    align-items: center;

    gap: 7px;

    padding-inline: 14px;

    color: #e87979;

    border: 1px solid rgba(255, 92, 92, 0.14);
    border-radius: 8px;

    background: rgba(255, 92, 92, 0.025);

    font-family: inherit;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.7;

    cursor: pointer;

    transition:
        background var(--transition),
        border-color var(--transition);
}

.logout-button:hover {
    border-color: rgba(255, 92, 92, 0.3);

    background: rgba(255, 92, 92, 0.06);
}

.logout-button:focus-visible,
.save-button:focus-visible {
    outline: 2px solid var(--orange);
    outline-offset: 4px;
}

.profile-grid {
    display: grid;

    grid-template-columns: repeat(2, minmax(0, 1fr));

    gap: 14px;
}

.profile-card {
    padding: 24px;

    border: 1px solid var(--border);
    border-radius: 14px;

    background:
        linear-gradient(
            145deg,
            rgba(255, 255, 255, 0.025),
            rgba(255, 255, 255, 0.008)
        );

    box-shadow:
        0 20px 60px rgba(0, 0, 0, 0.2);
}

.card-header {
    display: flex;
    align-items: center;

    gap: 11px;

    margin-bottom: 25px;
}

.card-icon {
    width: 42px;
    height: 42px;

    display: flex;
    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    color: var(--orange);

    border: 1px solid rgba(255, 107, 0, 0.18);
    border-radius: 9px;

    background: var(--orange-soft);
}

.card-header h2 {
    color: var(--text-primary);

    font-size: 15px;
    font-weight: 800;
    line-height: 1.7;
}

.card-header p {
    margin-top: 2px;

    color: var(--text-muted);

    font-size: 11px;
    line-height: 1.8;
}

.profile-form {
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

    font-size: 11px;
    font-weight: 700;
    line-height: 1.7;
}

.field label small {
    color: var(--text-muted);

    font-size: 10px;
    font-weight: 400;
}

.input {
    min-height: 46px;

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

    color: #656c72;

    transition: color var(--transition);
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

.field-error {
    margin-top: 6px;

    color: var(--danger);

    font-size: 10px;
    line-height: 1.7;
}

.save-button {
    min-height: 46px;

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

.save-button:hover:not(:disabled) {
    background: var(--orange-light);

    transform: translateY(-2px);
}

.save-button:disabled {
    opacity: 0.7;

    cursor: wait;
}

.message {
    margin-bottom: 17px;

    padding: 11px 12px;

    display: flex;
    align-items: center;

    gap: 7px;

    border-radius: 7px;

    font-size: 10px;
    line-height: 1.8;
}

.message.success {
    color: var(--success);

    border: 1px solid rgba(55, 214, 122, 0.14);

    background: rgba(55, 214, 122, 0.025);
}

.message.error {
    color: var(--danger);

    border: 1px solid rgba(255, 92, 92, 0.14);

    background: rgba(255, 92, 92, 0.025);
}

.profile-sections {
    display: flex;
    flex-direction: column;

    gap: 14px;

    margin-top: 14px;
}

.loader {
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@media (max-width: 780px) {
    .profile-page {
        padding-top: 45px;
    }

    .profile-header {
        align-items: flex-start;

        flex-direction: column;
    }

    .profile-grid {
        grid-template-columns: 1fr;
    }

    .logout-button {
        align-self: flex-start;
    }
}

@media (max-width: 480px) {
    .profile-page {
        padding-bottom: 70px;
    }

    .profile-card {
        padding: 19px 16px;
    }

    .profile-header h1 {
        font-size: 31px;
    }

    .logout-button {
        width: 100%;

        justify-content: center;
    }
}

@media (prefers-reduced-motion: reduce) {
    .input svg,
    .logout-button,
    .save-button {
        transition: none;
    }

    .save-button:hover:not(:disabled) {
        transform: none;
    }

    .loader {
        animation: none;
    }
}
</style>
