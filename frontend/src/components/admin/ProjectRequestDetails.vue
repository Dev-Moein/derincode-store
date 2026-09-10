<script setup>
import { reactive, ref, watch } from 'vue'
import {
    LoaderCircle,
    Save,
    X,
} from 'lucide-vue-next'

import { useAdminProjectRequestsStore } from '../../stores/adminProjectRequests'

const props = defineProps({
    request: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits([
    'saved',
    'close',
])

const store = useAdminProjectRequestsStore()

const form = reactive({
    status: 'pending',
    admin_note: '',
})

const initialized = ref(false)

const statuses = [
    {
        value: 'pending',
        label: 'در انتظار',
    },
    {
        value: 'reviewing',
        label: 'در حال بررسی',
    },
    {
        value: 'accepted',
        label: 'تأیید شده',
    },
    {
        value: 'rejected',
        label: 'رد شده',
    },
    {
        value: 'completed',
        label: 'تکمیل شده',
    },
    {
        value: 'cancelled',
        label: 'لغو شده',
    },
]

watch(
    () => props.request,
    (request) => {
        if (!request) {
            initialized.value = false
            return
        }

        form.status =
            request.status || 'pending'

        form.admin_note =
            request.admin_note || ''

        initialized.value = true

        if (store.clearErrors) {
            store.clearErrors()
        }
    },
    {
        immediate: true,
    }
)

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
            timeStyle: 'short',
        }
    ).format(date)
}

const formatBudget = (
    budget,
    currency
) => {
    if (
        budget === null ||
        budget === undefined ||
        budget === ''
    ) {
        return 'تعیین نشده'
    }

    const numericBudget = Number(budget)

    if (Number.isNaN(numericBudget)) {
        return 'تعیین نشده'
    }

    return `${new Intl.NumberFormat(
        'en-US'
    ).format(numericBudget)} ${
        currency || ''
    }`.trim()
}

const submit = async () => {
    if (!initialized.value || store.saving) {
        return
    }

    if (store.clearErrors) {
        store.clearErrors()
    }

    const result =
        await store.updateStatus(
            props.request.id,
            form.status,
            form.admin_note.trim() || null
        )

    if (!result?.success) {
        return
    }

    emit(
        'saved',
        result.request
    )
}
</script>

<template>
    <div
        class="details"
        aria-labelledby="request-details-title"
    >
        <!-- Header -->
        <header class="details-header">
            <div>
                <span class="kicker">
                    REQUEST #{{ request.id }}
                </span>

                <h2 id="request-details-title">
                    {{ request.title }}
                </h2>
            </div>

            <button
                type="button"
                class="close-button"
                aria-label="بستن جزئیات درخواست"
                :disabled="store.saving"
                @click="emit('close')"
            >
                <X
                    :size="18"
                    aria-hidden="true"
                />
            </button>
        </header>

        <!-- General Error -->
        <div
            v-if="store.error"
            id="request-details-error"
            class="error-box"
            role="alert"
            aria-live="assertive"
        >
            {{ store.error }}
        </div>

        <!-- User -->
        <section
            class="info-section"
            aria-labelledby="request-user-title"
        >
            <h3
                id="request-user-title"
                class="section-label"
            >
                مشتری
            </h3>

            <div class="user-card">
                <div
                    class="user-avatar"
                    aria-hidden="true"
                >
                    {{
                        request.user?.name
                            ?.charAt(0)
                            ?.toUpperCase() || 'U'
                    }}
                </div>

                <div>
                    <strong>
                        {{ request.user?.name || '-' }}
                    </strong>

                    <span>
                        {{ request.user?.email || '-' }}
                    </span>
                </div>
            </div>
        </section>

        <!-- Request -->
        <section
            class="info-section"
            aria-labelledby="request-info-title"
        >
            <h3
                id="request-info-title"
                class="section-label"
            >
                اطلاعات درخواست
            </h3>

            <div class="request-info">
                <div>
                    <span>
                        عنوان
                    </span>

                    <strong>
                        {{ request.title || '-' }}
                    </strong>
                </div>

                <div>
                    <span>
                        بودجه
                    </span>

                    <strong>
                        {{
                            formatBudget(
                                request.budget,
                                request.currency
                            )
                        }}
                    </strong>
                </div>

                <div>
                    <span>
                        تاریخ ثبت
                    </span>

                    <time
                        :datetime="
                            request.created_at || undefined
                        "
                    >
                        {{
                            formatDate(
                                request.created_at
                            )
                        }}
                    </time>
                </div>

                <div>
                    <span>
                        بررسی شده
                    </span>

                    <time
                        :datetime="
                            request.reviewed_at || undefined
                        "
                    >
                        {{
                            formatDate(
                                request.reviewed_at
                            )
                        }}
                    </time>
                </div>
            </div>
        </section>

        <!-- Description -->
        <section
            class="info-section"
            aria-labelledby="request-description-title"
        >
            <h3
                id="request-description-title"
                class="section-label"
            >
                توضیحات مشتری
            </h3>

            <div class="description">
                {{
                    request.description ||
                    'توضیحی ثبت نشده است.'
                }}
            </div>
        </section>

        <!-- Status -->
        <section
            class="info-section"
            aria-labelledby="request-management-title"
        >
            <h3
                id="request-management-title"
                class="section-label"
            >
                مدیریت درخواست
            </h3>

            <div class="field">
                <label for="request-status">
                    وضعیت
                </label>

                <select
                    id="request-status"
                    v-model="form.status"
                    :disabled="store.saving"
                    :aria-describedby="
                        store.error
                            ? 'request-details-error'
                            : undefined
                    "
                >
                    <option
                        v-for="item in statuses"
                        :key="item.value"
                        :value="item.value"
                    >
                        {{ item.label }}
                    </option>
                </select>
            </div>

            <div class="field">
                <label for="request-admin-note">
                    یادداشت ادمین
                </label>

                <textarea
                    id="request-admin-note"
                    v-model="form.admin_note"
                    rows="6"
                    maxlength="10000"
                    :disabled="store.saving"
                    placeholder="یادداشت داخلی برای این درخواست..."
                ></textarea>

                <small>
                    این یادداشت در پنل و پاسخ درخواست
                    قابل استفاده است.
                </small>
            </div>
        </section>

        <!-- Actions -->
        <div class="form-actions">
            <button
                type="button"
                class="secondary-button"
                :disabled="store.saving"
                @click="emit('close')"
            >
                بستن
            </button>

            <button
                type="button"
                class="primary-button"
                :disabled="
                    store.saving ||
                    !initialized
                "
                :aria-busy="store.saving"
                @click="submit"
            >
                <LoaderCircle
                    v-if="store.saving"
                    :size="15"
                    class="spin"
                    aria-hidden="true"
                />

                <Save
                    v-else
                    :size="15"
                    aria-hidden="true"
                />

                {{
                    store.saving
                        ? 'در حال ذخیره...'
                        : 'ذخیره تغییرات'
                }}
            </button>
        </div>
    </div>
</template>

<style scoped>
.details {
    direction: rtl;
}

.details-header {
    display: flex;

    align-items: flex-start;
    justify-content: space-between;

    gap: 15px;

    margin-bottom: 23px;
}

.kicker {
    color: var(--orange);

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 9px;
    font-weight: 600;
}

.details-header h2 {
    margin-top: 5px;

    color: var(--text-primary);

    font-family: inherit;

    font-size: 20px;

    font-weight: 850;

    line-height: 1.5;
}

.close-button {
    width: 34px;
    height: 34px;

    display: flex;

    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    color: var(--text-muted);

    border: 1px solid var(--border);

    border-radius: 8px;

    background: transparent;

    cursor: pointer;

    transition:
        color var(--transition),
        border-color var(--transition),
        background var(--transition);
}

.close-button:hover:not(:disabled) {
    color: var(--orange);

    border-color: var(--border-orange);

    background: var(--orange-soft);
}

.close-button:disabled {
    opacity: 0.5;

    cursor: wait;
}

.close-button:focus-visible,
.primary-button:focus-visible,
.secondary-button:focus-visible,
.field select:focus-visible,
.field textarea:focus-visible {
    outline: 2px solid var(--orange);

    outline-offset: 3px;
}

.error-box {
    margin-bottom: 15px;

    padding: 10px 12px;

    color: var(--danger);

    border: 1px solid rgba(255, 92, 92, 0.14);

    border-radius: 8px;

    background: rgba(255, 92, 92, 0.025);

    font-family: inherit;

    font-size: 10px;

    line-height: 1.7;
}

.info-section {
    margin-top: 21px;
}

.info-section:first-of-type {
    margin-top: 0;
}

.section-label {
    margin: 0 0 9px;

    color: var(--text-muted);

    font-family: inherit;

    font-size: 11px;

    font-weight: 800;
}

.user-card {
    display: flex;

    align-items: center;

    gap: 9px;

    padding: 11px;

    border: 1px solid var(--border);

    border-radius: 8px;

    background: rgba(255, 255, 255, 0.015);
}

.user-avatar {
    width: 35px;
    height: 35px;

    display: flex;

    align-items: center;
    justify-content: center;

    flex-shrink: 0;

    color: #111;

    border-radius: 9px;

    background: var(--orange);

    font-size: 11px;

    font-weight: 900;
}

.user-card > div:last-child {
    min-width: 0;

    display: flex;

    flex-direction: column;

    gap: 3px;
}

.user-card strong {
    color: var(--text-secondary);

    font-family: inherit;

    font-size: 11px;
}

.user-card span {
    overflow: hidden;

    color: var(--text-muted);

    direction: ltr;

    font-family: inherit;

    font-size: 10px;

    text-align: right;

    text-overflow: ellipsis;
    white-space: nowrap;
}

.request-info {
    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 7px;
}

.request-info > div {
    min-height: 51px;

    padding: 9px;

    display: flex;

    flex-direction: column;

    justify-content: center;

    gap: 4px;

    border: 1px solid var(--border);

    border-radius: 7px;

    background: rgba(255, 255, 255, 0.01);
}

.request-info span {
    color: var(--text-muted);

    font-family: inherit;

    font-size: 9px;
}

.request-info strong,
.request-info time {
    color: var(--text-secondary);

    font-family: inherit;

    font-size: 10px;

    font-weight: 700;
}

.request-info > div:nth-child(2) strong {
    direction: ltr;

    text-align: right;
}

.description {
    min-height: 80px;

    padding: 12px;

    color: var(--text-secondary);

    border: 1px solid var(--border);

    border-radius: 8px;

    background: rgba(255, 255, 255, 0.01);

    font-family: inherit;

    font-size: 11px;

    line-height: 2;

    white-space: pre-line;
}

.field {
    display: flex;

    flex-direction: column;

    margin-top: 11px;
}

.field label {
    margin-bottom: 7px;

    color: var(--text-secondary);

    font-family: inherit;

    font-size: 11px;

    font-weight: 700;
}

.field select,
.field textarea {
    width: 100%;

    outline: 0;

    color: var(--text-primary);

    border: 1px solid var(--border);

    border-radius: 8px;

    background: var(--bg-primary);

    font-family: inherit;

    font-size: 12px;

    transition: border-color var(--transition);
}

.field select {
    min-height: 40px;

    padding-inline: 10px;
}

.field textarea {
    padding: 10px;

    resize: vertical;

    line-height: 1.9;
}

.field select:focus,
.field textarea:focus {
    border-color: var(--border-orange);
}

.field select option {
    color: var(--text-primary);

    background: var(--bg-primary);
}

.field small {
    margin-top: 5px;

    color: var(--text-muted);

    font-family: inherit;

    font-size: 9px;

    line-height: 1.7;
}

.form-actions {
    display: flex;

    justify-content: flex-end;

    gap: 7px;

    margin-top: 25px;

    padding-top: 15px;

    border-top: 1px solid var(--border);
}

.primary-button,
.secondary-button {
    min-height: 40px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 7px;

    padding-inline: 14px;

    border-radius: 8px;

    font-family: inherit;

    font-size: 11px;

    font-weight: 700;

    cursor: pointer;
}

.primary-button {
    color: #fff;

    border: 0;

    background: var(--orange);

    transition:
        background var(--transition),
        opacity var(--transition);
}

.primary-button:hover:not(:disabled) {
    background: var(--orange-light);
}

.secondary-button {
    color: var(--text-secondary);

    border: 1px solid var(--border);

    background: transparent;

    transition:
        color var(--transition),
        border-color var(--transition),
        background var(--transition);
}

.secondary-button:hover:not(:disabled) {
    color: var(--text-primary);

    border-color: var(--border-orange);

    background: var(--orange-soft);
}

button:disabled {
    opacity: 0.6;

    cursor: wait;
}

.spin {
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@media (prefers-reduced-motion: reduce) {
    .close-button,
    .primary-button,
    .secondary-button,
    .field select,
    .field textarea,
    .spin {
        transition: none;
        animation: none;
    }
}

@media (max-width: 500px) {
    .request-info {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .primary-button,
    .secondary-button {
        width: 100%;
    }
}
</style>
