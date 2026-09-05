import { defineStore } from 'pinia'
import api from '../services/api'

export const useProjectsStore = defineStore('projects', {
    state: () => ({
        projects: [],
        loading: false,
        error: null,

        pagination: {
            currentPage: 1,
            lastPage: 1,
            perPage: 6,
            total: 0,
        },
    }),

    getters: {
        featuredProjects: (state) => state.projects,
    },

    actions: {
        async fetchFeaturedProjects() {
            this.loading = true
            this.error = null

            try {
                const response = await api.get('/projects', {
                    params: {
                        is_featured: 1,
                        per_page: 4,
                    },
                })

                /*
                 * Laravel Resource Collection with pagination:
                 *
                 * {
                 *   data: [],
                 *   links: {},
                 *   meta: {}
                 * }
                 */

                const data = response?.data?.data

                this.projects = Array.isArray(data)
                    ? data
                    : []

                const meta = response?.data?.meta

                if (meta) {
                    this.pagination.currentPage =
                        meta.current_page ?? 1

                    this.pagination.lastPage =
                        meta.last_page ?? 1

                    this.pagination.perPage =
                        meta.per_page ?? this.projects.length

                    this.pagination.total =
                        meta.total ?? this.projects.length
                }
            } catch (error) {
                console.error(
                    'Failed to fetch projects:',
                    error
                )

                this.error =
                    error?.response?.data?.message ||
                    'دریافت نمونه‌کارها با مشکل مواجه شد.'

                this.projects = []
            } finally {
                this.loading = false
            }
        },
    },
})
