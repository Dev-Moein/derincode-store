<script setup>
import { reactive, ref } from 'vue'
import {
    ArrowLeft,
    CheckCircle2,
    FileText,
    LoaderCircle,
    Wallet,
} from 'lucide-vue-next'

import { useAuthStore } from '../../stores/auth'
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

const authStore = useAuthStore()
const requestsStore = useProjectRequestsStore()

const form = reactive({
    title: props.project.title || '',
    description: '',
    budget: '',
    currency: 'IRR',
})

const success = ref(false)
const successMessage = ref('')

const submit = async () => {
    const result =
        await requestsStore.createRequest({
            title: form.title,
            description: form.description,
            budget:
                form.budget === ''
                    ? null
                    : Number(form.budget),
            currency: form.currency,
        })

    if (!result.success) {
        return
    }

    success.value = true
    successMessage.value =
        result.message

    emit('success', result.request)
}
</script>

<template>
    <div class="request-form">

        <div class="form-heading">
            <div class="heading-icon">
                <FileText :size="19" />
            </div>

            <div>
                <span>
                    PROJECT REQUEST
                </span>

                <h3>
                    درخواست پروژه
                </h3>
            </div>
        </div>

        <!-- Success -->
        <div
            v-if="success"
            class="success-state"
        >
            <div class="success-icon">
                <CheckCircle2 :size="30" />
            </div>

            <h3>
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
        </div>

        <!-- Form -->
        <form
            v-else
            @submit.prevent="submit"
        >
            <!-- Error -->
            <div
                v-if="requestsStore.error"
                class="general-error"
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
                    required
                />

                <span
                    v-if="requestsStore.errors.title"
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
                ></textarea>

                <span
                    v-if="
                        requestsStore.errors.description
                    "
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
                        بودجه
                        <small>(اختیاری)</small>
                    </label>

                    <div class="input-with-icon">
                        <Wallet :size="15" />

                        <input
                            id="request-budget"
                            v-model="form.budget"
                            type="number"
                            min="0"
                            step="0.01"
                            placeholder="مثلاً 50000000"
                        />
                    </div>

                    <span
                        v-if="requestsStore.errors.budget"
                        class="field-error"
                    >
                        {{ requestsStore.errors.budget[0] }}
                    </span>
                </div>

                <div class="field">
                    <label for="request-currency">
                        واحد پول
                    </label>

                    <select
                        id="request-currency"
                        v-model="form.currency"
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
                        v-if="requestsStore.errors.currency"
                        class="field-error"
                    >
                        {{ requestsStore.errors.currency[0] }}
                    </span>
                </div>
            </div>

            <div class="form-footer">
                <button
                    type="submit"
                    class="submit-button"
                    :disabled="requestsStore.loading"
                >
                    <span v-if="!requestsStore.loading">
                        ثبت درخواست
                    </span>

                    <LoaderCircle
                        v-else
                        :size="16"
                        class="loader"
                    />

                    <ArrowLeft
                        v-if="!requestsStore.loading"
                        :size="16"
                    />
                </button>

                <button
                    type="button"
                    class="cancel-button"
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

    font-size: 7px;
}

.form-heading h3 {
    margin-top: 2px;

    color: var(--text-primary);

    font-size: 14px;
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

    font-size: 9px;
    font-weight: 700;
}

.field label small {
    color: var(--text-muted);

    font-size: 8px;
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

    font-size: 10px;

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

.input-with-icon svg {
    color: #656c72;
}

.input-with-icon input {
    min-height: 40px;

    padding: 0;

    border: 0;

    background: transparent;
}

.budget-row {
    display: grid;

    grid-template-columns: 1.4fr 0.6fr;

    gap: 10px;
}

.field-error {
    margin-top: 5px;

    color: var(--danger);

    font-size: 8px;
}

.general-error {
    margin-bottom: 16px;

    padding: 10px;

    color: var(--danger);

    border: 1px solid rgba(255, 92, 92, 0.14);
    border-radius: 7px;

    background: rgba(255, 92, 92, 0.025);

    font-size: 8px;
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

    font-size: 10px;
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

.submit-button:disabled {
    opacity: 0.7;

    cursor: wait;
}

.cancel-button {
    padding-inline: 18px;

    color: var(--text-primary);

    border: 1px solid var(--border);

    background: transparent;
}

.cancel-button:hover {
    border-color: var(--border-orange);
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

    font-size: 17px;
}

.success-state p {
    margin-top: 7px;

    color: var(--text-muted);

    font-size: 10px;
}

.close-button {
    margin-top: 20px;

    padding-inline: 18px;

    color: #fff;

    background: var(--orange);
}

@keyframes spin {
    to {
        transform: rotate(360deg);
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
