import { defineStore } from 'pinia'
import api from '../services/api'

export const usePaymentsStore = defineStore(
    'payments',
    {
        state: () => ({
            payments: [],
            currentPayment: null,

            loading: false,
            error: null,
            errors: {},

            pagination: {
                currentPage: 1,
                lastPage: 1,
                perPage: 10,
                total: 0,
            },
        }),

        getters: {
            successfulPayments: (state) => {
                return state.payments.filter(
                    (payment) =>
                        payment.status === 'successful'
                )
            },

            pendingPayments: (state) => {
                return state.payments.filter(
                    (payment) =>
                        payment.status === 'pending'
                )
            },

            hasPayments: (state) => {
                return state.payments.length > 0
            },
        },

        actions: {
            clearErrors() {
                this.error = null
                this.errors = {}
            },

            setError(error) {
                this.errors =
                    error?.response?.data?.errors || {}

                this.error =
                    error?.response?.data?.message ||
                    'عملیات پرداخت انجام نشد.'
            },

            async createPayment({
                projectId,
                gateway = 'zarinpal',
            }) {
                this.loading = true
                this.clearErrors()

                try {
                    const response =
                        await api.post(
                            '/payments',
                            {
                                project_id: projectId,
                                gateway,
                            }
                        )

                    const data =
                        response?.data?.data

                    if (
                        !data?.payment ||
                        !data?.payment_url
                    ) {
                        throw new Error(
                            'Invalid payment response.'
                        )
                    }

                    this.currentPayment =
                        data.payment

                    return {
                        success: true,

                        payment:
                            data.payment,

                        paymentUrl:
                            data.payment_url,

                        message:
                            response?.data?.message ||
                            'پرداخت ایجاد شد.',
                    }
                } catch (error) {
                    this.setError(error)

                    return {
                        success: false,
                        error,
                    }
                } finally {
                    this.loading = false
                }
            },

            async fetchMyPayments(
                params = {}
            ) {
                this.loading = true
                this.clearErrors()

                try {
                    const response =
                        await api.get(
                            '/payments',
                            {
                                params: {
                                    per_page: 10,
                                    ...params,
                                },
                            }
                        )

                    const data =
                        response?.data?.data

                    this.payments =
                        Array.isArray(data)
                            ? data
                            : []

                    const meta =
                        response?.data?.meta

                    if (meta) {
                        this.pagination.currentPage =
                            meta.current_page ?? 1

                        this.pagination.lastPage =
                            meta.last_page ?? 1

                        this.pagination.perPage =
                            meta.per_page ?? 10

                        this.pagination.total =
                            meta.total ??
                            this.payments.length
                    }

                    return {
                        success: true,
                    }
                } catch (error) {
                    this.setError(error)

                    return {
                        success: false,
                        error,
                    }
                } finally {
                    this.loading = false
                }
            },

            async fetchPayment(id) {
                this.loading = true
                this.clearErrors()

                try {
                    const response =
                        await api.get(
                            `/payments/${id}`
                        )

                    const payment =
                        response?.data?.data

                    if (!payment) {
                        throw new Error(
                            'Invalid payment response.'
                        )
                    }

                    this.currentPayment =
                        payment

                    return {
                        success: true,
                        payment,
                    }
                } catch (error) {
                    this.setError(error)

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
