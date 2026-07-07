<script setup>
import { Link, router } from '@inertiajs/vue3';

defineProps({
    columns: Array,
    rows: Array,
    actions: {
        type: Array,
        default: () => [], // list of actions to show
    }
});

// confirmation handler
function handleAction(row, action) {
    if (action.label.toLowerCase() === 'delete') {
        const confirmed = window.confirm('Are you sure you want to delete this item?');
        if (!confirmed) return;

        const routePath =
            typeof action.route === 'function'
                ? action.route(row)
                : route(`${action.route}`, { id: row.id });

        router.visit(routePath, {
            method: action.method || 'delete',
        });
    }
}
</script>

<template>
    <tbody>
        <tr v-for="row in rows" :key="row.id">
            <td v-for="col in columns" :key="col.key">{{ row[col.key] }}</td>

            <td>
                <template v-for="action in actions" :key="action.label">
                    <span style="padding: 0px 5px 0px 5px;">
                        <!-- Conditional rendering for delete with confirmation -->
                        <Link
                            v-if="action.label.toLowerCase() !== 'delete'"
                            :href="typeof action.route === 'function'
                                ? action.route(row)
                                : route(`${action.route}`, { id: row.id })"
                            :method="action.method"
                            :class="['btn', action.class]"
                            :title="action.label"
                        >
                            <i :class="action.icon"></i>
                        </Link>

                        <button
                            v-else
                            @click="handleAction(row, action)"
                            type="button"
                            :class="['btn', action.class]"
                            :title="action.label"
                        >
                            <i :class="action.icon"></i>
                        </button>
                    </span>
                </template>
            </td>
        </tr>
    </tbody>
</template>
