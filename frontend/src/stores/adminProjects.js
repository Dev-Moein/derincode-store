import { defineStore } from 'pinia'
import api from '../services/api'


export const useAdminProjectsStore = defineStore(
    'adminProjects',
    {

        state: () => ({

            projects: [],

            loading: false,

            saving: false,

            deleting: false,

            error: null,

            errors: {},


            filters: {

                search: '',

                status: '',

                is_featured: '',

                is_for_sale: '',

                per_page: 12,

                page: 1,

            },


            pagination: {

                currentPage: 1,

                lastPage: 1,

                perPage: 12,

                total: 0,

            },


            currentProject: null,

        }),



        getters: {

            hasProjects: (state) =>
                state.projects.length > 0,


            isEmpty: (state) =>
                !state.loading &&
                state.projects.length === 0,

        },



        actions: {


            /*
            |--------------------------------------------------------------------------
            | Errors
            |--------------------------------------------------------------------------
            */

            clearErrors() {

                this.error = null

                this.errors = {}

            },


            setError(error) {

                this.errors =
                    error?.apiErrors ||
                    error?.response?.data?.errors ||
                    {}


                this.error =
                    error?.apiMessage ||
                    error?.response?.data?.message ||
                    error?.message ||
                    'عملیات انجام نشد.'

            },



            /*
            |--------------------------------------------------------------------------
            | Filters
            |--------------------------------------------------------------------------
            */

            resetFilters() {

                this.filters = {

                    search: '',

                    status: '',

                    is_featured: '',

                    is_for_sale: '',

                    per_page: 12,

                    page: 1,

                }

            },



            /*
            |--------------------------------------------------------------------------
            | Fetch Projects
            |--------------------------------------------------------------------------
            */

            async fetchProjects() {

                this.loading = true

                this.clearErrors()


                try {

                    const params = {

                        page:
                            this.filters.page,

                        per_page:
                            this.filters.per_page,

                    }


                    if (
                        this.filters.search
                    ) {

                        params.search =
                            this.filters.search

                    }


                    if (
                        this.filters.status
                    ) {

                        params.status =
                            this.filters.status

                    }


                    if (
                        this.filters.is_featured !== ''
                    ) {

                        params.is_featured =
                            this.filters.is_featured

                    }


                    if (
                        this.filters.is_for_sale !== ''
                    ) {

                        params.is_for_sale =
                            this.filters.is_for_sale

                    }


                    const response =
                        await api.get(
                            '/projects',
                            {
                                params,
                            }
                        )


                    const data =
                        response?.data?.data


                    this.projects =
                        Array.isArray(data)
                            ? data
                            : []


                    const meta =
                        response?.data?.meta


                    if (meta) {

                        this.pagination = {

                            currentPage:
                                meta.current_page ?? 1,

                            lastPage:
                                meta.last_page ?? 1,

                            perPage:
                                meta.per_page ??
                                this.filters.per_page,

                            total:
                                meta.total ??
                                this.projects.length,

                        }

                    } else {

                        this.pagination = {

                            currentPage: 1,

                            lastPage: 1,

                            perPage:
                                this.filters.per_page,

                            total:
                                this.projects.length,

                        }

                    }


                    return {

                        success: true,

                        projects:
                            this.projects,

                    }


                } catch (error) {

                    this.setError(error)

                    this.projects = []


                    return {

                        success: false,

                        error,

                    }

                } finally {

                    this.loading = false

                }

            },



            /*
            |--------------------------------------------------------------------------
            | Create Project
            |--------------------------------------------------------------------------
            */

            async createProject(formData) {

                this.saving = true

                this.clearErrors()


                try {

                    const response =
                        await api.post(
                            '/projects',
                            formData,
                            {
                                headers: {
                                    'Content-Type':
                                        'multipart/form-data',
                                },
                            }
                        )


                    const project =
                        response?.data?.data


                    if (!project) {

                        throw new Error(
                            'Invalid project response.'
                        )

                    }


                    this.currentProject =
                        project


                    return {

                        success: true,

                        project,

                        message:
                            response?.data?.message ||
                            'پروژه با موفقیت ایجاد شد.',

                    }


                } catch (error) {

                    this.setError(error)


                    return {

                        success: false,

                        error,

                    }

                } finally {

                    this.saving = false

                }

            },



            /*
            |--------------------------------------------------------------------------
            | Update Project
            |--------------------------------------------------------------------------
            */

            async updateProject(
                slug,
                formData
            ) {

                this.saving = true

                this.clearErrors()


                try {

                    /*
                    |--------------------------------------------------------------------------
                    | Laravel multipart PUT workaround
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !(formData instanceof FormData)
                    ) {

                        throw new Error(
                            'Invalid form data.'
                        )

                    }


                    formData.append(
                        '_method',
                        'PUT'
                    )


                    const response =
                        await api.post(
                            `/projects/${slug}`,
                            formData,
                            {
                                headers: {
                                    'Content-Type':
                                        'multipart/form-data',
                                },
                            }
                        )


                    const project =
                        response?.data?.data


                    if (!project) {

                        throw new Error(
                            'Invalid project response.'
                        )

                    }


                    this.currentProject =
                        project


                    const index =
                        this.projects.findIndex(
                            (item) =>
                                item.slug ===
                                project.slug
                        )


                    if (index !== -1) {

                        this.projects[index] =
                            project

                    }


                    return {

                        success: true,

                        project,

                        message:
                            response?.data?.message ||
                            'پروژه با موفقیت به‌روزرسانی شد.',

                    }


                } catch (error) {

                    this.setError(error)


                    return {

                        success: false,

                        error,

                    }

                } finally {

                    this.saving = false

                }

            },



            /*
            |--------------------------------------------------------------------------
            | Delete Project
            |--------------------------------------------------------------------------
            */

            async deleteProject(slug) {

                this.deleting = true

                this.clearErrors()


                try {

                    const response =
                        await api.delete(
                            `/projects/${slug}`
                        )


                    this.projects =
                        this.projects.filter(
                            (project) =>
                                project.slug !==
                                slug
                        )


                    this.pagination.total =
                        Math.max(
                            0,
                            this.pagination.total - 1
                        )


                    return {

                        success: true,

                        message:
                            response?.data?.message ||
                            'پروژه حذف شد.',

                    }


                } catch (error) {

                    this.setError(error)


                    return {

                        success: false,

                        error,

                    }

                } finally {

                    this.deleting = false

                }

            },



            /*
            |--------------------------------------------------------------------------
            | Upload Images
            |--------------------------------------------------------------------------
            */

            async uploadImages(
                slug,
                files,
                alt = ''
            ) {

                this.saving = true

                this.clearErrors()


                try {

                    if (
                        !files ||
                        files.length === 0
                    ) {

                        throw new Error(
                            'حداقل یک تصویر انتخاب کنید.'
                        )

                    }


                    const formData =
                        new FormData()


                    Array.from(files).forEach(
                        (file) => {

                            formData.append(
                                'images[]',
                                file
                            )

                        }
                    )


                    if (alt) {

                        formData.append(
                            'alt',
                            alt
                        )

                    }


                    const response =
                        await api.post(
                            `/projects/${slug}/images`,
                            formData,
                            {
                                headers: {
                                    'Content-Type':
                                        'multipart/form-data',
                                },
                            }
                        )


                    return {

                        success: true,

                        images:
                            response?.data?.data ||
                            [],

                        message:
                            response?.data?.message ||
                            'تصاویر با موفقیت آپلود شدند.',

                    }


                } catch (error) {

                    this.setError(error)


                    return {

                        success: false,

                        error,

                    }

                } finally {

                    this.saving = false

                }

            },



            /*
            |--------------------------------------------------------------------------
            | Delete Image
            |--------------------------------------------------------------------------
            */

            async deleteImage(imageId) {

                this.saving = true

                this.clearErrors()


                try {

                    const response =
                        await api.delete(
                            `/projects/images/${imageId}`
                        )


                    return {

                        success: true,

                        message:
                            response?.data?.message ||
                            'تصویر حذف شد.',

                    }


                } catch (error) {

                    this.setError(error)


                    return {

                        success: false,

                        error,

                    }

                } finally {

                    this.saving = false

                }

            },



            /*
            |--------------------------------------------------------------------------
            | Reorder Images
            |--------------------------------------------------------------------------
            */

            async reorderImages(
                slug,
                images
            ) {

                this.saving = true

                this.clearErrors()


                try {

                    const response =
                        await api.put(
                            `/projects/${slug}/images/reorder`,
                            {
                                images,
                            }
                        )


                    return {

                        success: true,

                        images:
                            response?.data?.data ||
                            [],

                        message:
                            response?.data?.message ||
                            'ترتیب تصاویر ذخیره شد.',

                    }


                } catch (error) {

                    this.setError(error)


                    return {

                        success: false,

                        error,

                    }

                } finally {

                    this.saving = false

                }

            },


        },

    }
)
