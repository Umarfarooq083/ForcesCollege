<!-- resources/js/Components/TableHeader.vue -->
<script setup>
defineProps({
  columns: Array, // e.g., [{ label: 'ID', key: 'id' }, { label: 'Name', key: 'name' }]
});
</script>

<template>
  <thead>
    <tr>
      <th v-for="col in columns" :key="col.key">{{ col.label }}</th>
      <!-- <th>Action</th> -->
    </tr>
  </thead>
</template>
