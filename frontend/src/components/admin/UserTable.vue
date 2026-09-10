<script setup>
import { useRouter } from 'vue-router'
import { useAdminUsersStore } from '../../stores/adminUsers'

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
})

const emit = defineEmits([
    'refresh',
])

const router = useRouter()
const store = useAdminUsersStore()

const goDetails = (id) => {
    router.push({
        name: 'admin-user-details',
        params: {
            id,
        },
    })
}

const changeRole = async (
    id,
    event
) => {
    const role = event.target.value

    if (!role) {
        return
    }

    const result =
        await store.updateRole(
            id,
            role
        )

    if (result?.success) {
        emit('refresh')
    }

    // Reset select after operation.
    event.target.value = ''
}

const deleteUser = async (id) => {
    const confirmDelete = confirm(
        'آیا مطمئن هستید که می‌خواهید این کاربر را حذف کنید؟'
    )

    if (!confirmDelete) {
        return
    }

    const result =
        await store.deleteUser(id)

    if (result?.success) {
        emit('refresh')
    }
}

const formatDate = (value) => {
    if (!value) {
        return '-'
    }

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return '-'
    }

    return new Intl.DateTimeFormat(
        'fa-IR'
    ).format(date)
}

const getRoleLabel = (role) => {
    if (role === 'admin') {
        return 'ادمین'
    }

    if (role === 'user') {
        return 'کاربر'
    }

    return role || '-'
}
</script>

<template>
    <div
        class="table-wrapper"
        role="region"
        aria-label="جدول کاربران"
        tabindex="0"
    >
        <table>
            <caption class="sr-only">
                فهرست کاربران سیستم
            </caption>

            <thead>
                <tr>
                    <th scope="col">
                        نام
                    </th>

                    <th scope="col">
                        ایمیل
                    </th>

                    <th scope="col">
                        تلفن
                    </th>

                    <th scope="col">
                        نقش
                    </th>

                    <th scope="col">
                        تاریخ ثبت
                    </th>

                    <th scope="col">
                        عملیات
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr
                    v-for="user in props.users"
                    :key="user.id"
                >
                    <td>
                        <strong class="user-name">
                            {{ user.name || '-' }}
                        </strong>
                    </td>

                    <td>
                        <span
                            class="ltr"
                            dir="ltr"
                        >
                            {{ user.email || '-' }}
                        </span>
                    </td>

                    <td>
                        <span
                            v-if="user.phone"
                            class="ltr"
                            dir="ltr"
                        >
                            {{ user.phone }}
                        </span>

                        <span v-else>
                            -
                        </span>
                    </td>

                    <td>
                        <div
                            v-if="
                                user.roles?.length
                            "
                            class="roles"
                        >
                            <span
                                v-for="
                                    role in user.roles
                                "
                                :key="role.id"
                            >
                                {{
                                    getRoleLabel(
                                        role.name
                                    )
                                }}
                            </span>
                        </div>

                        <span
                            v-else
                            class="no-role"
                        >
                            بدون نقش
                        </span>

                        <select
                            :aria-label="`تغییر نقش ${user.name || 'کاربر'}`"
                            @change="
                                changeRole(
                                    user.id,
                                    $event
                                )
                            "
                        >
                            <option value="">
                                تغییر نقش
                            </option>

                            <option value="admin">
                                ادمین
                            </option>

                            <option value="user">
                                کاربر
                            </option>
                        </select>
                    </td>

                    <td>
                        <time
                            :datetime="
                                user.created_at ||
                                undefined
                            "
                        >
                            {{
                                formatDate(
                                    user.created_at
                                )
                            }}
                        </time>
                    </td>

                    <td>
                        <div class="actions">
                            <button
                                type="button"
                                class="action-btn details-btn"
                                :aria-label="`مشاهده جزئیات ${user.name || 'کاربر'}`"
                                @click="
                                    goDetails(
                                        user.id
                                    )
                                "
                            >
                                جزئیات
                            </button>

                            <button
                                type="button"
                                class="action-btn delete-btn"
                                :aria-label="`حذف ${user.name || 'کاربر'}`"
                                @click="
                                    deleteUser(
                                        user.id
                                    )
                                "
                            >
                                حذف
                            </button>
                        </div>
                    </td>
                </tr>

                <tr
                    v-if="props.users.length === 0"
                >
                    <td
                        colspan="6"
                        class="empty"
                    >
                        کاربری وجود ندارد
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
.table-wrapper {
    width: 100%;

    overflow-x: auto;

    background: var(--bg-primary);

    border: 1px solid var(--border);

    border-radius: 12px;
}

.table-wrapper:focus-visible {
    outline: 2px solid var(--orange);

    outline-offset: 3px;
}

table {
    width: 100%;

    min-width: 850px;

    border-collapse: collapse;

    color: var(--text-primary);
}

th,
td {
    padding: 13px 15px;

    text-align: right;

    border-bottom: 1px solid var(--border);

    font-family: inherit;

    font-size: 11px;
}

th {
    color: var(--text-secondary);

    background: rgba(255, 255, 255, 0.02);

    font-size: 10px;

    font-weight: 800;

    white-space: nowrap;
}

tbody tr:last-child td {
    border-bottom: 0;
}

tbody tr {
    transition: background var(--transition);
}

tbody tr:hover {
    background: rgba(255, 255, 255, 0.015);
}

.user-name {
    color: var(--text-primary);

    font-size: 11px;

    font-weight: 700;
}

.ltr {
    direction: ltr;

    text-align: right;
}

.roles {
    display: flex;

    flex-wrap: wrap;

    gap: 5px;

    margin-bottom: 8px;
}

.roles span {
    padding: 4px 8px;

    color: var(--orange);

    border: 1px solid rgba(255, 107, 0, 0.15);

    border-radius: 6px;

    background: var(--orange-soft);

    font-size: 9px;

    font-weight: 700;
}

.no-role {
    display: block;

    margin-bottom: 8px;

    color: var(--text-muted);

    font-size: 10px;
}

select {
    min-height: 32px;

    padding: 5px 8px;

    color: var(--text-secondary);

    border: 1px solid var(--border);

    border-radius: 6px;

    outline: 0;

    background: var(--bg-primary);

    font-family: inherit;

    font-size: 10px;

    cursor: pointer;

    transition: border-color var(--transition);
}

select:hover {
    border-color: var(--border-orange);
}

select:focus-visible {
    border-color: var(--orange);

    outline: 2px solid rgba(255, 107, 0, 0.18);

    outline-offset: 2px;
}

.actions {
    display: flex;

    flex-wrap: wrap;

    gap: 6px;
}

.action-btn {
    min-height: 34px;

    padding: 7px 12px;

    border-radius: 7px;

    font-family: inherit;

    font-size: 10px;

    font-weight: 700;

    cursor: pointer;

    transition:
        color var(--transition),
        border-color var(--transition),
        background var(--transition);
}

.details-btn {
    color: #4da3ff;

    border: 1px solid rgba(77, 163, 255, 0.5);

    background: rgba(13, 31, 53, 0.7);
}

.details-btn:hover {
    color: #fff;

    border-color: #4da3ff;

    background: rgba(13, 31, 53, 1);
}

.delete-btn {
    color: var(--danger);

    border: 1px solid rgba(255, 85, 85, 0.5);

    background: rgba(42, 13, 13, 0.7);
}

.delete-btn:hover {
    color: #fff;

    border-color: var(--danger);

    background: rgba(90, 20, 20, 0.8);
}

.action-btn:focus-visible {
    outline: 2px solid var(--orange);

    outline-offset: 2px;
}

.empty {
    padding: 40px 20px;

    color: var(--text-muted);

    text-align: center;

    font-size: 11px;
}

.sr-only {
    position: absolute;

    width: 1px;
    height: 1px;

    padding: 0;
    margin: -1px;

    overflow: hidden;

    clip: rect(0, 0, 0, 0);

    white-space: nowrap;

    border: 0;
}

@media (prefers-reduced-motion: reduce) {
    tbody tr,
    select,
    .action-btn {
        transition: none;
    }
}

@media (max-width: 900px) {
    .table-wrapper {
        border-radius: 10px;
    }
}
</style>
