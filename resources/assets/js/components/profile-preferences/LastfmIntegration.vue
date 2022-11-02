<template>
  <section class="text-secondary">
    <h1>Last.fm 集成</h1>

    <div v-if="useLastfm" data-testid="lastfm-integrated">
      <p>
        Koel已集成Last.fm 。
        <span v-if="connected">
          您似乎也连接了 Last.fm 帐户 – 完美！
        </span>
        <span v-else>不过，您似乎还没有连接到您的 Last.fm 帐户。</span>
      </p>
      <p>
        连接 Koel 和您的 Last.fm 帐户可实现以下令人兴奋的功能：
        <a
          class="text-highlight"
          href="https://www.last.fm/about/trackmymusic"
          rel="noopener"
          target="_blank"
        >乱码</a>.
      </p>
      <div class="buttons">
        <Btn class="connect" @click.prevent="connect">
          <icon :icon="faLastfm"/>
          {{ connected ? 'Reconnect' : 'Connect' }}
        </Btn>

        <Btn v-if="connected" class="disconnect" gray @click.prevent="disconnect">断开</Btn>
      </div>
    </div>

    <div v-else data-testid="lastfm-not-integrated">
      <p>
        Koel还没有集成Last.fm 。
        <span v-if="isAdmin" data-testid="lastfm-admin-instruction">
          访问
          <a href="https://docs.koel.dev/3rd-party.html#last-fm" class="text-highlight" target="_blank">Koel使用教程</a>
          快速获取操作方法
        </span>
        <span v-else data-testid="lastfm-user-instruction">
          试着请管理员启用它。
        </span>
      </p>
    </div>
  </section>
</template>

<script lang="ts" setup>
import { faLastfm } from '@fortawesome/free-brands-svg-icons'
import { computed, defineAsyncComponent } from 'vue'
import { authService, http } from '@/services'
import { forceReloadWindow } from '@/utils'
import { useAuthorization, useThirdPartyServices } from '@/composables'

const Btn = defineAsyncComponent(() => import('@/components/ui/Btn.vue'))

const { currentUser, isAdmin } = useAuthorization()
const { useLastfm } = useThirdPartyServices()

const connected = computed(() => Boolean(currentUser.value.preferences!.lastfm_session_key))

/**
 * Connect the current user to Last.fm.
 * This method opens a new window.
 * Koel will reload once the connection is successful.
 */
const connect = () => window.open(
  `${window.BASE_URL}lastfm/connect?api_token=${authService.getToken()}`,
  '_blank',
  'toolbar=no,titlebar=no,location=no,width=1024,height=640'
)

const disconnect = async () => {
  await http.delete('lastfm/disconnect')
  forceReloadWindow()
}
</script>

<style lang="scss" scoped>
.buttons {
  margin-top: 1.25rem;

  > * + * {
    margin-left: 0.5rem;
  }

  .connect {
    background: #d31f27; // Last.fm color yo!
  }
}
</style>
