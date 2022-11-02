<template>
  <div @keydown.esc="maybeClose">
    <SoundBars v-if="loading"/>
    <form v-else data-testid="edit-playlist-form" @submit.prevent="submit">
      <header>
        <h1>重命名播放列表</h1>
      </header>

      <main>
        <div class="form-row">
          <input
            v-model="name"
            v-koel-focus
            name="name"
            placeholder="Playlist name"
            required
            title="Playlist name"
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
import { logger, requireInjection } from '@/utils'
import { playlistStore } from '@/stores'
import { DialogBoxKey, MessageToasterKey, PlaylistKey } from '@/symbols'

import Btn from '@/components/ui/Btn.vue'
import SoundBars from '@/components/ui/SoundBars.vue'

const toaster = requireInjection(MessageToasterKey)
const dialog = requireInjection(DialogBoxKey)
const [playlist, updatePlaylistName] = requireInjection(PlaylistKey)

const name = ref(playlist.value.name)
const loading = ref(false)

const submit = async () => {
  loading.value = true

  try {
    await playlistStore.update(playlist.value, { name: name.value })
    updatePlaylistName(name.value)
    toaster.value.success('重命名成功')
    close()
  } catch (error) {
    dialog.value.error('出问题了,请再试一次。')
    logger.error(error)
  } finally {
    loading.value = false
  }
}

const emit = defineEmits(['close'])

const close = () => emit('close')

const maybeClose = async () => {
  if (name.value.trim() === playlist.value.name) {
    close()
    return
  }

  await dialog.value.confirm('放弃所有更改?') && 关闭()
}
</script>
