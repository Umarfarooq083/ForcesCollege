<script setup>
import { computed } from 'vue'
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue'
import { openGroup } from '@/sidebarState' 

const props = defineProps({
  label: String,
  icon: String,
  items: Array,
})

const isRoute = (routeName) => {
  if (Array.isArray(routeName)) {
    return routeName.some(name => route().current(name))
  }
  return route().current(routeName)
}

const isActive = computed(() =>
  props.items.some((item) => isRoute(item.route))
)

const isOpen = computed(() =>
  openGroup.value === props.label || isActive.value
)

const toggle = () => {
  openGroup.value = isOpen.value ? null : props.label
}
</script>

<template>
  <li :class="{ active: isActive || isOpen }">
    <a href="#" class="has-arrow" @click.prevent="toggle">
      <i :class="icon"></i>
      <span>{{ label }}</span>
    </a>
    <ul :class="['collapse', isOpen ? 'in' : '']">
      <li v-for="item in items" :key="item.label" :class="{ active: isRoute(item.route) }">
        <ResponsiveNavLink :href="route(Array.isArray(item.route) ? item.route[0] : item.route)">
          <i :class="item.icon"></i>
          <span>{{ item.label }}</span>
        </ResponsiveNavLink>
      </li>
    </ul>
  </li>
</template>
