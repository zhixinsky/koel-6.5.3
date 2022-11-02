<template>
  <div tabindex="0" @keydown.esc="maybeClose">
    <SoundBars v-if="loading"/>
    <form v-else @submit.prevent="submit">
      <header>
        <h1>新播放列表文件夹r</h1>
      </header>

      <main>
        <div class="form-row">
          <input
            v-model="name"
            v-koel-focus
            name="name"
            placeholder="Folder name"
            required
            type="text"
          >
        </div>
      </main>

      <footer>
        <Btn type="submit">保存</Btn>
        <Btn white @click.prevent="maybeClose">取消</Btn>
      </footer>
    </form>
  </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import { playlistFolderStore } from '@/stores'
import { logger, requireInjection } from '@/utils'
import { DialogBoxKey, MessageToasterKey } from '@/symbols'

import SoundBars from '@/components/ui/SoundBars.vue'
import Btn from '@/components/ui/Btn.vue'

const toaster = requireInjection(MessageToasterKey)
const dialog = requireInjection(DialogBoxKey)

const loading = ref(false)
const name = ref('')

const emit = defineEmits(['close'])

const close = () => emit('close')

const submit = async () => {
  loading.value = true

  try {
    const folder = await playlistFolderStore.store(name.value)
    close()
    toaster.value.success(` 创建成功 `)
  } catch (error) {
    dialog.value.error('出问题了,请再试一次。')
    logger.error(error)
  } finally {
    loading.value = false
  }
}

const maybeClose = async () => {
  if (name.value.trim() === '') {
    close()
    return
  }

  await dialog.value.confirm('放弃所有更改？') && 关闭()
}
</script>
