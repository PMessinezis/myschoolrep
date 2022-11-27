require('./bootstrap')
import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()
import { livewire_hot_reload } from 'virtual:livewire-hot-reload'
livewire_hot_reload();
