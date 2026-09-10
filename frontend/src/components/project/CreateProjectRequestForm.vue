<script setup>
import { reactive, ref } from 'vue'

import {
    ArrowLeft,
    CheckCircle2,
    FileText,
    LoaderCircle,
    Wallet,
} from 'lucide-vue-next'

import { useProjectRequestsStore } from '../../stores/projectRequests'

const requestsStore = useProjectRequestsStore()

const form = reactive({
    title: '',
    description: '',
    budget: '',
    currency: 'IRR',
})

const success = ref(false)
const successMessage = ref('')

const submit = async () => {
    requestsStore.clearErrors()

    const title = form.title.trim()
    const description = form.description.trim()

    const budget =
        form.budget === ''
            ? null
            : Number(form.budget)

    const result =
        await requestsStore.createRequest({
            title,
            description,
            budget,
            currency: form.currency,
        })

    if (!result.success) {
        return
    }

    success.value = true

    successMessage.value =
        result.message ||
        'درخواست پروژه شما با موفقیت ثبت شد.'
}
</script>

<template>
    <section
        class="request-form"
        aria-labelledby="project-request-form-title"
    >
        <!-- Header -->
        <header class="form-heading">
            <div
                class="heading-icon"
                aria-hidden="true"
            >
                <FileText :size="20" />
            </div>

            <div>
                <span class="heading-kicker">
                    PROJECT REQUEST
                </span>

                <h2 id="project-request-form-title">
                    اطلاعات پروژه
                </h2>
            </div>
        </header>

        <!-- Success -->
        <div
            v-if="success"
            class="success-state"
            role="status"
            aria-live="polite"
        >
            <div
                class="success-icon"
                aria-hidden="true"
            >
                <CheckCircle2 :size="38" />
            </div>

            <h2>
                درخواست شما ثبت شد
            </h2>

            <p>
                {{ successMessage }}
            </p>

            <span class="success-note">
                درخواست شما برای بررسی به تیم DerinCode
                ارسال شد.
            </span>
        </div>

        <!-- Form -->
        <form
            v-else
            novalidate
            @submit.prevent="submit"
        >
            <!-- General Error -->
            <div
                v-if="requestsStore.error"
                id="request-form-error"
                class="general-error"
                role="alert"
                aria-live="assertive"
            >
                {{ requestsStore.error }}
            </div>

            <!-- Title -->
            <div class="field">
                <label for="request-title">
                    عنوان پروژه
                </label>

                <input
                    id="request-title"
                    v-model="form.title"
                    type="text"
                    autocomplete="off"
                    maxlength="255"
                    placeholder="مثلاً طراحی سایت فروشگاهی"
                    :aria-invalid="
                        !!requestsStore.errors.title
                    "
                    :aria-describedby="
                        requestsStore.errors.title
                            ? 'request-title-error'
                            : undefined
                    "
                    required
                />

                <span
                    v-if="requestsStore.errors.title"
                    id="request-title-error"
                    class="field-error"
                >
                    {{ requestsStore.errors.title[0] }}
                </span>
            </div>

            <!-- Description -->
            <div class="field">
                <label for="request-description">
                    توضیحات پروژه
                </label>

                <textarea
                    id="request-description"
                    v-model="form.description"
                    rows="8"
                    maxlength="10000"
                    placeholder="نیازمندی‌ها، امکانات، تکنولوژی‌های موردنظر و هر توضیحی که برای فهم پروژه لازم است..."
                    :aria-invalid="
                        !!requestsStore.errors.description
                    "
                    :aria-describedby="
                        requestsStore.errors.description
                            ? 'request-description-error'
                            : undefined
                    "
                    required
                ></textarea>

                <span
                    v-if="
                        requestsStore.errors.description
                    "
                    id="request-description-error"
                    class="field-error"
                >
                    {{
                        requestsStore.errors.description[0]
                    }}
                </span>
            </div>

            <!-- Budget -->
            <div class="budget-row">
                <div class="field">
                    <label for="request-budget">
                        بودجه تقریبی
                        <small>
                            (اختیاری)
                        </small>
                    </label>

                    <div class="input-with-icon">
                        <Wallet
                            :size="15"
                            aria-hidden="true"
                        />

                        <input
                            id="request-budget"
                            v-model="form.budget"
                            type="number"
                            inputmode="decimal"
                            min="0"
                            step="0.01"
                            placeholder="مثلاً 50000000"
                            :aria-invalid="
                                !!requestsStore.errors.budget
                            "
                            :aria-describedby="
                                requestsStore.errors.budget
                                    ? 'request-budget-error'
                                    : undefined
                            "
                        />
                    </div>

                    <span
                        v-if="
                            requestsStore.errors.budget
                        "
                        id="request-budget-error"
                        class="field-error"
                    >
                        {{
                            requestsStore.errors.budget[0]
                        }}
                    </span>
                </div>

                <!-- Currency -->
                <div class="field">
                    <label for="request-currency">
                        واحد پول
                    </label>

                    <select
                        id="request-currency"
                        v-model="form.currency"
                        :aria-invalid="
                            !!requestsStore.errors.currency
                        "
                        :aria-describedby="
                            requestsStore.errors.currency
                                ? 'request-currency-error'
                                : undefined
                        "
                        required
                    >
                        <option value="IRR">
                            IRR
                        </option>

                        <option value="USD">
                            USD
                        </option>

                        <option value="EUR">
                            EUR
                        </option>
                    </select>

                    <span
                        v-if="
                            requestsStore.errors.currency
                        "
                        id="request-currency-error"
                        class="field-error"
                    >
                        {{
                            requestsStore.errors.currency[0]
                        }}
                    </span>
                </div>
            </div>

            <!-- Footer -->
            <div class="form-footer">
                <button
                    type="submit"
                    class="submit-button"
                    :disabled="requestsStore.loading"
                    :aria-busy="requestsStore.loading"
                >
                    <span
                        v-if="!requestsStore.loading"
                    >
                        ارسال درخواست

                        <ArrowLeft
                            :size="16"
                            aria-hidden="true"
                        />
                    </span>

                    <span
                        v-else
                        class="loading-content"
                    >
                        <LoaderCircle
                            :size="16"
                            class="loader"
                            aria-hidden="true"
                        />

                        در حال ارسال...
                    </span>
                </button>
            </div>
        </form>
    </section>
</template>

<style scoped>
.request-form {
    width: min(100%, 680px);

    margin: 0 auto;

    padding: 26px;

    border:
        1px solid var(--border);

    border-radius: 16px;

    background:
        linear-gradient(
            145deg,
            rgba(255, 255, 255, 0.035),
            rgba(255, 255, 255, 0.008)
        );

    box-shadow:
        0 25px 80px rgba(0, 0, 0, 0.3);
}

/* Header */

.form-heading {
    display: flex;

    align-items: center;

    gap: 12px;

    margin-bottom: 24px;
}

.heading-icon {
    width: 42px;
    height: 42px;

    flex-shrink: 0;

    display: flex;

    align-items: center;
    justify-content: center;

    color: var(--orange);

    border:
        1px solid rgba(255, 107, 0, 0.18);

    border-radius: 10px;

    background:
        rgba(255, 107, 0, 0.045);
}

.form-heading > div:last-child {
    display: flex;

    flex-direction: column;
}

.heading-kicker {
    color: var(--orange);

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 9px;

    letter-spacing: 0.06em;
}

.form-heading h2 {
    margin-top: 4px;

    color: var(--text-primary);

    font-size: 16px;

    font-weight: 800;

    line-height: 1.5;
}

/* Fields */

.field {
    display: flex;

    flex-direction: column;

    margin-bottom: 17px;
}

.field label {
    margin-bottom: 7px;

    color: var(--text-secondary);

    font-size: 11px;

    font-weight: 700;

    line-height: 1.6;
}

.field label small {
    color: var(--text-muted);

    font-size: 9px;

    font-weight: 400;
}

.field input,
.field textarea,
.field select {
    width: 100%;

    color: var(--text-primary);

    border:
        1px solid var(--border);

    border-radius: 8px;

    outline: 0;

    background:
        rgba(0, 0, 0, 0.2);

    font-family: inherit;

    font-size: 12px;

    transition:
        border-color var(--transition),
        background var(--transition),
        box-shadow var(--transition);
}

.field input,
.field select {
    min-height: 44px;

    padding-inline: 11px;
}

.field textarea {
    min-height: 145px;

    padding: 11px;

    resize: vertical;

    line-height: 2;
}

.field input:focus,
.field textarea:focus,
.field select:focus {
    border-color:
        var(--border-orange);

    background:
        rgba(255, 107, 0, 0.015);

    box-shadow:
        0 0 0 3px rgba(255, 107, 0, 0.04);
}

.field input::placeholder,
.field textarea::placeholder {
    color: #555c62;
}

.field select option {
    color: #fff;

    background: #0b0d0f;
}

.field input[aria-invalid="true"],
.field textarea[aria-invalid="true"],
.field select[aria-invalid="true"] {
    border-color:
        rgba(255, 92, 92, 0.4);
}

/* Budget */

.input-with-icon {
    min-height: 44px;

    display: flex;

    align-items: center;

    gap: 8px;

    padding-inline: 10px;

    border:
        1px solid var(--border);

    border-radius: 8px;

    background:
        rgba(0, 0, 0, 0.2);

    transition:
        border-color var(--transition),
        box-shadow var(--transition);
}

.input-with-icon:focus-within {
    border-color:
        var(--border-orange);

    box-shadow:
        0 0 0 3px rgba(255, 107, 0, 0.04);
}

.input-with-icon svg {
    flex-shrink: 0;

    color: #656c72;
}

.input-with-icon input {
    min-height: 40px;

    padding: 0;

    border: 0;

    background: transparent;

    box-shadow: none;
}

.input-with-icon input:focus {
    background: transparent;

    box-shadow: none;
}

.budget-row {
    display: grid;

    grid-template-columns:
        1.4fr 0.6fr;

    gap: 10px;
}

/* Errors */

.field-error {
    margin-top: 5px;

    color: var(--danger);

    font-size: 10px;

    line-height: 1.7;
}

.general-error {
    margin-bottom: 16px;

    padding: 11px;

    color: var(--danger);

    border:
        1px solid rgba(255, 92, 92, 0.14);

    border-radius: 7px;

    background:
        rgba(255, 92, 92, 0.025);

    font-size: 10px;

    line-height: 1.8;
}

/* Submit */

.form-footer {
    margin-top: 7px;
}

.submit-button {
    width: 100%;

    min-height: 46px;

    display: flex;

    align-items: center;
    justify-content: center;

    color: #fff;

    border: 0;

    border-radius: 8px;

    background:
        var(--orange);

    font-family: inherit;

    font-size: 12px;

    font-weight: 800;

    cursor: pointer;

    transition:
        background var(--transition),
        transform var(--transition),
        opacity var(--transition);
}

.submit-button:hover:not(:disabled) {
    background:
        var(--orange-light);

    transform:
        translateY(-1px);
}

.submit-button:disabled {
    opacity: 0.7;

    cursor: wait;
}

.submit-button > span {
    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;
}

.loading-content {
    display: flex;

    align-items: center;

    gap: 8px;
}

.loader {
    animation:
        spin 0.8s linear infinite;
}

/* Success */

.success-state {
    min-height: 320px;

    display: flex;

    flex-direction: column;

    align-items: center;
    justify-content: center;

    text-align: center;
}

.success-icon {
    width: 64px;
    height: 64px;

    display: flex;

    align-items: center;
    justify-content: center;

    color: var(--success);

    border:
        1px solid rgba(55, 214, 122, 0.16);

    border-radius: 16px;

    background:
        rgba(55, 214, 122, 0.04);
}

.success-state h2 {
    margin-top: 18px;

    color: var(--text-primary);

    font-size: 19px;

    line-height: 1.5;
}

.success-state p {
    max-width: 520px;

    margin-top: 8px;

    color: var(--text-secondary);

    font-size: 11px;

    line-height: 1.9;
}

.success-note {
    margin-top: 6px;

    color: var(--text-muted);

    font-size: 9px;

    line-height: 1.8;
}

/* Accessibility */

button:focus-visible,
input:focus-visible,
textarea:focus-visible,
select:focus-visible {
    outline: 2px solid var(--orange);

    outline-offset: 2px;
}

/* Animation */

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Responsive */

@media (max-width: 520px) {
    .request-form {
        padding: 18px 14px;

        border-radius: 13px;
    }

    .budget-row {
        grid-template-columns: 1fr;
    }

    .form-heading {
        margin-bottom: 20px;
    }

    .field textarea {
        min-height: 125px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .loader {
        animation: none;
    }

    .field input,
    .field textarea,
    .field select,
    .input-with-icon,
    .submit-button {
        transition: none;
    }
}
</style>
