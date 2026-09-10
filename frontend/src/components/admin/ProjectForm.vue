<script setup>
import { reactive, ref, watch } from 'vue'
import {
    ImagePlus,
    LoaderCircle,
    Save,
    Upload,
    X,
} from 'lucide-vue-next'

import { useAdminProjectsStore } from '../../stores/adminProjects'

const props = defineProps({
    project: {
        type: Object,
        default: null,
    },
})

const emit = defineEmits([
    'saved',
    'cancel',
])

const store = useAdminProjectsStore()

const isEdit = ref(false)

const form = reactive({
    title: '',
    slug: '',
    short_description: '',
    description: '',
    price: '',
    currency: 'IRR',
    is_for_sale: false,
    is_featured: false,
    status: 'draft',
})

const projectFile = ref(null)
const imageFiles = ref([])

const resetForm = () => {
    form.title = ''
    form.slug = ''
    form.short_description = ''
    form.description = ''
    form.price = ''
    form.currency = 'IRR'
    form.is_for_sale = false
    form.is_featured = false
    form.status = 'draft'

    projectFile.value = null
    imageFiles.value = []

    if (store.clearErrors) {
        store.clearErrors()
    }
}

const fillForm = () => {
    if (!props.project) {
        isEdit.value = false
        resetForm()

        return
    }

    isEdit.value = true

    form.title =
        props.project.title || ''

    form.slug =
        props.project.slug || ''

    form.short_description =
        props.project.short_description || ''

    form.description =
        props.project.description || ''

    form.price =
        props.project.price ?? ''

    form.currency =
        props.project.currency || 'IRR'

    form.is_for_sale =
        Boolean(props.project.is_for_sale)

    form.is_featured =
        Boolean(props.project.is_featured)

    form.status =
        props.project.status || 'draft'

    projectFile.value = null
    imageFiles.value = []

    if (store.clearErrors) {
        store.clearErrors()
    }
}

watch(
    () => props.project,
    fillForm,
    {
        immediate: true,
    }
)

const handleProjectFile = (event) => {
    projectFile.value =
        event.target.files?.[0] || null
}

const handleImages = (event) => {
    imageFiles.value = Array.from(
        event.target.files || []
    )
}

const buildFormData = () => {
    const data = new FormData()

    data.append(
        'title',
        form.title.trim()
    )

    if (form.slug.trim()) {
        data.append(
            'slug',
            form.slug.trim()
        )
    }

    data.append(
        'short_description',
        form.short_description.trim()
    )

    data.append(
        'description',
        form.description.trim()
    )

    if (form.price !== '') {
        data.append(
            'price',
            form.price
        )
    }

    data.append(
        'currency',
        form.currency
    )

    data.append(
        'is_for_sale',
        form.is_for_sale ? '1' : '0'
    )

    data.append(
        'is_featured',
        form.is_featured ? '1' : '0'
    )

    data.append(
        'status',
        form.status
    )

    if (projectFile.value) {
        data.append(
            'file',
            projectFile.value
        )
    }

    return data
}

const submit = async () => {
    if (store.clearErrors) {
        store.clearErrors()
    }

    const data = buildFormData()

    let result

    if (isEdit.value) {
        result = await store.updateProject(
            props.project.slug,
            data
        )
    } else {
        result = await store.createProject(
            data
        )
    }

    if (!result?.success) {
        return
    }

    /*
     * Images use a separate API endpoint.
     */
    if (imageFiles.value.length > 0) {
        const imageResult =
            await store.uploadImages(
                result.project.slug,
                imageFiles.value
            )

        if (
            imageResult &&
            imageResult.success === false
        ) {
            return
        }
    }

    emit(
        'saved',
        result.project
    )

    resetForm()
}
</script>

<template>
    <form
        class="project-form"
        aria-labelledby="project-form-title"
        @submit.prevent="submit"
    >
        <!-- Header -->
        <header class="form-header">
            <div>
                <span>
                    {{
                        isEdit
                            ? 'EDIT PROJECT'
                            : 'NEW PROJECT'
                    }}
                </span>

                <h2 id="project-form-title">
                    {{
                        isEdit
                            ? 'ویرایش پروژه'
                            : 'ایجاد پروژه جدید'
                    }}
                </h2>
            </div>

            <button
                type="button"
                class="close-button"
                aria-label="بستن فرم"
                :disabled="store.saving"
                @click="emit('cancel')"
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
            id="project-form-error"
            class="error-box"
            role="alert"
            aria-live="assertive"
        >
            {{ store.error }}
        </div>

        <!-- Basic -->
        <div class="form-grid">
            <div class="field full">
                <label for="project-title">
                    عنوان پروژه
                </label>

                <input
                    id="project-title"
                    v-model="form.title"
                    type="text"
                    maxlength="255"
                    autocomplete="off"
                    required
                    :aria-invalid="
                        !!store.errors.title
                    "
                    :aria-describedby="
                        store.errors.title
                            ? 'project-title-error'
                            : undefined
                    "
                />

                <span
                    v-if="store.errors.title"
                    id="project-title-error"
                    class="field-error"
                >
                    {{ store.errors.title[0] }}
                </span>
            </div>

            <div class="field">
                <label for="project-slug">
                    Slug
                </label>

                <input
                    id="project-slug"
                    v-model="form.slug"
                    type="text"
                    maxlength="255"
                    autocomplete="off"
                    placeholder="my-project"
                    dir="ltr"
                    :aria-invalid="
                        !!store.errors.slug
                    "
                    :aria-describedby="
                        store.errors.slug
                            ? 'project-slug-error'
                            : undefined
                    "
                />

                <span
                    v-if="store.errors.slug"
                    id="project-slug-error"
                    class="field-error"
                >
                    {{ store.errors.slug[0] }}
                </span>
            </div>

            <div class="field">
                <label for="project-status">
                    وضعیت
                </label>

                <select
                    id="project-status"
                    v-model="form.status"
                >
                    <option value="draft">
                        پیش‌نویس
                    </option>

                    <option value="published">
                        منتشر شده
                    </option>

                    <option value="archived">
                        آرشیو
                    </option>
                </select>
            </div>

            <div class="field full">
                <label for="project-short-description">
                    توضیح کوتاه
                </label>

                <input
                    id="project-short-description"
                    v-model="form.short_description"
                    type="text"
                    maxlength="1000"
                    :aria-invalid="
                        !!store.errors.short_description
                    "
                    :aria-describedby="
                        store.errors.short_description
                            ? 'project-short-description-error'
                            : undefined
                    "
                />

                <span
                    v-if="store.errors.short_description"
                    id="project-short-description-error"
                    class="field-error"
                >
                    {{
                        store.errors
                            .short_description[0]
                    }}
                </span>
            </div>

            <div class="field full">
                <label for="project-description">
                    توضیحات
                </label>

                <textarea
                    id="project-description"
                    v-model="form.description"
                    rows="7"
                    maxlength="50000"
                    :aria-invalid="
                        !!store.errors.description
                    "
                    :aria-describedby="
                        store.errors.description
                            ? 'project-description-error'
                            : undefined
                    "
                ></textarea>

                <span
                    v-if="store.errors.description"
                    id="project-description-error"
                    class="field-error"
                >
                    {{ store.errors.description[0] }}
                </span>
            </div>
        </div>

        <!-- Commercial -->
        <div class="section-title">
            فروش
        </div>

        <div class="commercial-grid">
            <div class="field">
                <label for="project-price">
                    قیمت
                </label>

                <input
                    id="project-price"
                    v-model="form.price"
                    type="number"
                    min="0"
                    step="0.01"
                    inputmode="decimal"
                    dir="ltr"
                    :aria-invalid="
                        !!store.errors.price
                    "
                    :aria-describedby="
                        store.errors.price
                            ? 'project-price-error'
                            : undefined
                    "
                />

                <span
                    v-if="store.errors.price"
                    id="project-price-error"
                    class="field-error"
                >
                    {{ store.errors.price[0] }}
                </span>
            </div>

            <div class="field">
                <label for="project-currency">
                    واحد پول
                </label>

                <select
                    id="project-currency"
                    v-model="form.currency"
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
            </div>
        </div>

        <div
            class="switches"
            aria-label="تنظیمات فروش و نمایش"
        >
            <label class="switch">
                <input
                    v-model="form.is_for_sale"
                    type="checkbox"
                />

                <span>
                    قابل فروش
                </span>
            </label>

            <label class="switch">
                <input
                    v-model="form.is_featured"
                    type="checkbox"
                />

                <span>
                    پروژه منتخب
                </span>
            </label>
        </div>

        <!-- Files -->
        <div class="section-title">
            فایل پروژه
        </div>

        <label class="upload-box">
            <Upload
                :size="18"
                aria-hidden="true"
            />

            <div>
                <strong>
                    {{
                        projectFile?.name ||
                        'انتخاب فایل ZIP'
                    }}
                </strong>

                <span>
                    حداکثر 500MB
                </span>
            </div>

            <input
                type="file"
                accept=".zip,application/zip"
                :aria-label="`انتخاب فایل پروژه ${
                    projectFile?.name || ''
                }`"
                @change="handleProjectFile"
            />
        </label>

        <span
            v-if="store.errors.file"
            class="field-error"
        >
            {{ store.errors.file[0] }}
        </span>

        <!-- Images -->
        <div class="section-title">
            تصاویر
        </div>

        <label class="upload-box images">
            <ImagePlus
                :size="18"
                aria-hidden="true"
            />

            <div>
                <strong>
                    {{
                        imageFiles.length
                            ? `${imageFiles.length} تصویر انتخاب شد`
                            : 'انتخاب تصاویر'
                    }}
                </strong>

                <span>
                    JPG / PNG / WEBP — حداکثر 20 فایل
                </span>
            </div>

            <input
                type="file"
                accept="image/jpeg,image/png,image/webp"
                multiple
                aria-label="انتخاب تصاویر پروژه"
                @change="handleImages"
            />
        </label>

        <span
            v-if="store.errors.images"
            class="field-error"
        >
            {{ store.errors.images[0] }}
        </span>

        <!-- Actions -->
        <div class="form-actions">
            <button
                type="button"
                class="secondary-button"
                :disabled="store.saving"
                @click="emit('cancel')"
            >
                انصراف
            </button>

            <button
                type="submit"
                class="primary-button"
                :disabled="store.saving"
                :aria-busy="store.saving"
            >
                <LoaderCircle
                    v-if="store.saving"
                    :size="16"
                    class="spin"
                    aria-hidden="true"
                />

                <Save
                    v-else
                    :size="16"
                    aria-hidden="true"
                />

                {{
                    store.saving
                        ? 'در حال ذخیره...'
                        : 'ذخیره پروژه'
                }}
            </button>
        </div>
    </form>
</template>

<style scoped>
.project-form {
    width: 100%;
}

.form-header {
    display: flex;

    align-items: center;
    justify-content: space-between;

    margin-bottom: 23px;
}

.form-header span {
    color: var(--orange);

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 9px;
    font-weight: 600;
}

.form-header h2 {
    margin-top: 5px;

    color: var(--text-primary);

    font-family: inherit;

    font-size: 20px;
    font-weight: 850;
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

    background: rgba(255, 255, 255, 0.02);

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
.secondary-button:focus-visible {
    outline: 2px solid var(--orange);

    outline-offset: 3px;
}

.error-box {
    margin-bottom: 16px;

    padding: 10px 12px;

    color: var(--danger);

    border: 1px solid rgba(255, 92, 92, 0.14);

    border-radius: 8px;

    background: rgba(255, 92, 92, 0.025);

    font-family: inherit;

    font-size: 10px;
    line-height: 1.7;
}

.form-grid {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 14px;
}

.field {
    display: flex;

    flex-direction: column;
}

.field.full {
    grid-column: 1 / -1;
}

.field label {
    margin-bottom: 7px;

    color: var(--text-secondary);

    font-family: inherit;

    font-size: 11px;

    font-weight: 700;
}

.field input,
.field select,
.field textarea {
    width: 100%;

    border: 1px solid var(--border);

    border-radius: 8px;

    outline: 0;

    color: var(--text-primary);

    background: rgba(255, 255, 255, 0.025);

    font-family: inherit;

    font-size: 12px;

    transition: border-color var(--transition);
}

.field input,
.field select {
    min-height: 42px;

    padding-inline: 11px;
}

.field textarea {
    min-height: 145px;

    padding: 11px;

    resize: vertical;

    line-height: 1.9;
}

.field input:focus,
.field select:focus,
.field textarea:focus {
    border-color: var(--border-orange);
}

.field input:focus-visible,
.field select:focus-visible,
.field textarea:focus-visible {
    box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.08);
}

.field input[aria-invalid='true'],
.field select[aria-invalid='true'],
.field textarea[aria-invalid='true'] {
    border-color: var(--danger);
}

.field select option {
    color: #fff;

    background: #0d0d0d;
}

.field-error {
    margin-top: 5px;

    color: var(--danger);

    font-family: inherit;

    font-size: 10px;

    line-height: 1.6;
}

.section-title {
    margin: 24px 0 12px;

    padding-bottom: 8px;

    color: var(--text-muted);

    border-bottom: 1px solid rgba(255, 255, 255, 0.05);

    font-family: inherit;

    font-size: 11px;

    font-weight: 800;
}

.commercial-grid {
    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 12px;
}

.switches {
    display: flex;

    gap: 18px;

    margin-top: 14px;
}

.switch {
    display: flex;

    align-items: center;

    gap: 7px;

    color: var(--text-secondary);

    font-family: inherit;

    font-size: 10px;

    cursor: pointer;
}

.switch input {
    accent-color: var(--orange);
}

.upload-box {
    position: relative;

    min-height: 66px;

    display: flex;

    align-items: center;

    gap: 11px;

    padding: 12px;

    overflow: hidden;

    color: var(--orange);

    border: 1px dashed rgba(255, 107, 0, 0.2);

    border-radius: 9px;

    background: rgba(255, 107, 0, 0.02);

    cursor: pointer;

    transition:
        border-color var(--transition),
        background var(--transition);
}

.upload-box:hover {
    border-color: rgba(255, 107, 0, 0.4);

    background: rgba(255, 107, 0, 0.035);
}

.upload-box:focus-within {
    border-color: var(--border-orange);

    box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.08);
}

.upload-box > div {
    min-width: 0;

    display: flex;

    flex-direction: column;

    gap: 4px;
}

.upload-box strong {
    overflow: hidden;

    color: var(--text-secondary);

    font-family: inherit;

    font-size: 10px;

    text-overflow: ellipsis;
    white-space: nowrap;
}

.upload-box span {
    color: var(--text-muted);

    font-family: inherit;

    font-size: 9px;
}

.upload-box input {
    position: absolute;

    inset: 0;

    width: 100%;
    height: 100%;

    opacity: 0;

    cursor: pointer;
}

.form-actions {
    display: flex;

    justify-content: flex-end;

    gap: 8px;

    margin-top: 25px;
}

.primary-button,
.secondary-button {
    min-height: 40px;

    display: inline-flex;

    align-items: center;
    justify-content: center;

    gap: 7px;

    padding-inline: 15px;

    border-radius: 8px;

    font-family: inherit;

    font-size: 11px;

    font-weight: 700;

    cursor: pointer;
}

.primary-button {
    color: #fff;

    background: var(--orange);

    transition:
        background var(--transition),
        opacity var(--transition);
}

.primary-button:hover:not(:disabled) {
    background: var(--orange-light);
}

.primary-button:disabled {
    opacity: 0.65;

    cursor: wait;
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

.secondary-button:disabled {
    opacity: 0.5;

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
    .upload-box,
    .spin {
        transition: none;
        animation: none;
    }
}

@media (max-width: 600px) {
    .form-grid,
    .commercial-grid {
        grid-template-columns: 1fr;
    }

    .field.full {
        grid-column: auto;
    }

    .switches {
        flex-direction: column;

        gap: 9px;
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
