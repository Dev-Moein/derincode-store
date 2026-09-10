import { defineStore } from 'pinia'
import api from '../services/api'


export const useAdminStore = defineStore(
    'admin',
    {

        state: () => ({

            dashboard: null,

            loading: false,

            error: null,

            errors: {},

        }),



        getters: {

            stats: (state) =>
                state.dashboard?.stats || {},


            recentUsers: (state) =>
                state.dashboard?.recent_users || [],


            recentPayments: (state) =>
                state.dashboard?.recent_payments || [],


            recentProjectRequests: (state) =>
                state.dashboard?.recent_project_requests || [],

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
                    'دریافت اطلاعات داشبورد انجام نشد.'

            },



            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */

            async fetchDashboard() {

                this.loading = true

                this.clearErrors()


                try {

                    const response =
                        await api.get(
                            '/admin/dashboard'
                        )


                    const dashboard =
                        response?.data?.data


                    if (!dashboard) {

                        throw new Error(
                            'Invalid admin dashboard response.'
                        )

                    }


                    this.dashboard =
                        dashboard


                    return {

                        success: true,

                        data:
                            this.dashboard,

                    }


                } catch (error) {

                    this.setError(error)


                    this.dashboard = null


                    return {

                        success: false,

                        error,

                    }

                } finally {

                    this.loading = false

                }

            },

        },

    }
)
