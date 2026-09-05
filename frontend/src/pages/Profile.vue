<script setup>
import { onMounted, reactive, ref } from 'vue'
import MyRequestsSection from '../components/profile/MyRequestsSection.vue'

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

    profileForm.name =
        authStore.user?.name || ''

    profileForm.email =
        authStore.user?.email || ''

    profileForm.phone =
        authStore.user?.phone || ''
}

const updateProfile = async () => {
    profileMessage.value = ''
    profileError.value = ''

    const result =
        await authStore.updateProfile({
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

    const result =
        await authStore.changePassword({
            current_password:
                passwordForm.current_password,

            password:
                passwordForm.password,

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
    <main class="profile-page">
        <div class="container">

            <!-- Header -->
            <div class="profile-header">
                <div>
                    <span class="profile-kicker">
                        ACCOUNT
                    </span>

                    <h1>
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
                    <LogOut :size="15" />
                    خروج
                </button>
            </div>

            <!-- Main Grid -->
            <div class="profile-grid">

                <!-- Personal Information -->
                <section class="profile-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <UserRound :size="18" />
                        </div>

                        <div>
                            <h2>
                                اطلاعات شخصی
                            </h2>

                            <p>
                                اطلاعات حساب خود را به‌روزرسانی کنید.
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="profileMessage"
                        class="message success"
                    >
                        <CheckCircle2 :size="15" />
                        {{ profileMessage }}
                    </div>

                    <div
                        v-if="profileError"
                        class="message error"
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
                                <UserRound :size="15" />

                                <input
                                    id="profile-name"
                                    v-model="profileForm.name"
                                    type="text"
                                    autocomplete="name"
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
                            <label for="profile-email">
                                ایمیل
                            </label>

                            <div class="input">
                                <Mail :size="15" />

                                <input
                                    id="profile-email"
                                    v-model="profileForm.email"
                                    type="email"
                                    autocomplete="email"
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
                            <label for="profile-phone">
                                شماره تماس
                                <small>(اختیاری)</small>
                            </label>

                            <div class="input">
                                <Phone :size="15" />

                                <input
                                    id="profile-phone"
                                    v-model="profileForm.phone"
                                    type="tel"
                                    autocomplete="tel"
                                    maxlength="30"
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
                        >
                            <span v-if="!authStore.loading">
                                ذخیره تغییرات
                            </span>

                            <LoaderCircle
                                v-else
                                :size="16"
                                class="loader"
                            />

                            <Save
                                v-if="!authStore.loading"
                                :size="15"
                            />
                        </button>
                    </form>
                </section>

                <!-- Password -->
                <section class="profile-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <LockKeyhole :size="18" />
                        </div>

                        <div>
                            <h2>
                                تغییر رمز عبور
                            </h2>

                            <p>
                                رمز عبور حساب خود را تغییر دهید.
                            </p>
                        </div>
                    </div>

                    <div
                        v-if="passwordMessage"
                        class="message success"
                    >
                        <CheckCircle2 :size="15" />
                        {{ passwordMessage }}
                    </div>

                    <div
                        v-if="passwordError"
                        class="message error"
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
                                <LockKeyhole :size="15" />

                                <input
                                    id="current-password"
                                    v-model="
                                        passwordForm.current_password
                                    "
                                    type="password"
                                    autocomplete="current-password"
                                    required
                                />
                            </div>

                            <span
                                v-if="
                                    authStore.errors
                                        .current_password
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
                                <LockKeyhole :size="15" />

                                <input
                                    id="new-password"
                                    v-model="
                                        passwordForm.password
                                    "
                                    type="password"
                                    autocomplete="new-password"
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

                        <!-- Confirmation -->
                        <div class="field">
                            <label for="password-confirmation">
                                تکرار رمز عبور جدید
                            </label>

                            <div class="input">
                                <LockKeyhole :size="15" />

                                <input
                                    id="password-confirmation"
                                    v-model="
                                        passwordForm.password_confirmation
                                    "
                                    type="password"
                                    autocomplete="new-password"
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
                            class="save-button"
                            :disabled="authStore.loading"
                        >
                            <span v-if="!authStore.loading">
                                تغییر رمز عبور
                            </span>

                            <LoaderCircle
                                v-else
                                :size="16"
                                class="loader"
                            />

                            <LockKeyhole
                                v-if="!authStore.loading"
                                :size="15"
                            />
                        </button>
                    </form>
                </section>

            </div>
     <div>

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
    color: var(--orange);

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 8px;
}

.profile-header h1 {
    margin-top: 8px;

    color: var(--text-primary);

    font-size: clamp(30px, 4vw, 45px);

    font-weight: 900;
}

.profile-header p {
    margin-top: 7px;

    color: var(--text-muted);

    font-size: 11px;
}

.logout-button {
    min-height: 40px;

    display: inline-flex;
    align-items: center;

    gap: 7px;

    padding-inline: 14px;

    color: #e87979;

    border: 1px solid rgba(255, 92, 92, 0.14);
    border-radius: 8px;

    background: rgba(255, 92, 92, 0.025);

    font-size: 9px;
    font-weight: 700;

    cursor: pointer;

    transition:
        background var(--transition),
        border-color var(--transition);
}

.logout-button:hover {
    border-color: rgba(255, 92, 92, 0.3);

    background: rgba(255, 92, 92, 0.06);
}

.profile-grid {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 14px;
}

.profile-card {
    padding: 23px;

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
    width: 40px;
    height: 40px;

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

    font-size: 14px;
    font-weight: 800;
}

.card-header p {
    margin-top: 2px;

    color: var(--text-muted);

    font-size: 8px;
}

.profile-form {
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

    font-size: 9px;
    font-weight: 700;
}

.field label small {
    color: var(--text-muted);

    font-size: 8px;
    font-weight: 400;
}

.input {
    min-height: 44px;

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

.save-button {
    min-height: 45px;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

    margin-top: 3px;

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

    padding: 10px 11px;

    display: flex;
    align-items: center;

    gap: 7px;

    border-radius: 7px;

    font-size: 8px;
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
}

@media (max-width: 480px) {
    .profile-card {
        padding: 18px 15px;
    }

    .profile-header h1 {
        font-size: 31px;
    }

    .logout-button {
        width: 100%;
    }
}
</style>
