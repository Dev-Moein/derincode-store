<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import {
    CheckCircle2,
    XCircle,
    Clock3,
    ArrowRight,
    Download,
} from 'lucide-vue-next'

import api from '../services/api'

const route = useRoute()
const router = useRouter()

const loading = ref(true)
const payment = ref(null)

const status = computed(() => {
    return route.query.status || 'error'
})

const title = computed(() => {
    switch (status.value) {
        case 'success':
            return 'پرداخت با موفقیت انجام شد.'

        case 'cancelled':
            return 'پرداخت لغو شد.'

        case 'failed':
            return 'پرداخت ناموفق بود.'

        default:
            return 'مشکلی در پرداخت رخ داد.'
    }
})

const description = computed(() => {
    switch (status.value) {
        case 'success':
            return 'پرداخت شما تأیید شد و پروژه در حساب شما قرار گرفت.'

        case 'cancelled':
            return 'پرداخت توسط شما لغو شده است.'

        case 'failed':
            return 'پرداخت توسط درگاه تأیید نشد.'

        default:
            return 'اطلاعات پرداخت قابل دریافت نیست.'
    }
})

const fetchPayment = async () => {
    const paymentId = route.query.payment

    if (
        !paymentId ||
        status.value !== 'success'
    ) {
        loading.value = false
        return
    }

    try {
        const response =
            await api.get(
                `/payments/${paymentId}`
            )

        payment.value =
            response?.data?.data || null
    } catch (error) {
        console.error(
            'Failed to fetch payment:',
            error
        )
    } finally {
        loading.value = false
    }
}

const goHome = () => {
    router.push({
        name: 'home',
    })
}

const goProfile = () => {
    router.push({
        name: 'profile',
    })
}

onMounted(fetchPayment)
</script>

<template>
    <main class="payment-result-page">
        <div class="result-card">

            <div
                v-if="status === 'success'"
                class="result-icon success"
            >
                <CheckCircle2 :size="36" />
            </div>

            <div
                v-else-if="
                    status === 'cancelled'
                "
                class="result-icon cancelled"
            >
                <Clock3 :size="36" />
            </div>

            <div
                v-else
                class="result-icon failed"
            >
                <XCircle :size="36" />
            </div>

            <span class="result-kicker">
                PAYMENT RESULT
            </span>

            <h1>
                {{ title }}
            </h1>

            <p>
                {{ description }}
            </p>

            <div
                v-if="
                    !loading &&
                    payment &&
                    status === 'success'
                "
                class="payment-summary"
            >
                <div>
                    <span>
                        شماره پرداخت
                    </span>

                    <strong>
                        #{{ payment.id }}
                    </strong>
                </div>

                <div>
                    <span>
                        مبلغ
                    </span>

                    <strong>
                        {{ payment.amount }}
                        {{ payment.currency }}
                    </strong>
                </div>

                <div>
                    <span>
                        وضعیت
                    </span>

                    <strong>
                        {{ payment.status }}
                    </strong>
                </div>
            </div>

            <div class="result-actions">

                <button
                    v-if="status === 'success'"
                    type="button"
                    class="primary-button"
                    @click="goProfile"
                >
                    <Download :size="16" />
                    مشاهده پروژه‌ها و دانلود
                </button>

                <button
                    type="button"
                    class="secondary-button"
                    @click="goHome"
                >
                    <ArrowRight :size="16" />
                    بازگشت به سایت
                </button>

            </div>
        </div>
    </main>
</template>

<style scoped>
.payment-result-page {
    min-height: 100vh;

    display: flex;
    align-items: center;
    justify-content: center;

    padding: 30px 16px;

    background:
        radial-gradient(
            circle at 50% 0%,
            rgba(255, 107, 0, 0.055),
            transparent 30rem
        ),
        var(--bg-primary);
}

.result-card {
    width: 100%;
    max-width: 500px;

    padding: 35px 28px;

    text-align: center;

    border: 1px solid var(--border);
    border-radius: 17px;

    background:
        linear-gradient(
            145deg,
            rgba(255, 255, 255, 0.03),
            rgba(255, 255, 255, 0.008)
        );

    box-shadow:
        0 30px 90px rgba(0, 0, 0, 0.3);
}

.result-icon {
    width: 70px;
    height: 70px;

    margin: 0 auto 18px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 18px;
}

.result-icon.success {
    color: var(--success);

    border: 1px solid
        rgba(55, 214, 122, 0.17);

    background:
        rgba(55, 214, 122, 0.035);
}

.result-icon.cancelled {
    color: #e6a74a;

    border: 1px solid
        rgba(230, 167, 74, 0.17);

    background:
        rgba(230, 167, 74, 0.035);
}

.result-icon.failed {
    color: var(--danger);

    border: 1px solid
        rgba(255, 92, 92, 0.17);

    background:
        rgba(255, 92, 92, 0.035);
}

.result-kicker {
    color: var(--orange);

    direction: ltr;

    font-family: var(--font-mono);

    font-size: 8px;
}

.result-card h1 {
    margin-top: 9px;

    color: var(--text-primary);

    font-size: 24px;
    font-weight: 900;
}

.result-card > p {
    max-width: 390px;

    margin: 9px auto 0;

    color: var(--text-muted);

    font-size: 10px;
    line-height: 2;
}

.payment-summary {
    margin-top: 22px;

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 7px;
}

.payment-summary > div {
    padding: 9px;

    display: flex;
    flex-direction: column;

    gap: 3px;

    border: 1px solid var(--border);

    border-radius: 8px;

    background:
        rgba(255, 255, 255, 0.012);
}

.payment-summary span {
    color: var(--text-muted);

    font-size: 7px;
}

.payment-summary strong {
    color: var(--text-secondary);

    font-size: 8px;
}

.result-actions {
    display: flex;

    flex-direction: column;

    gap: 8px;

    margin-top: 25px;
}

.primary-button,
.secondary-button {
    min-height: 44px;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 8px;

    border-radius: 8px;

    font-size: 9px;
    font-weight: 700;

    cursor: pointer;
}

.primary-button {
    color: #fff;

    background: var(--orange);

    transition:
        background var(--transition),
        transform var(--transition);
}

.primary-button:hover {
    background: var(--orange-light);

    transform: translateY(-2px);
}

.secondary-button {
    color: var(--text-primary);

    border: 1px solid var(--border);

    background: transparent;
}

.secondary-button:hover {
    border-color: var(--border-orange);
}

@media (max-width: 450px) {
    .result-card {
        padding: 28px 17px;
    }

    .payment-summary {
        grid-template-columns: 1fr;
    }
}
</style>
