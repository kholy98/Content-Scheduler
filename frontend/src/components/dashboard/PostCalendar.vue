<!-- <script setup>
import { ref } from 'vue'
import VueCal from 'vue-cal'
import 'vue-cal/dist/vuecal.css'

const props = defineProps({
  events: Array,
})

const emit = defineEmits(['date-selected'])

// Handle clicks on day cells
function onDateClick({ date }) {
  emit('date-selected', date)
}


function onViewChange(view) {
  if (view.startDate) {
    emit('date-selected', new Date(view.startDate))
  }
}
</script>

<template>
  <vue-cal
    style="height: 500px"
    default-view="month"
    :events="props.events.map(event => ({
      start: event.date,
      end: event.date,
      title: event.title,
    }))"
    @cell-click="onDateClick"
    @view-change="onViewChange"
  />
</template> -->


<script setup>
import { ref } from 'vue'
import VueCal from 'vue-cal'
import 'vue-cal/dist/vuecal.css'

const props = defineProps({
  events: Array,
})

const emit = defineEmits(['date-selected'])

function onDateClick({ date }) {
  emit('date-selected', date)
}
</script>

<template>
  <vue-cal
    style="height: 500px"
    @cell-click="onDateClick"
    :events="props.events.map(event => ({
      start: event.date,
      end: event.date,
      title: event.title,
    }))"
    default-view="month"
  />
</template>


