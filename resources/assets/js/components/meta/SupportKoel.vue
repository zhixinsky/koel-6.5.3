<template>
  <div v-if="shown" class="support-bar" data-testid="support-bar">
    <p>
      喜欢Koel中文版么? 有任何建议请联系
      <a href="mailto:admin@dokor.cn" rel="noopener" target="_blank">E-mail:admin@dokor.cn</a>
      and/or
      <a href="https://home.dokor.cn" rel="noopener" target="_blank">QQ:30613092</a>.
    </p>
    <button data-testid="hide-support-koel" type="button" @click.prevent="close">隐藏</button>
    <span class="sep"></span>
    <button data-testid="stop-support-koel-bugging" type="button" @click.prevent="stopBugging">
      不再出现该提示
    </button>
  </div>
</template>

<script lang="ts" setup>
import isMobile from 'ismobilejs'
import { computed, ref, toRef } from 'vue'
import { eventBus } from '@/utils'
import { preferenceStore } from '@/stores'

const delayUntilShow = 30 * 60 * 1000
let timeoutHandle = 0

const shown = ref(false)
const noBugging = toRef(preferenceStore.state, 'supportBarNoBugging')

const canNag = computed(() => !isMobile.any && !noBugging.value)

const setUpShowBarTimeout = () => (timeoutHandle = window.setTimeout(() => (shown.value = true), delayUntilShow))

const close = () => {
  shown.value = false
  window.clearTimeout(timeoutHandle)
}

const stopBugging = () => {
  preferenceStore.set('supportBarNoBugging', true)
  close()
}

eventBus.on('KOEL_READY', () => canNag.value && setUpShowBarTimeout())
</script>

<style lang="scss" scoped>
.support-bar {
  background: var(--color-bg-primary);
  font-size: .9rem;
  padding: .75rem 1rem;
  display: flex;
  color: rgba(255, 255, 255, .6);
  z-index: 9;

  > * + * {
    margin-left: 1rem;
  }

  p {
    flex: 1;
  }

  a {
    color: var(--color-text-primary);

    &:hover {
      color: var(--color-highlight);
    }
  }

  .sep {
    display: block;

    &::after {
      content: '•';
      display: block;
    }
  }

  button {
    color: var(--color-text-primary);
    font-size: .9rem;

    &:hover {
      color: var(--color-highlight);
    }
  }
}
</style>
