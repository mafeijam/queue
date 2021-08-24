import Echo from 'laravel-echo'
import 'pusher-js'

let connected = false

export const useEcho = key => {
  if (connected === false) {
    connected = new Echo({
      broadcaster: 'pusher',
      key: key,
      wsHost: 'mini.jamwong.me',
      encrypted: false,
      forceTLS: true,
      disableStats: true
    })
  }

  return connected
}
