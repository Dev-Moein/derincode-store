<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
    ArrowRight,
    CheckCircle2,
    LoaderCircle,
    Mail,
    Phone,
    ShieldCheck,
    Trash2,
    UserRound,
} from 'lucide-vue-next'

import { useAdminUsersStore } from '../../stores/adminUsers'

const route = useRoute()
const router = useRouter()
const store = useAdminUsersStore()

const selectedRole = ref('')
const roleSuccess = ref('')

const loadUser = async () => {
    roleSuccess.value = ''

    const result = await store.fetchUser(route.params.id)

    if (!result.success) {
        return
    }

    const roles = store.currentUser?.roles || []

    selectedRole.value = roles.length
        ? roles[0].name
        : ''
}

const updateRole = async () => {
    roleSuccess.value = ''

    if (!selectedRole.value) {
        return
    }

    const result = await store.updateRole(
        route.params.id,
        selectedRole.value
    )

    if (result.success) {
        roleSuccess.value =
            result.message ||
            'نقش کاربر با موفقیت تغییر کرد.'

        await loadUser()
    }
}

const deleteUser = async () => {
    const confirmDelete = window.confirm(
        'آیا از حذف این کاربر مطمئن هستید؟ این عملیات قابل بازگشت نیست.'
    )

    if (!confirmDelete) {
        return
    }

    const result = await store.deleteUser(
        route.params.id
    )

    if (result.success) {
        router.push({
            name: 'admin-users',
        })
    }
}

const formatDate = (value) => {
    if (!value) {
        return '-'
    }

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return '-'
    }

    return new Intl.DateTimeFormat(
        'fa-IR',
        {
            dateStyle: 'medium',
        }
    ).format(date)
}

const roleLabel = (role) => {
    const labels = {
        admin: 'مدیر',
        user: 'کاربر',
    }

    return labels[role] || role || 'نامشخص'
}

onMounted(loadUser)
</script>

<template>
    <main
        class="user-page"
        aria-labelledby="user-details-title"
    >
        <!-- Loading -->
        <div
            v-if="store.loading"
            class="state"
            role="status"
            aria-live="polite"
        >
            <LoaderCircle
                :size="24"
                class="spin"
                aria-hidden="true"
            />

            <span>
                در حال دریافت اطلاعات کاربر...
            </span>
        </div>

        <!-- Error -->
        <div
            v-else-if="store.error"
            class="error-banner"
            role="alert"
            aria-live="assertive"
        >
            {{ store.error }}

            <button
                type="button"
                class="retry-button"
                @click="loadUser"
            >
                تلاش مجدد
            </button>
        </div>

        <!-- User -->
        <section
            v-else-if="store.currentUser"
            class="user-card"
            aria-labelledby="user-details-title"
        >
            <!-- Header -->
            <header class="page-header">
                <div>
                    <span class="kicker">
                        ADMIN / USER DETAILS
                    </span>

                    <h1 id="user-details-title">
                        جزئیات کاربر
                    </h1>

                    <p>
                        مشاهده اطلاعات حساب، نقش‌ها و
                        دسترسی‌های این کاربر.
                    </p>
                </div>

                <button
                    type="button"
                    class="back-button"
                    @click="
                        router.push({
                            name: 'admin-users',
                        })
                    "
                >
                    <ArrowRight
                        :size="15"
                        aria-hidden="true"
                    />

                    بازگشت به کاربران
                </button>
            </header>

            <!-- Basic Information -->
            <section
                class="section"
                aria-labelledby="basic-info-title"
            >
                <header class="section-header">
                    <UserRound
                        :size="18"
                        aria-hidden="true"
                    />

                    <h2 id="basic-info-title">
                        اطلاعات حساب
                    </h2>
                </header>

                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">
                            نام
                        </span>

                        <strong>
                            {{ store.currentUser.name || '-' }}
                        </strong>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            ایمیل
                        </span>

                        <strong
                            dir="ltr"
                            class="ltr"
                        >
                            {{ store.currentUser.email || '-' }}
                        </strong>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            تلفن
                        </span>

                        <strong
                            dir="ltr"
                            class="ltr"
                        >
                            {{ store.currentUser.phone || '-' }}
                        </strong>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            تاریخ ثبت‌نام
                        </span>

                        <strong>
                            {{
                                formatDate(
                                    store.currentUser.created_at
                                )
                            }}
                        </strong>
                    </div>
                </div>
            </section>

            <!-- Roles -->
            <section
                class="section"
                aria-labelledby="roles-title"
            >
                <header class="section-header">
                    <ShieldCheck
                        :size="18"
                        aria-hidden="true"
                    />

                    <h2 id="roles-title">
                        نقش‌های کاربر
                    </h2>
                </header>

                <div
                    v-if="
                        store.currentUser.roles?.length
                    "
                    class="badges"
                >
                    <span
                        v-for="
                            role in store.currentUser.roles
                        "
                        :key="role.id"
                        class="badge"
                    >
                        {{ roleLabel(role.name) }}
                    </span>
                </div>

                <p
                    v-else
                    class="muted"
                >
                    این کاربر نقشی ندارد.
                </p>

                <div class="role-change">
                    <label
                        for="user-role"
                        class="sr-only"
                    >
                        نقش جدید
                    </label>

                    <select
                        id="user-role"
                        v-model="selectedRole"
                        :disabled="store.loading"
                    >
                        <option value="">
                            انتخاب نقش
                        </option>

                        <option value="admin">
                            مدیر
                        </option>

                        <option value="user">
                            کاربر
                        </option>
                    </select>

                    <button
                        type="button"
                        class="primary-button"
                        :disabled="
                            store.loading ||
                            !selectedRole
                        "
                        :aria-busy="store.loading"
                        @click="updateRole"
                    >
                        <LoaderCircle
                            v-if="store.loading"
                            :size="14"
                            class="spin"
                            aria-hidden="true"
                        />

                        <CheckCircle2
                            v-else
                            :size="14"
                            aria-hidden="true"
                        />

                        تغییر نقش
                    </button>
                </div>

                <div
                    v-if="roleSuccess"
                    class="success-message"
                    role="status"
                    aria-live="polite"
                >
                    <CheckCircle2
                        :size="14"
                        aria-hidden="true"
                    />

                    {{ roleSuccess }}
                </div>
            </section>

            <!-- Permissions -->
            <section
                class="section"
                aria-labelledby="permissions-title"
            >
                <header class="section-header">
                    <ShieldCheck
                        :size="18"
                        aria-hidden="true"
                    />

                    <h2 id="permissions-title">
                        Permission ها
                    </h2>
                </header>

                <div
                    v-if="
                        store.currentUser.permissions?.length
                    "
                    class="badges"
                >
                    <span
                        v-for="
                            permission in
                            store.currentUser.permissions
                        "
                        :key="permission.id"
                        class="badge permission"
                    >
                        {{ permission.name }}
                    </span>
                </div>

                <p
                    v-else
                    class="muted"
                >
                    این کاربر Permission مستقیمی ندارد.
                </p>
            </section>

            <!-- Danger Zone -->
            <section
                class="danger-zone"
                aria-labelledby="danger-zone-title"
            >
                <div>
                    <h2 id="danger-zone-title">
                        حذف حساب کاربر
                    </h2>

                    <p>
                        با حذف کاربر، اطلاعات حساب او
                        حذف خواهد شد و این عملیات قابل
                        بازگشت نیست.
                    </p>
                </div>

                <button
                    type="button"
                    class="delete-button"
                    :disabled="store.loading"
                    :aria-busy="store.loading"
                    @click="deleteUser"
                >
                    <Trash2
                        :size="15"
                        aria-hidden="true"
                    />

                    حذف کاربر
                </button>
            </section>
        </section>
    </main>
</template>

<style scoped>
.user-page {
    direction: rtl;
}

/* Header */

.page-header {
    display: flex;

    align-items: flex-end;
    justify-content: space-between;

    gap: 20px;

    margin-bottom: 24px;
}

.kicker {
    color: #ff6a00;

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 10px;

    letter-spacing: 0.08em;
}

.page-header h1 {
    margin-top: 7px;

    color: #f3f3f3;

    font-size: 29px;

    font-weight: 900;

    line-height: 1.35;
}

.page-header p {
    max-width: 620px;

    margin-top: 6px;

    color: #777;

    font-size: 12px;

    line-height: 1.9;
}

/* Card */

.user-card {
    padding: 22px;

    border:
        1px solid rgba(255, 255, 255, 0.065);

    border-radius: 13px;

    background:
        rgba(255, 255, 255, 0.012);
}

/* Back */

.back-button {
    min-height: 38px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 7px;

    padding-inline: 12px;

    color: #888;

    border:
        1px solid rgba(255, 255, 255, 0.065);

    border-radius: 8px;

    background: transparent;

    font-family: inherit;

    font-size: 11px;

    font-weight: 600;

    cursor: pointer;

    transition:
        color 0.2s ease,
        border-color 0.2s ease,
        background 0.2s ease;
}

.back-button:hover {
    color: #ff6a00;

    border-color:
        rgba(255, 106, 0, 0.2);

    background:
        rgba(255, 106, 0, 0.025);
}

/* Sections */

.section {
    padding: 20px 0;

    border-top:
        1px solid rgba(255, 255, 255, 0.055);
}

.section:first-of-type {
    padding-top: 0;

    border-top: 0;
}

.section-header {
    display: flex;

    align-items: center;

    gap: 8px;

    margin-bottom: 15px;

    color: #ff6a00;
}

.section-header h2 {
    color: #d8d8d8;

    font-size: 15px;

    font-weight: 800;
}

/* Info */

.info-grid {
    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 8px;
}

.info-item {
    min-width: 0;

    padding: 14px;

    display: flex;

    flex-direction: column;

    gap: 7px;

    border:
        1px solid rgba(255, 255, 255, 0.05);

    border-radius: 9px;

    background:
        rgba(255, 255, 255, 0.012);
}

.info-label {
    color: #555;

    font-size: 10px;
}

.info-item strong {
    overflow: hidden;

    color: #cfcfcf;

    font-size: 11px;

    font-weight: 600;

    line-height: 1.7;

    text-overflow: ellipsis;
}

.ltr {
    text-align: right;
}

/* Badges */

.badges {
    display: flex;

    flex-wrap: wrap;

    gap: 7px;
}

.badge {
    padding: 6px 9px;

    color: #ff9a5a;

    border:
        1px solid rgba(255, 106, 0, 0.12);

    border-radius: 6px;

    background:
        rgba(255, 106, 0, 0.045);

    font-family: var(--font-mono);

    font-size: 9px;

    line-height: 1.5;
}

.badge.permission {
    color: #aaa;

    border-color:
        rgba(255, 255, 255, 0.07);

    background:
        rgba(255, 255, 255, 0.025);
}

.muted {
    color: #555;

    font-size: 10px;
}

/* Role */

.role-change {
    margin-top: 16px;

    display: flex;

    align-items: center;

    gap: 8px;
}

.role-change select {
    min-height: 39px;

    min-width: 150px;

    padding-inline: 10px;

    color: #aaa;

    border:
        1px solid rgba(255, 255, 255, 0.07);

    border-radius: 7px;

    outline: 0;

    background: #0d0d0d;

    font-family: inherit;

    font-size: 11px;

    cursor: pointer;
}

.role-change select:focus-visible {
    border-color:
        rgba(255, 106, 0, 0.45);

    box-shadow:
        0 0 0 3px rgba(255, 106, 0, 0.08);
}

.primary-button {
    min-height: 39px;

    padding-inline: 13px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 7px;

    color: #fff;

    border: 0;

    border-radius: 7px;

    background: #ff6a00;

    font-family: inherit;

    font-size: 11px;

    font-weight: 700;

    cursor: pointer;

    transition:
        background 0.2s ease,
        transform 0.2s ease;
}

.primary-button:hover:not(:disabled) {
    background: #ff781b;

    transform: translateY(-1px);
}

.primary-button:disabled {
    opacity: 0.45;

    cursor: not-allowed;
}

.success-message {
    margin-top: 10px;

    display: flex;

    align-items: center;

    gap: 7px;

    color: #4ad58a;

    font-size: 10px;
}

/* Danger */

.danger-zone {
    margin-top: 8px;

    padding: 17px;

    display: flex;

    align-items: center;
    justify-content: space-between;

    gap: 20px;

    border:
        1px solid rgba(220, 75, 75, 0.12);

    border-radius: 9px;

    background:
        rgba(220, 75, 75, 0.018);
}

.danger-zone h2 {
    color: #d77b7b;

    font-size: 12px;

    font-weight: 800;
}

.danger-zone p {
    margin-top: 5px;

    color: #666;

    font-size: 9px;

    line-height: 1.8;
}

.delete-button {
    min-height: 38px;

    flex-shrink: 0;

    padding-inline: 12px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 7px;

    color: #e87878;

    border:
        1px solid rgba(232, 113, 113, 0.16);

    border-radius: 7px;

    background:
        rgba(232, 113, 113, 0.025);

    font-family: inherit;

    font-size: 10px;

    font-weight: 700;

    cursor: pointer;

    transition:
        color 0.2s ease,
        border-color 0.2s ease,
        background 0.2s ease;
}

.delete-button:hover:not(:disabled) {
    color: #fff;

    border-color:
        rgba(232, 113, 113, 0.35);

    background:
        rgba(232, 113, 113, 0.09);
}

.delete-button:disabled {
    opacity: 0.45;

    cursor: not-allowed;
}

/* Error */

.error-banner {
    padding: 14px;

    display: flex;

    align-items: center;
    justify-content: space-between;

    gap: 15px;

    color: #e87878;

    border:
        1px solid rgba(232, 120, 120, 0.13);

    border-radius: 9px;

    background:
        rgba(232, 120, 120, 0.025);

    font-size: 11px;
}

.retry-button {
    min-height: 34px;

    padding-inline: 10px;

    color: #ddd;

    border:
        1px solid rgba(255, 255, 255, 0.07);

    border-radius: 6px;

    background: transparent;

    font-family: inherit;

    font-size: 10px;

    cursor: pointer;
}

/* Loading */

.state {
    min-height: 380px;

    display: flex;

    flex-direction: column;

    align-items: center;
    justify-content: center;

    gap: 10px;

    color: #666;

    border:
        1px solid rgba(255, 255, 255, 0.06);

    border-radius: 12px;

    background:
        rgba(255, 255, 255, 0.01);

    font-size: 11px;
}

.spin {
    animation:
        spin 0.9s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Accessibility */

.sr-only {
    position: absolute;

    width: 1px;
    height: 1px;

    padding: 0;
    margin: -1px;

    overflow: hidden;

    clip: rect(0, 0, 0, 0);

    white-space: nowrap;

    border: 0;
}

button:focus-visible,
select:focus-visible {
    outline: 2px solid #ff6a00;

    outline-offset: 2px;
}

/* Responsive */

@media (max-width: 700px) {
    .page-header {
        align-items: flex-start;

        flex-direction: column;
    }

    .back-button {
        width: 100%;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    .danger-zone {
        align-items: flex-start;

        flex-direction: column;
    }

    .delete-button {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .user-card {
        padding: 16px;
    }

    .page-header h1 {
        font-size: 25px;
    }

    .role-change {
        align-items: stretch;

        flex-direction: column;
    }

    .role-change select,
    .primary-button {
        width: 100%;
    }

    .error-banner {
        align-items: flex-start;

        flex-direction: column;
    }

    .retry-button {
        width: 100%;
    }
}

/* Reduced motion */

@media (prefers-reduced-motion: reduce) {
    .spin {
        animation: none;
    }

    .back-button,
    .primary-button,
    .delete-button {
        transition: none;
    }
}
</style>
