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

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits([
    'success',
    'close',
])

const requestsStore = useProjectRequestsStore()

const form = reactive({
    title: props.project?.title || '',
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

    const result = await requestsStore.createRequest({
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

    emit('success', result.request)
}
</script>

<template>
    <div
        class="request-form"
        aria-labelledby="project-request-title"
    >
        <header class="form-heading">
            <div
                class="heading-icon"
                aria-hidden="true"
            >
                <FileText :size="19" />
            </div>

            <div>
                <span>PROJECT REQUEST</span>

                <h3 id="project-request-title">
                    درخواست پروژه
                </h3>
            </div>
        </header>

        <!-- Success -->
        <section
            v-if="success"
            class="success-state"
            role="status"
            aria-live="polite"
            aria-labelledby="request-success-title"
        >
            <div
                class="success-icon"
                aria-hidden="true"
            >
                <CheckCircle2 :size="30" />
            </div>

            <h3 id="request-success-title">
                درخواست شما ثبت شد
            </h3>

            <p>
                {{ successMessage }}
            </p>

            <button
                type="button"
                class="close-button"
                @click="emit('close')"
            >
                بازگشت
            </button>
        </section>

        <!-- Form -->
        <form
            v-else
            @submit.prevent="submit"
        >
            <!-- General Error -->
            <div
                v-if="requestsStore.error"
                id="request-general-error"
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
                    maxlength="255"
                    autocomplete="off"
                    required
                    :aria-invalid="
                        !!requestsStore.errors.title
                    "
                    :aria-describedby="
                        requestsStore.errors.title
                            ? 'request-title-error'
                            : undefined
                    "
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
                    rows="6"
                    maxlength="10000"
                    placeholder="نیازها، امکانات و توضیحات پروژه را بنویسید..."
                    required
                    :aria-invalid="
                        !!requestsStore.errors.description
                    "
                    :aria-describedby="
                        requestsStore.errors.description
                            ? 'request-description-error'
                            : undefined
                    "
                ></textarea>

                <span
                    v-if="requestsStore.errors.description"
                    id="request-description-error"
                    class="field-error"
                >
                    {{ requestsStore.errors.description[0] }}
                </span>
            </div>

            <!-- Budget -->
            <div class="budget-row">
                <div class="field">
                    <label for="request-budget">
                        بودجه
                        <small>(اختیاری)</small>
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
                            min="0"
                            step="0.01"
                            inputmode="decimal"
                            dir="ltr"
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
                        v-if="requestsStore.errors.budget"
                        id="request-budget-error"
                        class="field-error"
                    >
                        {{ requestsStore.errors.budget[0] }}
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
                        required
                        :aria-invalid="
                            !!requestsStore.errors.currency
                        "
                        :aria-describedby="
                            requestsStore.errors.currency
                                ? 'request-currency-error'
                                : undefined
                        "
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
                        v-if="requestsStore.errors.currency"
                        id="request-currency-error"
                        class="field-error"
                    >
                        {{ requestsStore.errors.currency[0] }}
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
                    <span v-if="!requestsStore.loading">
                        ثبت درخواست
                    </span>

                    <LoaderCircle
                        v-else
                        :size="16"
                        class="loader"
                        aria-hidden="true"
                    />

                    <ArrowLeft
                        v-if="!requestsStore.loading"
                        :size="16"
                        aria-hidden="true"
                    />
                </button>

                <button
                    type="button"
                    class="cancel-button"
                    :disabled="requestsStore.loading"
                    @click="emit('close')"
                >
                    انصراف
                </button>
            </div>
        </form>
    </div>
</template>

<style scoped>
.request-form {
    width: min(100%, 620px);

    padding: 24px;

    border: 1px solid var(--border);
    border-radius: 15px;

    background:
        linear-gradient(
            145deg,
            rgba(255, 255, 255, 0.03),
            rgba(255, 255, 255, 0.008)
        );

    box-shadow:
        0 25px 80px rgba(0, 0, 0, 0.32);
}

.form-heading {
    display: flex;
    align-items: center;

    gap: 11px;

    margin-bottom: 24px;
}

.heading-icon {
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

.form-heading > div:last-child {
    display: flex;
    flex-direction: column;
}

.form-heading span {
    color: var(--orange);

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 9px;
    font-weight: 600;

    letter-spacing: 0.04em;
}

.form-heading h3 {
    margin-top: 3px;

    color: var(--text-primary);

    font-family: inherit;

    font-size: 16px;
    font-weight: 750;
}

/* Fields */

.field {
    display: flex;
    flex-direction: column;

    margin-bottom: 16px;
}

.field label {
    margin-bottom: 7px;

    color: var(--text-secondary);

    font-family: inherit;

    font-size: 11px;
    font-weight: 700;
}

.field label small {
    color: var(--text-muted);

    font-size: 10px;
    font-weight: 400;
}

.field input,
.field textarea,
.field select {
    width: 100%;

    border: 1px solid var(--border);
    border-radius: 8px;

    outline: 0;

    color: var(--text-primary);

    background: rgba(0, 0, 0, 0.2);

    font-family: inherit;
    font-size: 12px;

    transition: border-color var(--transition);
}

.field input,
.field select {
    min-height: 44px;

    padding-inline: 11px;
}

.field textarea {
    min-height: 130px;

    padding: 11px;

    resize: vertical;

    line-height: 2;
}

.field input:focus,
.field textarea:focus,
.field select:focus {
    border-color: var(--border-orange);
}

.field input:focus-visible,
.field textarea:focus-visible,
.field select:focus-visible {
    box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.08);
}

.field input[aria-invalid='true'],
.field textarea[aria-invalid='true'],
.field select[aria-invalid='true'] {
    border-color: var(--danger);
}

.field input::placeholder,
.field textarea::placeholder {
    color: #555c62;
}

.field select option {
    color: #fff;

    background: #0b0d0f;
}

.input-with-icon {
    min-height: 44px;

    display: flex;
    align-items: center;

    gap: 8px;

    padding-inline: 10px;

    border: 1px solid var(--border);
    border-radius: 8px;

    background: rgba(0, 0, 0, 0.2);

    transition: border-color var(--transition);
}

.input-with-icon:focus-within {
    border-color: var(--border-orange);
}

.input-with-icon:has(input[aria-invalid='true']) {
    border-color: var(--danger);
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
}

.input-with-icon input:focus,
.input-with-icon input:focus-visible {
    box-shadow: none;
}

.budget-row {
    display: grid;

    grid-template-columns: 1.4fr 0.6fr;

    gap: 10px;
}

.field-error {
    margin-top: 5px;

    color: var(--danger);

    font-family: inherit;

    font-size: 10px;
    line-height: 1.6;
}

.general-error {
    margin-bottom: 16px;

    padding: 10px;

    color: var(--danger);

    border: 1px solid rgba(255, 92, 92, 0.14);
    border-radius: 7px;

    background: rgba(255, 92, 92, 0.025);

    font-family: inherit;

    font-size: 10px;
    line-height: 1.7;
}

/* Footer */

.form-footer {
    display: flex;

    gap: 8px;

    margin-top: 8px;
}

.submit-button,
.cancel-button,
.close-button {
    min-height: 44px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

    border-radius: 8px;

    font-family: inherit;

    font-size: 12px;
    font-weight: 700;

    cursor: pointer;
}

.submit-button {
    flex: 1;

    color: #fff;

    background: var(--orange);

    transition:
        background var(--transition),
        transform var(--transition);
}

.submit-button:hover:not(:disabled) {
    background: var(--orange-light);

    transform: translateY(-2px);
}

.submit-button:disabled,
.cancel-button:disabled {
    opacity: 0.7;

    cursor: wait;
}

.submit-button:focus-visible,
.cancel-button:focus-visible,
.close-button:focus-visible {
    outline: 2px solid var(--orange);

    outline-offset: 3px;
}

.cancel-button {
    padding-inline: 18px;

    color: var(--text-primary);

    border: 1px solid var(--border);

    background: transparent;

    transition:
        border-color var(--transition),
        background var(--transition);
}

.cancel-button:hover:not(:disabled) {
    border-color: var(--border-orange);

    background: rgba(255, 107, 0, 0.03);
}

.loader {
    animation: spin 0.8s linear infinite;
}

/* Success */

.success-state {
    min-height: 300px;

    display: flex;
    flex-direction: column;

    align-items: center;
    justify-content: center;

    text-align: center;
}

.success-icon {
    width: 58px;
    height: 58px;

    display: flex;
    align-items: center;
    justify-content: center;

    color: var(--success);

    border: 1px solid rgba(55, 214, 122, 0.16);
    border-radius: 14px;

    background: rgba(55, 214, 122, 0.04);
}

.success-state h3 {
    margin-top: 18px;

    color: var(--text-primary);

    font-family: inherit;

    font-size: 19px;
    font-weight: 750;
}

.success-state p {
    max-width: 430px;

    margin-top: 8px;

    color: var(--text-muted);

    font-family: inherit;

    font-size: 11px;
    line-height: 1.8;
}

.close-button {
    margin-top: 20px;

    padding-inline: 18px;

    color: #fff;

    background: var(--orange);

    transition:
        background var(--transition),
        transform var(--transition);
}

.close-button:hover {
    background: var(--orange-light);

    transform: translateY(-2px);
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

@media (prefers-reduced-motion: reduce) {
    .submit-button,
    .cancel-button,
    .close-button,
    .loader {
        transition: none;
        animation: none;
    }

    .submit-button:hover:not(:disabled),
    .close-button:hover {
        transform: none;
    }
}

@media (max-width: 520px) {
    .request-form {
        padding: 18px 14px;
    }

    .budget-row {
        grid-template-columns: 1fr;
    }

    .form-footer {
        flex-direction: column;
    }

    .cancel-button {
        width: 100%;
    }
}
</style>
